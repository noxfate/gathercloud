<?php
/**
 * Created by PhpStorm.
 * User: Arraylist
 * Date: 02-Dec-15
 * Time: 6:27 PM
 */

namespace App\Library;

require_once __DIR__ . '../../../vendor/autoload.php';
use AdammBalogh\Box\Command\Content;
use AdammBalogh\Box\Factory\ResponseFactory;
use AdammBalogh\Box\GuzzleHttp\Message\SuccessResponse;
use AdammBalogh\Box\GuzzleHttp\Message\ErrorResponse;
use AdammBalogh\Box\ContentClient;
use AdammBalogh\Box\Client\Content\ApiClient;
use AdammBalogh\Box\Client\Content\UploadClient;
use AdammBalogh\Box\Request\ExtendedRequest;
use AdammBalogh\Box\Client\OAuthClient;
use AdammBalogh\KeyValueStore\KeyValueStore;
use AdammBalogh\KeyValueStore\Adapter\MemoryAdapter;
use AdammBalogh\Box\Exception\ExitException;
use AdammBalogh\Box\Exception\OAuthException;
use App\Token;
use GuzzleHttp\Exception\ClientException;

class BoxInterface implements ModelInterface
{
    private $access_token;
    private $refresh_token;
    private $expired_in;
    private $clientId = 'ovfbhzo5niff7zog2joq53pkocb544uc';
    private $clientSecret = '4Nw8sSNI2OQediWzn3VgyZeqYzqNKbur';
    private $redirectUri = 'http://localhost/gathercloud/public/add/box';

    function __construct($token = null)
    {
        if ($token != null) {
            $this->access_token = $token->access_token;
            $this->refresh_token = $token->refresh_token;
            $this->expired_in = $token->expired_in;
            if($this->getAccessTokenStatus() != 1){
                $keyValueStore = new KeyValueStore(new MemoryAdapter());
                $keyValueStore->set('access_token',$this->access_token);
                $keyValueStore->set('refresh_token',$this->refresh_token);
                $keyValueStore->expire('access_token', 0);
                $keyValueStore->expire('refresh_token', ($this->expired_in + (5184000-3600)) - time()); #  60 days

                $oAuthClient = new OAuthClient($keyValueStore, $this->clientId, $this->clientSecret, $this->redirectUri);
                $oAuthClient->authorize();
                $keyValueStore = $oAuthClient->getKvs();
                Token::where('access_token', $this->access_token)->where('refresh_token',$this->refresh_token)
                    ->update(array(
                        'access_token'  =>$keyValueStore->get('access_token'),
                        'refresh_token' =>$keyValueStore->get('refresh_token'),
                        'expired_in'    =>time() + $keyValueStore->getTtl('access_token')));
                $this->access_token = $keyValueStore->get('access_token');
                $this->refresh_token = $keyValueStore->get('refresh_token');
                $this->expired_in = time() + $keyValueStore->getTtl('access_token');
            }

        } else {
            $keyValueStore = new KeyValueStore(new MemoryAdapter());
            $oAuthClient = new OAuthClient($keyValueStore, $this->clientId, $this->clientSecret, $this->redirectUri);
            try {
                $oAuthClient->authorize();
                $keyValueStore = $oAuthClient->getKvs();
                $this->access_token = $keyValueStore->get('access_token');
                $this->refresh_token = $keyValueStore->get('refresh_token');
                $this->expired_in = time() + $keyValueStore->getTtl('access_token');
            } catch (ExitException $e) {
                # Location header has set (box's authorize page)
                # Instead of an exit call it throws an ExitException
                exit;
            } catch (OAuthException $e) {
                # e.g. Invalid user credentials
                # e.g. The user denied access to your application
            } catch (ClientException $e) {
                # e.g. if $_GET['code'] is older than 30 sec
            }
        }

    }

    public function downloadFile($file, $destination = null)
    {
        if ($file != null){
            $list_file = explode("/", $file);
            $file = end($list_file);
        }
        $contentClient = new ContentClient(new ApiClient($this->access_token), new UploadClient($this->access_token));
        $er = new ExtendedRequest();
        if($destination != 'temp'){
            $file = substr($file, 5);
            $command = new Content\File\DownloadFile($file, $er);
            $response = ResponseFactory::getResponse($contentClient, $command);
//        dd($response->getHeaders()["Location"][0]);
            if ($response instanceof SuccessResponse) {
//            echo (string)$response->getStatusCode();
//            echo "<br>";
//            echo (string)$response->getReasonPhrase();
//            echo "<br>";
                header("Location: " . $response->getHeaders()["Location"][0]);
                die();
            } elseif ($response instanceof ErrorResponse) {
                # ...
            }
        }else{
            $info = $this->getFiles($file);
            $file = substr($file, 5);
            $fh = @fopen($destination . '/' . $info->name, 'wb'); // write binary
            if($fh === false) {
                @fclose($fh);
                throw new DropboxException("Could not create file" . $destination .'/' . $info->name . "!");
            }
            $er->setHeader(CURLOPT_FILE, $fh);
            $er->setHeader(CURLOPT_BINARYTRANSFER , true);
            $er->setHeader(CURLOPT_FOLLOWLOCATION, true);
            $command = new Content\File\DownloadFile($file, $er);
            $response = ResponseFactory::getResponse($contentClient, $command);
            $ch = curl_init();
            curl_setopt_array($ch, array(
                // SSL options.
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_BINARYTRANSFER => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_FILE => $fh,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_URL        => $response->getHeaders()["Location"][0],
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/download',
                ),
            ));
            $res = curl_exec($ch);
            curl_close($ch);
            fclose($fh);
            return $destination.'/'.$info->name;
        }

    }

    public function uploadFile($file, $destination = null)
    {
        if ($destination != null){
            $list_destination = explode("/", $destination);
            $destination = end($list_destination);
            $destination = substr($destination, 7);
        }
        if (null == $destination) {
            $destination = '0';
        }

        $parentId = $destination;
        $contentClient = new ContentClient(new ApiClient($this->access_token), new UploadClient($this->access_token));
        if(is_array($file)){
            $name = $file['name'];
            $content = file_get_contents($file['tmp_name']);
            $command = new Content\File\UploadFile($name, $parentId, $content);
        } else {
            $command = new Content\Folder\CreateFolder($file, $parentId);
        }

        $response = ResponseFactory::getResponse($contentClient, $command);
        if ($response instanceof SuccessResponse) {
            $response->getStatusCode();
            $response->getReasonPhrase();
            $response->getHeaders();
            $data = (string)$response->getBody();
            $entity = json_decode($data);

            if(is_array($file)){
                $entity = $entity->entries[0];
            }

            $format = array();
            $sh = ($entity->shared_link == null) ? null : $entity->shared_link->url;
            $mime = ($entity->type == 'folder') ? 'folder' : substr($entity->name,strpos($entity->name,'.')+1);
            array_push($format,
                array(
                    'name' => $entity->name,
                    'path' => ($entity->id == "0") ? null : ($entity->type == "folder") ? "folder." . $entity->id : "file." . $entity->id,
                    'size' => $entity->size,
                    'mime_type' => $mime,
                    'is_dir' => ($entity->type == "folder") ? true : false, // 1 == Folder, 0 = File
                    'modified' => $entity->modified_at,
                    'shared' => $sh
                ));
            return $format;
        } elseif ($response instanceof ErrorResponse) {
            return false;
        }

    }

    public function getFiles($file = null)
    {
        $full_path = "/";
        if ($file != null){
            $full_path = $file . $full_path;
            $list_file = explode("/", $file);
            $file = end($list_file);
        }
        $contentClient = new ContentClient(new ApiClient($this->access_token), new UploadClient($this->access_token));
        if (null === $file || $file == "") {
            $id = '0';
            $command = new Content\Folder\GetFolderInfo($id);
        } elseif (strpos($file, 'folder') !== false) {
            $id = substr($file, 7);
            $command = new Content\Folder\GetFolderInfo($id);
        } elseif (strpos($file, 'file') !== false) {
            $id = substr($file, 5);
            $command = new Content\File\GetFileInfo($id);
        } else {
            echo "LOL";
        }
        $response = ResponseFactory::getResponse($contentClient, $command);
        if ($response instanceof SuccessResponse) {
            $response->getStatusCode();
            $response->getReasonPhrase();
            $response->getHeaders();
            $data = (string)$response->getBody();
            $manage = json_decode($data);

            if (strpos($file, 'file') !== false) {
                return $manage;
            }

            $format = array();
            if ($manage->type == 'folder' && $manage->item_collection->total_count != 0) {
                for ($i = 0; $i < $manage->item_collection->total_count; $i++) {
                    $entity = $this->getEntity($manage->item_collection->entries[$i]->type . "." . $manage->item_collection->entries[$i]->id);
                    $sh = ($entity->shared_link == null) ? null : $entity->shared_link->url;
                    $mime = ($entity->type == 'folder') ? 'folder' : substr($entity->name,strpos($entity->name,'.')+1);
                    array_push($format,
                        array(
                            'name' => $entity->name,
                            'path' => $full_path . (($entity->id == "0") ? null : ($entity->type == "folder") ? "folder." . $entity->id : "file." . $entity->id),
                            'size' => $entity->size,
                            'mime_type' => $mime,
                            'is_dir' => ($entity->type == "folder") ? true : false, // 1 == Folder, 0 = File
                            'modified' => $entity->modified_at,
                            'shared' => $sh
                        ));
                }
            }
            return $format;
        } elseif ($response instanceof ErrorResponse) {
            return false;
        }

    }

    public function deleteFile($file)
    {
        $contentClient = new ContentClient(new ApiClient($this->access_token), new UploadClient($this->access_token));
        if ($file != null){
            $list_file = explode("/", $file);
            $file = end($list_file);
        }
        if (strpos($file, 'folder') !== false) {
            $file = substr($file, 7);
            $er = new ExtendedRequest();
            $er->addQueryField('recursive', true);
            $command = new Content\Folder\DeleteFolder($file,$er);
            $response = ResponseFactory::getResponse($contentClient, $command);
            if ($response instanceof SuccessResponse) {
//            echo (string)$response->getStatusCode();
//            echo "<br>";
//            echo (string)$response->getReasonPhrase();
                return true;

            } elseif ($response instanceof ErrorResponse) {
                $data = (string)$response->getBody();
                return $data;
            }


        } elseif (strpos($file, 'file') !== false) {
            $file = substr($file, 5);

            $command = new Content\File\DeleteFile($file);
            $response = ResponseFactory::getResponse($contentClient, $command);
            if ($response instanceof SuccessResponse) {
//            echo (string)$response->getStatusCode();
//            echo "<br>";
//            echo (string)$response->getReasonPhrase();
                return true;

            } elseif ($response instanceof ErrorResponse) {
                return false;
            }

        } else {
            echo "LOL";
        }


    }

    public function getLink($file)
    {
        $contentClient = new ContentClient(new ApiClient($this->access_token), new UploadClient($this->access_token));
        if ($file != null){
            $list_file = explode("/", $file);
            $file = end($list_file);
        }
        if (strpos($file, 'folder') !== false) {
            $folderId = substr($file, 7);

            $er = new ExtendedRequest();
            $er->setPostBodyField('shared_link', ['access'=>'open']);

            $command = new Content\Folder\CreateSharedFolderLink($folderId, $er);
            $response = ResponseFactory::getResponse($contentClient, $command);

            if ($response instanceof SuccessResponse) {
//            echo (string)$response->getStatusCode();
//            echo "<br>";
//            echo (string)$response->getReasonPhrase();
                $res = $this->getEntity('folder.'.$folderId);
                return $res->shared_link->url;

            } elseif ($response instanceof ErrorResponse) {
                return NULL;
            }

        } elseif (strpos($file, 'file') !== false) {
            $fileId = substr($file, 5);
            $er = new ExtendedRequest();
            $er->setPostBodyField('shared_link', ['access'=>'open']);

            $command = new Content\File\CreateSharedFileLink($fileId, $er);
            $response = ResponseFactory::getResponse($contentClient, $command);
            if ($response instanceof SuccessResponse) {
//            echo (string)$response->getStatusCode();
//            echo "<br>";
//            echo (string)$response->getReasonPhrase();
                $res = $this->getEntity('file.'.$fileId);
                return $res->shared_link->url;

            } elseif ($response instanceof ErrorResponse) {
                return NULL;
            }

        } else {
            echo "LOL";
        }

    }

    public function getAccountInfo()
    {
        $contentClient = new ContentClient(new ApiClient($this->access_token), new UploadClient($this->access_token));
        $command = new Content\User\GetCurrentUser();
        $response = ResponseFactory::getResponse($contentClient, $command);

        if ($response instanceof SuccessResponse) {
            $response->getStatusCode();
            $response->getReasonPhrase();
            $response->getHeaders();
            $data = (string)$response->getBody();
            $info = json_decode($data);
            $nml_info = array(
                'email' => $info->login,
                'quota' => $info->space_amount,
                'used' => $info->space_used,
                'remain' => $info->space_amount - $info->space_used
            );
            return (object)$nml_info;
        } elseif ($response instanceof ErrorResponse) {
            # ...
        }

    }

    public function getEntity($file)
    {
        $contentClient = new ContentClient(new ApiClient($this->access_token), new UploadClient($this->access_token));
        if (null === $file) {
            $id = '0';
            $command = new Content\Folder\GetFolderInfo($id);
        } elseif (strpos($file, 'folder') !== false) {
            $id = substr($file, 7);
            $command = new Content\Folder\GetFolderInfo($id);
        } elseif (strpos($file, 'file') !== false) {
            $id = substr($file, 5);
            $command = new Content\File\GetFileInfo($id);
        } else {
            echo "LOL";
        }
        $response = ResponseFactory::getResponse($contentClient, $command);
        if ($response instanceof SuccessResponse) {
            $response->getStatusCode();
            $response->getReasonPhrase();
            $response->getHeaders();
            $data = (string)$response->getBody();
            $entity = json_decode($data);

            return $entity;

        } elseif ($response instanceof ErrorResponse) {
            echo $id;
            echo (string)$response->getStatusCode();
            echo (string)$response->getReasonPhrase();
        }
    }

    public function rename($file, $new_name)
    {
        if ($file != null){
            $list_file = explode("/", $file);
            $file = end($list_file);
        }
        $contentClient = new ContentClient(new ApiClient($this->access_token), new UploadClient($this->access_token));
        $er = new ExtendedRequest();
        $er->setPostBodyField('name', $new_name);

        if (strpos($file, 'folder') !== false) {
            $file = substr($file, 7);
            $command = new Content\Folder\UpdateFolderInfo($file, $er);
        } elseif (strpos($file, 'file') !== false) {
            $file = substr($file, 5);
            $command = new Content\File\UpdateFileInfo($file, $er);
        } else {
            echo "LOL";
        }

        $response = ResponseFactory::getResponse($contentClient, $command);

        if ($response instanceof SuccessResponse) {
            return true;
        } elseif ($response instanceof ErrorResponse) {
            return false;
        }
    }

    public function getPathName($file)
    {
        $path = "";
        $list_file = explode("/", $file);
        foreach($list_file as $f){
            $entity = $this->getEntity($f);
            $path = $path . $entity->name . "/";
        }
        return substr($path,0,-1);
    }

    public function searchFile($keyword)
    {
        $contentClient = new ContentClient(new ApiClient($this->access_token), new UploadClient($this->access_token));
        $command = new Content\Search\SearchContent(urlencode($keyword));
        $response = ResponseFactory::getResponse($contentClient, $command);
        if ($response instanceof SuccessResponse) {
            $response->getStatusCode();
            $response->getReasonPhrase();
            $response->getHeaders();
            $data = (string)$response->getBody();
            $entries = json_decode($data);
            $format = array();
            foreach($entries->entries as $entity){
                $entity = (object)$entity;
                array_push($format,
                    array(
                        'name' => $entity->name,
                        'path' => "" . (($entity->id == "0") ? null : ($entity->type == "folder") ? "folder." . $entity->id : "file." . $entity->id),
                        'size' => $entity->size,
                        'mime_type' => null,
                        'is_dir' => ($entity->type == "folder") ? 1 : 0, // 1 == Folder, 0 = File
                        'modified' => $entity->modified_at,
                        'shared' => false
                    ));
            }

            return $format;

        } elseif ($response instanceof ErrorResponse) {
            return false;
        }
    }

    /**
     * @return object
     * =>(string)access_token
     * =>(integer)expired_in
     * =>(string)refresh_token
     */
    public function getToken()
    {
        $tk = array(
            'access_token' => $this->access_token,
            'expired_in' => $this->expired_in,
            'refresh_token' => $this->refresh_token
        );
        return (object)$tk;
    }

    /**
     * @param output of method getFiles() $list_data
     * @param $provider_logo
     * @param $connection_name
     * @return list of array
     * =>(string)name
     * =>(string)path format '/example/example/example'
     * =>(integer)bytes
     * =>(string)mime_type
     * =>(boolean)is_dir
     * =>(string)modified format 'Y m d H:i:s'
     * =>(string)shared
     * =>(string)provider_logo
     * =>(string)connection_name
     */
    public function normalizeMetaData($list_data, $provider_logo, $connection_name)
    {
        $format = array();
        foreach ($list_data as $k => $val) {
            array_push($format,
                array(
                    'name' => $val['name'],
                    'path' => $val['path'],
                    'path_name' => $val['path'],
                    'bytes' => $val['size'],
                    'mime_type' => $val['mime_type'],
                    'is_dir' => $val['is_dir'],
                    'modified' => date('Y m d H:i:s', strtotime($val['modified'])),
                    'shared' => $val['shared'],
                    'provider_logo' => $provider_logo,
                    'connection_name' => $connection_name
                ));
        }
        return $format;
    }

    /**
     * Gets the access token expiration delay.
     *
     * @return (int) The token expiration delay, in seconds.
     */
    private function getTokenExpire() {
        return $this->expired_in - time();
    }

    /**
     * Gets the status of the current access token.
     *
     * @return (int) The status of the current access token:
     *          0 => no access token
     *         -1 => access token will expire soon (1 minute or less)
     *         -2 => access token is expired
     *          1 => access token is valid
     */
    private function getAccessTokenStatus() {
        if (null === $this->access_token) {
            return 0;
        }

        $remaining = $this->getTokenExpire();

        if (0 >= $remaining) {
            return -2;
        }

        if (60 >= $remaining) {
            return -1;
        }

        return 1;
    }
}

?>