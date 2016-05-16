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
use Google_Service_Drive_ParentReference;
use Google_Service_Drive_DriveFile;
use App\Token;


class GoogleDriveInterface implements ModelInterface
{

    private $access_token;
    private $refresh_token;
    private $expired_in;
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
            $this->access_token = $token->access_token;
            $this->refresh_token = $token->refresh_token;
            $this->expired_in = $token->expired_in;
            if($this->getAccessTokenStatus() != 1){
                $this->client->refreshToken($this->refresh_token);
                $token = $this->client->getAccessToken();
                $token = (array)json_decode($token);
                $this->expired_in = time() + $token['expires_in'];
                Token::where('access_token', $this->access_token)->where('refresh_token',$this->refresh_token)
                    ->update(array(
                        'access_token'  =>$token['access_token'],
                        'expired_in'    =>$this->expired_in));
                $this->access_token = $token['access_token'];
            }else{
                $this->client->setAccessToken(json_encode($token));
            }
            $this->drive_service = new Google_Service_Drive($this->client);
        } else {
            if (isset($_GET['code'])) {
                $this->client->authenticate($_GET['code']);
                $token = $this->client->getAccessToken();
                $token = (array)json_decode($token);
                $this->access_token = $token['access_token'];
                $this->refresh_token = $token['refresh_token'];
                $this->expired_in = time() + $token['expires_in'];
                $this->drive_service = new Google_Service_Drive($this->client);
            }
            else{
                $authUrl = $this->client->createAuthUrl();
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
        $info = $this->drive_service->about->get();
        $nml_info = array(
            'email' => $info->getUser()->getEmailAddress(),
            'quota' => $info->getQuotaBytesTotal(),
            'used' => $info->getQuotaBytesUsed(),
            'remain' => $info->getQuotaBytesTotal() - $info->getQuotaBytesUsed()
        );
        return (object)$nml_info;
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
     * @param string $file
     * @return list of file metadata (return value depends on Provider)
     */
    public function getFiles($file)
    {
        return $this->drive_service->files->listFiles(array())->getItems();
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

        if(is_array($file)){
            $dfile = new Google_Service_Drive_DriveFile();
            $dfile->setTitle($file['name']);
            $dfile->setMimeType($file['type']);

            // Set the parent folder.
            if ($destination != null) {
                $parent = new Google_Service_Drive_ParentReference();
                $parent->setId($destination);
                $dfile->setParents(array($parent));
            }

            try {
                $data = file_get_contents($file['temp_name']);

                $createdFile = $this->drive_service->files->insert($dfile, array(
                    'data' => $data,
                    'mimeType' => $file['type'],
                ));

                // Uncomment the following line to print the File ID
                // print 'File ID: %s' % $createdFile->getId();

                return array($createdFile);
            } catch (Exception $e) {
                print "An error occurred: " . $e->getMessage();
            }
        } else{


            try {
                $dfile = new Google_Service_Drive_DriveFile();
                $dfile->setTitle($file);
                $dfile->setMimeType('application/vnd.google-apps.folder');

                // Set the parent folder.
                if ($destination != null) {
                    $parent = new Google_Service_Drive_ParentReference();
                    $parent->setId($destination);
                    $dfile->setParents(array($parent));
                }
                $createdFile = $this->drive_service->files->insert($dfile);

                // Uncomment the following line to print the File ID
                // print 'File ID: %s' % $createdFile->getId();

                return array($createdFile);
            } catch (Exception $e) {
                print "An error occurred: " . $e->getMessage();
            }

        }
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
        $res = $this->drive_service->files->listFiles(array(
            'q' => "title contains '$keyword'"
        ));
        return $res->getItems();
//        foreach($res->getItems() as $f){
//            dump($f);
//            dump($f->getTitle());
//
//            dump($f->getFileSize());
//            dump($f->getMimeType());
//            dump($f->getFileExtension());
//            dump($f->getModifiedDate());
//        }
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
        $format = array();
        foreach ($list_data as $f) {
            $is = ($f->getFileExtension() == null) ? true : false;
            array_push($format,
                array(
                    'name' => $f->getTitle(),
                    'path' => $f->getId(),
                    'bytes' => $f->getFileSize(),
                    'mime_type' => $f->getMimeType(),
                    'is_dir' => $is,
                    'modified' => date('Y m d H:i:s', strtotime($f->getModifiedDate())),
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