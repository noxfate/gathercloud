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
use GuzzleHttp\Exception\ClientException;


class OneDriveInterface implements ModelInterface
{
    private $access_token;
    private $refresh_token;
    private $clientId = '000000004016694A';
    private $clientSecret = 'U30Ozap0Su7I8aDMpOmbC4M3oehKH1eN';
    private $redirectUri = 'http://localhost/gathercloud/public/add/onedrive';
    private $state;

    function __construct($access_token = null){
        if($access_token != null){
            $this->access_token = $access_token[0];
            $this->state = (object) array(
                'redirect_uri' => null,
                'token'        => null
            );

            $this->state->token = (object) array(
                'obtained' => null,
                'data'     => (object) array('access_token' => $access_token[0])
            );
        } else{
            $keyValueStore = new KeyValueStore(new MemoryAdapter());
            $oAuthClient = new OAuthClient($keyValueStore, $this->clientId, $this->clientSecret, $this->redirectUri);
            try {
                $decoded = $oAuthClient->authorize();
                $keyValueStore = $oAuthClient->getKvs();
                $this->access_token = $keyValueStore->get('access_token');
                $this->state = (object) array(
                    'redirect_uri' => null,
                    'token'        => null
                );
                $this->state->token = (object) array(
                    'obtained' => time(),
                    'data'     => $decoded
                );
//                $this->refresh_token = $keyValueStore->get('refresh_token');
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
//        return $this->access_token;
        return $this->state->token->data->access_token;
    }

    /**
     * @return mixed
     */
    public function getRefreshToken()
    {
        return $this->refresh_token;
    }

    public function downloadFile($file, $destination = null)
    {
        $onedrive = new \App\Library\OneDrive\Client(array(
            'state' => $this->state
        ));

        $objects = $onedrive->apiDownload($file);
        var_dump($objects);
        header("Location: " . $objects->location);
        die();
//        var_dump($objects->fetchProperties()->source);
//        header("Content-Type: application/download");
//        header("Location: " . $objects->fetchProperties()->source);
//        die();

    }

    public function uploadFile($file, $destination = null)
    {
        $onedrive = new \App\Library\OneDrive\Client(array(
            'state' => $this->state
        ));

        $parentId    = empty($destination) ? null : $destination;
        $name		 = $file['name'];
        $content 	 = file_get_contents($file['tmp_name']);
        $parent      = $onedrive->fetchObject($parentId);
        $file        = $parent->createFile($name, $content);
        return $file;
    }

    public function getFiles($file = null)
    {
        $onedrive = new \App\Library\OneDrive\Client(array(
            'state' => $this->state
        ));

        if (null === $file) {
            $root    = $onedrive->fetchRoot();
            $objects = $root->fetchObjects();
            return $objects;
        } else if (strpos($file,'folder') !== false) {
            $folder  = $onedrive->fetchObject($file);
            $objects = $folder->fetchObjects();
            return $objects;
        } else {
            $object = $onedrive->fetchObject($file);
            return $object;
        }
    }

    public function deleteFile($file)
    {
        $onedrive = new \App\Library\OneDrive\Client(array(
            'state' => $this->state
        ));

        $onedrive->deleteObject($file);
        return true;
    }

    public function getLink($file)
    {
        $onedrive = new \App\Library\OneDrive\Client(array(
            'state' => $this->state
        ));
    }

    public function getAccountInfo(){
        $onedrive = new \App\Library\OneDrive\Client(array(
            'state' => $this->state
        ));
        $objects = $onedrive->fetchAccountInfo();
        return $objects;

    }

    public function getFolder($file)
    {
        $onedrive = new \App\Library\OneDrive\Client(array(
            'state' => $this->state
        ));
        $folder  = $onedrive->fetchObject($file);
        return  $folder;

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
}