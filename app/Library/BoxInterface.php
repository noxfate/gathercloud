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
use GuzzleHttp\Exception\ClientException;

class BoxInterface implements ModelInterface
{
    private $access_token;
    private $refresh_token;
    private $clientId = 'ovfbhzo5niff7zog2joq53pkocb544uc';
    private $clientSecret = '4Nw8sSNI2OQediWzn3VgyZeqYzqNKbur';
    private $redirectUri = 'http://localhost/gathercloud/public/add/box';

    function __construct($access_token = null)
    {
        if ($access_token != null) {
            $this->access_token = $access_token[0];
        } else {
            $keyValueStore = new KeyValueStore(new MemoryAdapter());
            $oAuthClient = new OAuthClient($keyValueStore, $this->clientId, $this->clientSecret, $this->redirectUri);
            try {
                $oAuthClient->authorize();
                $keyValueStore = $oAuthClient->getKvs();
                $this->access_token = $keyValueStore->get('access_token');
                $this->refresh_token = $keyValueStore->get('refresh_token');
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

    /**
     * @return mixed
     */
    public function getAccessToken()
    {
        return $this->access_token;
    }

    /**
     * @return mixed
     */
    public function getRefreshToken()
    {
        return $this->refresh_token;
    }


    public function downloadFile($file)
    {
        if ($file != null){
            $list_file = explode("/", $file);
            $file = end($list_file);
        }
        $file = substr($file, 5);
        $contentClient = new ContentClient(new ApiClient($this->access_token), new UploadClient($this->access_token));
        $er = new ExtendedRequest();
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

    }

    public function uploadFile($file, $destination = null)
    {
        if ($destination != null){
            $list_destination = explode("/", $destination);
            $destination = end($list_destination);
            $destination = substr($destination, 7);
        }

        $contentClient = new ContentClient(new ApiClient($this->access_token), new UploadClient($this->access_token));

        if (null === $destination) {
            $destination = '0';
        }
        dump($destination);
        $parentId = $destination;
        $name = $file['name'];
        $content = file_get_contents($file['tmp_name']);
        $command = new Content\File\UploadFile($name, $parentId, $content);
        $response = ResponseFactory::getResponse($contentClient, $command);
        if ($response instanceof SuccessResponse) {
            $response->getStatusCode();
            $response->getReasonPhrase();
            $response->getHeaders();
            $data = (string)$response->getBody();
            $manage = (array)json_decode($data);
            print_r($manage);
            return $manage;
        } elseif ($response instanceof ErrorResponse) {
            return $response;
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
            $manage = json_decode($data);
            $format = array();
            if ($manage->type == 'folder' && $manage->item_collection->total_count != 0) {
                for ($i = 0; $i < $manage->item_collection->total_count; $i++) {
                    $entity = $this->getEntity($manage->item_collection->entries[$i]->type . "." . $manage->item_collection->entries[$i]->id);
                    array_push($format,
                        array(
                            'name' => $entity->name,
                            'path' => $full_path . (($entity->id == "0") ? null : ($entity->type == "folder") ? "folder." . $entity->id : "file." . $entity->id),
                            'size' => $entity->size,
                            'mime_type' => null,
                            'is_dir' => ($entity->type == "folder") ? 1 : 0, // 1 == Folder, 0 = File
                            'modified' => $entity->modified_at,
                            'shared' => false
                        ));
                }
            }
            return $format;
        } elseif ($response instanceof ErrorResponse) {
            echo $id;
            echo (string)$response->getStatusCode();
            echo (string)$response->getReasonPhrase();
        }

    }

    public function deleteFile($file)
    {
        if ($file != null){
            $list_file = explode("/", $file);
            $file = end($list_file);
        }
        if (strpos($file, 'folder') !== false) {
            $file = substr($file, 7);
        } elseif (strpos($file, 'file') !== false) {
            $file = substr($file, 5);
        } else {
            echo "LOL";
        }
        $contentClient = new ContentClient(new ApiClient($this->access_token), new UploadClient($this->access_token));
        $er = new ExtendedRequest();
        $command = new Content\File\DeleteFile($file);
        $response = ResponseFactory::getResponse($contentClient, $command);
        if ($response instanceof SuccessResponse) {
//            echo (string)$response->getStatusCode();
//            echo "<br>";
//            echo (string)$response->getReasonPhrase();
            return (string)$response->getStatusCode();

        } elseif ($response instanceof ErrorResponse) {
            # ...
        }

    }

    public function getLink($file)
    {

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
            $manage = (array)json_decode($data);
            return $manage;
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
            return $response->getStatusCode();
        } elseif ($response instanceof ErrorResponse) {
            return $response->getStatusCode();
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
        $command = new Content\Search\SearchContent($keyword);
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
            return $response;
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
        // TODO: Implement getToken() method.
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
        // TODO: Implement normalizeMetaData() method.
    }
}

?>