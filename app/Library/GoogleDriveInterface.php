<?php
/**
 * Created by PhpStorm.
 * User: Arraylist
 * Date: 18-Apr-16
 * Time: 1:22 AM
 */

namespace App\Library;

require_once __DIR__ . '../../../vendor/autoload.php';

use Google_Exception;
use Google_Service_Drive_Permission;
use Google_Http_Request;
use Google_Client;
use Google_Service_Drive;
use Google_Service_Drive_ParentReference;
use Google_Service_Drive_DriveFile;
use App\Token;
use Mockery\CountValidator\Exception;


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
        if ($file != null){
            $list_file = explode("/", $file);
            $file = end($list_file);
        }
        $is_root = false;
        if($file == null || $file == ""){
            $is_root = true;
        }
        $res = $this->drive_service->files->listFiles(array())->getItems();
        $nfile = array();
        foreach($res as $item){
            $parents = $item->getParents();
            $parent = $parents[0];
            if($is_root){
                if($parent->getIsRoot()){
                    array_push($nfile,$item);
                }
            }else{
                if($parent->getId() == $file){
                    array_push($nfile,$item);
                }
            }
        }
//        dd($nfile);
//        return $res;
        return $nfile;

    }

    /**
     * @param string $file
     * @param string $destination
     * @return string of Location
     */
    public function downloadFile($file, $destination = null)
    {
        if ($file != null){
            $list_file = explode("/", $file);
            $file = end($list_file);
        }
        $res = $this->drive_service->files->get($file);

        if($destination == 'temp'){
            $downloadUrl = $res->getDownloadUrl();
            if ($downloadUrl) {
                $des = $destination.'/'.$res->getTitle();

                $request = new Google_Http_Request($downloadUrl, 'GET', null, null);
                $httpRequest = $this->drive_service->getClient()->getAuth()->authenticatedRequest($request);
                if ($httpRequest->getResponseHttpCode() == 200) {
                    $body = $httpRequest->getResponseBody();
                    file_put_contents($des,$body);
                    return $des;
                } else  {
                    // An error occurred.
                    dd($httpRequest);
                    return null;
                }
            } else {
                // The file doesn't have any content stored on Drive.
                dd("The file doesn't have any content stored on Drive.");
                return null;
            }

        }else {
            $downloadUrl = $res->getWebContentLink();
            if ($downloadUrl) {
                $request = new Google_Http_Request($downloadUrl, 'GET', null, null);
                $httpRequest = $this->drive_service->getClient()->getAuth()->authenticatedRequest($request);
                if($httpRequest->getResponseHttpCode() == 302){
                    $body = $httpRequest->getResponseBody();
                    $str_body = (string)$body;
//                dump($body);
//                echo $str_body;
//                echo strrpos($str_body,'<A HREF="')+9;
//                echo strpos($str_body,'">here');
//                echo strrpos($str_body,'">here') - (strrpos($str_body,'<A HREF="')+9);
                    $location = substr($str_body,strrpos($str_body,'<A HREF="')+9,strrpos($str_body,'">here') - (strrpos($str_body,'<A HREF="')+9));
                    header("Location: " . $location);
                    die();
                    return true;
                } else  {
                    // An error occurred.
                    dd($httpRequest);
                    return null;
                }
            }
        }
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
            $new_file = new Google_Service_Drive_DriveFile();
            $new_file->setTitle($file['name']);
            $new_file->setMimeType($file['type']);

            // Set the parent folder.
            $parent = new Google_Service_Drive_ParentReference();
            if ($destination != null && $destination != "") {
                $destination = explode("/", $destination);
                $destination = end($destination);
                $parent->setId($destination);
            }else{
                $info = $this->drive_service->about->get();
                $parent->setId($info->getRootFolderId());

            }
            $new_file->setParents(array($parent));

            try {
                $data = file_get_contents($file['tmp_name']);

                $createdFile = $this->drive_service->files->insert($new_file, array(
                    'data' => $data,
                    'mimeType' => $file['type'],
                    'uploadType' => 'media'
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
        if ($file != null){
            $list_file = explode("/", $file);
            $fileId = end($list_file);

            $newPermission = new Google_Service_Drive_Permission();
            $newPermission->setType('anyone');
            $newPermission->setRole('reader');
            $newPermission->setWithLink(true);
            try {
                $this->drive_service->permissions->insert($fileId, $newPermission);
                $file =  $this->drive_service->files->get($fileId);
                return $file->getAlternateLink();
            } catch (Google_Exception $e) {
                print "An error occurred: " . $e->getMessage();
            }
            return NULL;
        }
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
        if ($file != null){
            $list_file = explode("/", $file);
            $fileId = end($list_file);
        }
        try {
            $this->drive_service->files->delete($fileId);
        } catch (Exception $e) {
            print "An error occurred: " . $e->getMessage();
        }

        return true;
    }

    /**
     * @param string $file
     * @param string $new_name
     * @return boolean
     */
    public function rename($file, $new_name)
    {
        try {
            if ($file != null){
                $list_file = explode("/", $file);
                $fileId = end($list_file);
            }
            // First retrieve the file from the API.
            $file = $this->drive_service->files->get($fileId);

            // File's new metadata.
            $file->setTitle($new_name);

            // Send the request to the API.
            $updatedFile = $this->drive_service->files->update($fileId, $file);
            return true;
        } catch (Exception $e) {
            print "An error occurred: " . $e->getMessage();
        }
    }

    /**
     * @param string $file
     * @return string format 'example/example/example'
     */
    public function getPathName($file)
    {
        if ($file != null){
            $list_file = explode("/", $file);
        }
        $path_name = '';
        foreach($list_file as $f){
            $res = $this->drive_service->files->get($f);
            $path_name = $path_name . $res->getTitle() .'/';
        }

        return substr($path_name,0,-1);
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
            $parents = $f->getParents();
            $parent = $parents[0];
            $path_col = array(
                'path_name' => '',
                'path' => ''
            );
            if(!$parent->getIsRoot()){
                $parentId = $parent->getId();
                $path_col = $this->recusiveGetPath($parentId,$path_col);
            }
            $is = ($f->getMimeType() == 'application/vnd.google-apps.folder') ? true : false;
            array_push($format,
                array(
                    'name' => $f->getTitle(),
                    'path' => $path_col['path'] .'/' . $f->getId(),
                    'path_name' => $path_col['path_name'],
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

    private function recusiveGetPath($parentId,$path_col){
        $res = $this->drive_service->files->get($parentId);
        $parents = $res->getParents();
        if(empty($parents) || $parents[0]->getIsRoot()){
            $path_col['path_name'] = '/' . $res->getTitle() . $path_col['path_name'];
            $path_col['path'] = '/' . $res->getId() . $path_col['path'];
            return $path_col;
        }
        $path_col['path_name'] = '/' . $res->getTitle() . $path_col['path_name'];
        $path_col['path'] = '/' . $res->getId() . $path_col['path'];
        return $this->recusiveGetPath($parents[0]->getId(), $path_col);
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