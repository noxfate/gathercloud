<?php

namespace App\Library;

require __DIR__ . "/Dropbox/DropboxClient.php";


Class DropboxInterface implements ModelInterface
{
    private $token;
    private $dbxObj;
    private $APP_KEY = "fv1z1w4yn5039ys";
    private $APP_SECRET = "jyzrgispic9cabg";
    private $return_url = "http://localhost/gathercloud/public/add/dropbox?auth_callback=1";
    private $APP_FULL_ACCESS = true;

    public function __construct($access_token = null)
    {
        $this->dbxObj = $this->setDbxObj($access_token);
    }

    /**
     * @param mixed $dbxObj
     */
    public function setDbxObj($token)
    {
        error_reporting(E_ALL);
        $this->enable_implicit_flush();
        // -- end of unneeded stuff

        // if there are many files in your Dropbox it can take some time, so disable the max. execution time
        set_time_limit(0);

        $dbx = new \DropboxClient(array(
            'app_key' => $this->APP_KEY,
            'app_secret' => $this->APP_SECRET,
            'app_full_access' => $this->APP_FULL_ACCESS,

        ),'en');

//        $q = \App\Token::where('user_id',1)->get()[0]->access_token;
//        $access_token = \GuzzleHttp\json_decode($q,true);
        if(!empty($token)) {
            $dbx->SetAccessToken((array)\GuzzleHttp\json_decode($token->access_token));
//			 echo "loaded access token:";
//			 print_r($access_token);
        }
        elseif(!empty($_GET['auth_callback'])) // are we coming from dbx's auth page?
        {
            // then load our previosly created request token
            $request_token = $this->load_token("request_temp");

            if(empty($request_token)) die('Request token not found!');

            // get & store access token, the request token is not needed anymore
            $access_token = $dbx->GetAccessToken($request_token);
            $this->setToken($access_token);

//			$this->store_token($access_token, "access");
            $this->delete_token("request_temp");
        }

        // checks if access token is required
        if(!$dbx->IsAuthorized()) {
            // redirect user to dbx auth page
//			$return_url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'] . "?auth_callback=1";

            $auth_url = $dbx->BuildAuthorizeUrl($this->return_url);

            $request_token = $dbx->GetRequestToken();
            $this->store_token($request_token, "request_temp");
//            die(redirect($auth_url));
            header("Location: $auth_url");
            exit();
        }

        return $dbx;
    }

    public function getAccountInfo()
    {
        $info = $this->dbxObj->GetAccountInfo();
        $nml_info = array(
            'email' => $info->email,
            'quota' => $info->quota_info->quota,
            'used' => $info->quota_info->normal,
            'remain' => $info->quota_info->quota - $info->quota_info->normal
        );
        return (object)$nml_info;
    }

    /**
     * @return mixed
     */
    public function getToken()
    {
        $tk = array(
            'access_token' => json_encode($this->token),
            'expired_in' => 0,
            'refresh_token' => ""
        );
        return (object)$tk;
    }

    /**
     * @param mixed $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    // Implements
    // @params $file = String of File Paths on Dropbox
    public function downloadFile($file, $destination = null)
    {
        if($destination == 'temp'){
            $destination = $destination . $file;
            $this->dbxObj->DownloadFile($file,$destination);
            return $destination;
        }
        else{
        $this->dbxObj->DownloadFile($file,$destination);
        header("Content-Type: application/download");
        header("Content-disposition: attachment; filename=". basename($_GET['file']));
        readfile(basename($_GET['file']));
        unlink(basename($_GET['file']));
        }
    }
    public function uploadFile($file, $destination = null)
    {
        if(is_array($file)){
            if (empty($destination)){
                $destination = $file['name'];
            } else if(strpos($destination,'/') == 0){
                $destination = $destination."/".$file['name'];
            } else
            {
                $destination = '/'.$destination."/".$file['name'];
            }
            dump($destination);
            $res = $this->dbxObj->UploadFile($file['tmp_name'] ,$destination);
            return array((object)\GuzzleHttp\json_decode($res));
        } else {
            $file = '/'.$destination."/".$file;
            $res = $this->dbxObj->CreateFolder($file);
            return array((object)$res);
        }
    }

    public function getFiles($file = null)
    {
        return $this->dbxObj->GetFiles($file);
    }
    public function deleteFile($file)
    {
        $res = $this->dbxObj->Delete($file);
        return $res->is_deleted;
    }

    public function getLink($file)
    {
        return $this->dbxObj->GetLink($file);
    }

    private function store_token($token, $name)
    {
        if(!file_put_contents(__DIR__."/Dropbox/".$name.".token", serialize($token)))
            die('<br />Could not store token! <b>Make sure that the directory `tokens` exists and is writable!</b>');
    }

    private function load_token($name)
    {
        if(!file_exists(__DIR__."/Dropbox/".$name.".token")) return null;
        return @unserialize(@file_get_contents(__DIR__."/Dropbox/$name.token"));
    }

    private function delete_token($name)
    {
        @unlink(__DIR__."/Dropbox/$name.token");
    }


    private function enable_implicit_flush()
    {
        @apache_setenv('no-gzip', 1);
        @ini_set('zlib.output_compression', 0);
        @ini_set('implicit_flush', 1);
        for ($i = 0; $i < ob_get_level(); $i++) { ob_end_flush(); }
        ob_implicit_flush(1);
    }

    public function rename($file, $new_name)
    {
        $lastIndex = strripos($file, "/");
        $new_name = substr($file, 0,$lastIndex+1) . $new_name;
        $res = $this->dbxObj->Move($file,$new_name);
        return true;
    }

    public function getPathName($file)
    {
        return $file;
    }

    public function searchFile($keyword)
    {
        $list_data =  $this->dbxObj->Search("",urlencode($keyword));
        if(!empty($list_data)){
            $key = array();
            foreach($list_data as $data){
                $temp = explode("/",$data->path);
                $key[] = end($temp);
            }
            return $this->array_fill_keys($key,$list_data);
        } return $list_data;
    }
    function array_fill_keys($target, $value = '') {
        if(is_array($target)) {
            foreach($target as $key => $val) {
                $filledArray[$val] = is_array($value) ? $value[$key] : $value;
            }
        }
        return $filledArray;
    }

    /**
     * @param output of method getFiles() $list_data
     * @param $token_id
     * @param $connection_name
     * @return list of object
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
            $val->is_dir == 1 ? $mime = null : $mime = $val->mime_type;
            array_push($format,
                array(
                    'name' => basename($k),
                    'path' => $val->path,
                    'path_name' => substr($val->path,0,strrpos($val->path,"/",-1)),
                    'bytes' => $val->bytes,
                    'mime_type' => $mime,
                    'is_dir' => ($val->is_dir)? true:false,
                    'modified' => date('Y m d H:i:s',strtotime($val->modified)),
                    'shared' => null,
                    'provider_logo' => $provider_logo,
                    'connection_name' => $connection_name
                ));
        }
        return $format;
    }
}

?>