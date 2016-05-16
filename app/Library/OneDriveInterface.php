<?php
/**
 * Created by PhpStorm.
 * User: Arraylist
 * Date: 03-Dec-15
 * Time: 3:17 PM
 */

namespace App\Library;

require_once __DIR__ . '../../../vendor/autoload.php';

use AdammBalogh\Box\Command\Content;
use App\Library\OneDrive\OAuthClient;
use AdammBalogh\KeyValueStore\KeyValueStore;
use AdammBalogh\KeyValueStore\Adapter\MemoryAdapter;
use AdammBalogh\Box\Exception\ExitException;
use AdammBalogh\Box\Exception\OAuthException;
use App\Token;
use GuzzleHttp\Exception\ClientException;


class OneDriveInterface implements ModelInterface
{
    private $access_token;
    private $refresh_token;
    private $expired_in;
    private $clientId = '000000004016694A';
    private $clientSecret = 'U30Ozap0Su7I8aDMpOmbC4M3oehKH1eN';
    private $redirectUri = 'http://localhost/gathercloud/public/add/onedrive';
    private $state;

    function __construct($token = null){
        if($token != null){
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
        } else{
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

        $this->state = (object) array(
            'redirect_uri' => null,
            'token'        => null
        );

        $this->state->token = (object) array(
            'obtained' => null,
            'data'     => (object) array('access_token' => $this->access_token)
        );
    }


    public function downloadFile($file, $destination = null)
    {

        $onedrive = new \App\Library\OneDrive\Client(array(
            'state' => $this->state
        ));

        if($destination == 'temp'){
            $destination = $destination . $file;
            $res = $onedrive->apiDownloadIn(urlencode($file), $destination);
            return $destination;
        }else{
        $res = $onedrive->apiDownload(urlencode($file));
        header("Location: " . $res['Location']);
        die();
        }
//        var_dump($objects->fetchProperties()->source);
//        header("Content-Type: application/download");
//        header("Location: " . $objects->fetchProperties()->source);
//        die();

    }

    public function uploadFile($file, $destination = null)
    {
//        if ($destination != null){
//            $list_destination = explode("/", $destination);
//            $destination = end($list_destination);
//        }

        $onedrive = new \App\Library\OneDrive\Client(array(
            'state' => $this->state
        ));

//        $parentId    = empty($destination) ? null : $destination;
//        $parent      = $onedrive->fetchObject($parentId);

        if(is_array($file)){
            $name		 = $file['name'];
            $content 	 = file_get_contents($file['tmp_name']);
            $file        = $onedrive->createFile($name, $content);
            return array($file);
        } else {
            $name        = $file;
            $folder      = $onedrive->createFolder($name, $destination);
            return array($folder);
        }

    }

    public function getFiles($file = null)
    {
//        $full_path = "/";
//        if ($file != null){
//            $full_path = $file . $full_path;
//            $list_file = explode("/", $file);
//            $file = end($list_file);
//        }
        $onedrive = new \App\Library\OneDrive\Client(array(
            'state' => $this->state
        ));
        $object = $onedrive->fetchObjects(urlencode($file));

//        if (null === $file) {
//            $root    = $onedrive->fetchRoot();
//            $object = $root->fetchObjects();
//        } else if (strpos($file,'folder') !== false) {
//            $folder  = $onedrive->fetchObject($file);
//            $object = $folder->fetchObjects();
//        } else {
//            $object = $onedrive->fetchObject($file);
//        }

//        foreach($object as $val){
//            $val->setId($full_path);
//        }

        return $object;
    }

    public function deleteFile($file)
    {
        $onedrive = new \App\Library\OneDrive\Client(array(
            'state' => $this->state
        ));

        return $onedrive->deleteObject(urlencode($file));
    }

    public function getLink($file)
    {
        if ($file != null){
            $list_file = explode("/", $file);
            $file = end($list_file);
        }
        $onedrive = new \App\Library\OneDrive\Client(array(
            'state' => $this->state
        ));
    }

    public function getAccountInfo(){
        $onedrive = new \App\Library\OneDrive\Client(array(
            'state' => $this->state
        ));
        $info = $onedrive->fetchAccountInfo();
        $nml_info = array(
            'email' => $info->owner->user->displayName,
            'quota' => round($info->quota->total),
            'used' => round($info->quota->used),
            'remain' => round($info->quota->remaining)
        );
        return (object)$nml_info;

    }


    public function rename($file, $new_name)
    {
        $onedrive = new \App\Library\OneDrive\Client(array(
            'state' => $this->state
        ));

        $properties = array();
        $properties['name'] = $new_name;
        $onedrive->updateObject($file, $properties);
        return true;
    }

    public function getPathName($file)
    {
        return $file;

//        $entity = $this->getFolder($path);
//            while($entity->getParentId() != null){
//                $parent->pname[] = $entity->getName();
//                $parent->ppath[] = $entity->getId();
//                $entity = $obj->getFolder($entity->getParentId());
//            }
//            $parent->pname[] = $id;
//            $parent->ppath[] = '/' . $id;
//
//            $parent->pname = array_reverse($parent->pname);
//            $parent->ppath = array_reverse($parent->ppath);
    }

    public function searchFile($keyword)
    {
        $onedrive = new \App\Library\OneDrive\Client(array(
            'state' => $this->state
        ));
        $list_data = $onedrive->searchFile(urlencode($keyword));
        return $list_data;
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
        foreach ($list_data as $val) {
            $is = property_exists($val,'folder');
            $mime = ($is) ? 'folder' : substr($val->name,strpos($val->name,'.')+1);
            array_push($format,
                array(
                    'name' => basename($val->name),
                    'path' => substr($val->parentReference->path,strpos($val->parentReference->path,':') +1 ) . '/' . $val->name,
                    'bytes' => $val->size,
                    'mime_type' => $mime,
                    'is_dir' => $is,
                    'modified' => date('Y m d H:i:s', strtotime($val->lastModifiedDateTime)),
                    'shared' => false,
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