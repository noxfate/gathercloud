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
use Google_Service_Urlshortener;


class GoogleInterface implements ModelInterface
{

    private $access_token;
    private $refresh_token;
    private $client_id = '1098750327371-fmvl4jm6qrsb7ii76vlrku5sii1ivijb.apps.googleusercontent.com';
    private $client_secret = 'aBQwOX3KYK7ujJzjLKXxgNNL';
    private $redirect_uri = 'http://localhost/gathercloud/public/add/googledrive';

    function __construct($access_token = null){
        if($access_token != null){
            $this->access_token = $access_token[0];
        } else {
            $client = new Google_Client();
            $client->setClientId($this->client_id);
            $client->setClientSecret($this->client_secret);
            $client->setRedirectUri($this->redirect_uri);
            $client->setApprovalPrompt('force');
            $client->setAccessType("offline");
            $client->addScope("https://www.googleapis.com/auth/drive");

            if (isset($_GET['code'])) {
                $client->authenticate($_GET['code']);
                $this->access_token = $client->getAccessToken();
                $this->access_token = (array)json_decode($this->access_token);
                dd($this->access_token);
                //$_SESSION['googledriveToken'] = substr($_SESSION['access_token'], strpos($_SESSION['access_token'], "access_token")+15, strlen($_SESSION['access_token']) - strpos($_SESSION['access_token'], "token_type") + 13);
//                $redirect = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
////                $redirect = 'http://localhost/gathercloud/public/add/googledrive';
//                header('Location: ' . filter_var($redirect, FILTER_SANITIZE_URL));
//                die();
            }
            else{
                $authUrl = $client->createAuthUrl();
//                dd($authUrl);
                header('Location: ' . $authUrl);
                die();
            }
//            dd($authUrl);

//            $service = new Google_Service_Urlshortener($client);
//
        }
    }

    public function downloadFile($file, $destination = null)
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
        // TODO: Implement getAccountInfo() method.
    }

    public function getAccessToken()
    {
        // TODO: Implement getAccessToken() method.
    }
}