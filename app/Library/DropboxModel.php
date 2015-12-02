<?php

namespace App\Library;

use Auth;
use App\Token;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;

require __DIR__ . "/Dropbox/DropboxClient.php";



Class DropboxModel extends ModelAbstract
{
    private $token;
    private $dbxObj;
    private $APP_KEY = "fv1z1w4yn5039ys";
    private $APP_SECRET = "jyzrgispic9cabg";
    private $APP_FULL_ACCESS = true;

	function __construct($access_token = null)
	{
        $this->dbxObj = $this->setDbxObj($access_token);
	}

    /**
     * @param mixed $dbxObj
     */
    public function setDbxObj($access_token)
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
        if(!empty($access_token)) {
            $dbx->SetAccessToken($access_token);
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

            $return_url = "http://localhost/gathercloud/public/add/dropbox?auth_callback=1";
            $auth_url = $dbx->BuildAuthorizeUrl($return_url);

//			echo "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'] . "?auth_callback=1<br>";
//            echo $return_url;
//            echo "<br>";
//            echo $auth_url;

            $request_token = $dbx->GetRequestToken();
            $this->store_token($request_token, "request_temp");
            die(redirect($auth_url));

        }

        return $dbx;
    }



    function getAccountInfo()
    {
        return $this->dbxObj->GetAccountInfo();
    }

    /**
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
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
	function downloadFile($file, $destination = null)
	{
		return $this->dbxObj->DownloadFile($file,$destination);
	}
	function uploadFile($file, $destination = null)
	{
		if (empty($destination)){
			$destination = $file['name'];
		} else
		{
			$destination = substr($destination, "/".$file['name']);
		}	
 		return $this->dbxObj->UploadFile($file['tmp_name'] ,$destination);
	}
	function getFiles($file = null)
	{
		return $this->dbxObj->GetFiles();
	}
	function deleteFile($file)
	{
		return $this->dbxObj->Delete($file);
	}

	function getLink($file)
	{
		return $this->dbxObj->GetLink($file);
	}

	function store_token($token, $name)
	{
		if(!file_put_contents(__DIR__."/Dropbox/".$name.".token", serialize($token)))
			die('<br />Could not store token! <b>Make sure that the directory `tokens` exists and is writable!</b>');
	}

	function load_token($name)
	{
		if(!file_exists(__DIR__."/Dropbox/".$name.".token")) return null;
		return @unserialize(@file_get_contents(__DIR__."/Dropbox/$name.token"));
	}

	function delete_token($name)
	{
		@unlink(__DIR__."/Dropbox/$name.token");
	}


	function enable_implicit_flush()
	{
		@apache_setenv('no-gzip', 1);
		@ini_set('zlib.output_compression', 0);
		@ini_set('implicit_flush', 1);
		for ($i = 0; $i < ob_get_level(); $i++) { ob_end_flush(); }
		ob_implicit_flush(1);
		// echo "<!-- ".str_repeat(' ', 2000)." -->";
	}

}

?>