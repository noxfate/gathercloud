<?php
/**
 * Created by PhpStorm.
 * User: Arraylist
 * Date: 18-Apr-16
 * Time: 1:22 AM
 */

namespace App\Library;

require_once __DIR__ . '../../../vendor/autoload.php';

use Google_Client;
use Google_Service_Drive;


class GoogleDriveInterface implements ModelInterface
{

    private $access_token;
    private $refresh_token;
    private $client_id = '1098750327371-fmvl4jm6qrsb7ii76vlrku5sii1ivijb.apps.googleusercontent.com';
    private $client_secret = 'aBQwOX3KYK7ujJzjLKXxgNNL';
    private $redirect_uri = 'http://localhost/gathercloud/public/add/googledrive';
    private $client;
    private $drive_service;

    function __construct($token = null){

        $this->client = new Google_Client();
        $this->client->setClientId($this->client_id);
        $this->client->setClientSecret($this->client_secret);
        $this->client->setRedirectUri($this->redirect_uri);
        $this->client->setApprovalPrompt('force');
        $this->client->setAccessType("offline");
        $this->client->addScope("https://www.googleapis.com/auth/drive");

        if($token != null){
            $this->access_token = $token['access_token'];
            $this->refresh_token = $token['refresh_token'];
            $this->client->setAccessToken(json_encode($token));
            $this->drive_service = new Google_Service_Drive($this->client);
        } else {
            if (isset($_GET['code'])) {
                $this->client->authenticate($_GET['code']);
                $token = $this->client->getAccessToken();
                $token = (array)json_decode($token);
                $this->access_token = $token['access_token'];
                $this->refresh_token = $token['refresh_token'];
                $this->drive_service = new Google_Service_Drive($this->client);
            }
            else{
                $authUrl = $this->client->createAuthUrl();
//                dd($authUrl);
                header('Location: ' . $authUrl);
                die();
            }
        }
    }


    /**
     * @return object
     * =>(string)email
     * =>(integer)quota
     * =>(integer)used
     * =>(integer)remain
     */
    public function getAccountInfo()
    {
        $this->drive_service->about->get();
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
     * @param string $file
     * @return list of file metadata (return value depends on Provider)
     */
    public function getFiles($file)
    {
        $this->drive_service->files->listFiles(array())->getItems();
    }

    /**
     * @param string $file
     * @param string $destination
     * @return string of Location
     */
    public function downloadFile($file, $destination = null)
    {
        // TODO: Implement downloadFile() method.
    }

    /**
     * @param string $file
     * @param string $destination
     * @return list of file metadata (return value depends on Provider)
     *
     */
    public function uploadFile($file, $destination)
    {
        // TODO: Implement uploadFile() method.
    }

    /**
     * @param string $file
     * @return string of public share url
     */
    public function getLink($file)
    {
        // TODO: Implement getLink() method.
    }

    /**
     * @param string $keyword
     * @return list of file metadata (return value depends on Provider)
     */
    public function searchFile($keyword)
    {
        // TODO: Implement searchFile() method.
    }

    /**
     * @param string $file
     * @return boolean
     */
    public function deleteFile($file)
    {
        // TODO: Implement deleteFile() method.
    }

    /**
     * @param string $file
     * @param string $new_name
     * @return boolean
     */
    public function rename($file, $new_name)
    {
        // TODO: Implement rename() method.
    }

    /**
     * @param string $file
     * @return string format 'example/example/example'
     */
    public function getPathName($file)
    {
        // TODO: Implement getPathName() method.
    }

    /**
     * @param output from method getFiles() $list_data
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