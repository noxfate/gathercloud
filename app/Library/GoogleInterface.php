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


class GoogleInterface implements ModelInterface
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

    public function downloadFile($file)
    {
        // TODO: Implement downloadFile() method.
    }

    public function uploadFile($file, $destination = null)
    {
        // TODO: Implement uploadFile() method.
    }

    public function getFiles($file = null)
    {
        // TODO: Implement getFiles() method.
        return $this->drive_service->files->listFiles(array())->getItems();
    }

    public function deleteFile($file)
    {
        // TODO: Implement deleteFile() method.
    }

    public function getLink($file)
    {
        // TODO: Implement getLink() method.
    }

    public function getAccountInfo()
    {
        return $this->drive_service->about->get();
    }

    public function getAccessToken()
    {
        return $this->access_token;
    }
    public function getRefreshToken()
    {
        return $this->refresh_token;
    }
}