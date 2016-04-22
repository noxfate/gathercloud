<?php

namespace App\AppModels;

use Auth;
use App\Token;
use App\Cache;

class Provider
{
	private $provider;
    private $owner;
    private $connObj;
    private $token_id;
    private $storage;
    private $conName;

	function __construct($conName)
	{
        $this->conName = $conName;
        $tk = Token::where('connection_name', $conName)
            ->where('user_id', Auth::user()->id)
            ->firstOrFail();
        $this->provider = $tk->provider;
        $this->owner = $tk->user_id;
        $this->token_id  = $tk->id;

        switch ($this->provider) {
            case "dropbox":
                $this->connObj = new \App\Library\DropboxInterface((array)\GuzzleHttp\json_decode($tk->access_token));
                break;
            case "copy":
                $this->connObj = new \App\Library\CopyInterface((array)\GuzzleHttp\json_decode($tk->access_token));
                break;
            case "box":
                $this->connObj = new \App\Library\BoxInterface((array)\GuzzleHttp\json_decode($tk->access_token));
                break;
            case "onedrive":
                $this->connObj = new \App\Library\OneDriveInterface((array)\GuzzleHttp\json_decode($tk->access_token));
                break;
            case "googledrive":
                $token = array(
                    'access_token' => json_decode($tk->access_token),
                    'refresh_token' => json_decode($tk->refresh_token),
                    'expires_in' => ""
                );
                $this->connObj = new \App\Library\GoogleInterface($token);
                break;
            default:
                return "Error!! Provider: $this->provider";
        }
		
	}

    /**
     * @return mixed
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @return mixed
     */
    public function getTokenId()
    {
        return $this->token_id;
    }

    /**
     * @return mixed
     */
    public function getProvider()
    {
        return $this->provider;
    }


    /**
     * @return \App\Library\OneDriveInterface
     */
    public function getConnObj()
    {
        return $this->connObj;
    }
    /**
     * @return mixed
     */
    public function getStorage($readable = false)
    {
        $this->storage = array(
            'quota' => 0,
            'used' => 0,
            'remain' => 0
        );
        $st = $this->connObj->getAccountInfo();
        switch($this->provider){
            case "dropbox":
                $st = $st->quota_info;
                $this->storage['quota'] = $st->quota;
                $this->storage['used'] = $st->shared + $st->normal;
                $this->storage['remain'] = $st->quota - ($st->shared + $st->normal);
                break;
            case "copy":
                $st = json_decode($st);
                $this->storage['quota'] = $st->storage->quota;
                $this->storage['used'] = $st->storage->used;
                $this->storage['remain'] = $st->storage->quota - $st->storage->used;
                break;
            case "onedrive":
                break;
            case "box":
                break;
            case "googledrive":
                break;
            default:
                return "Error!! Provider: $this->provider";
        }

        if ($readable){
            foreach ($this->storage as $key => $val){
                $this->storage[$key] = $this->humanFileSize($val);
            }
        }

        return $this->storage;
    }

	function downloadFile($file)
	{
        $this->connObj->downloadFile($file);
	}
	function uploadFile($file, $destination = null)
	{
       return $this->connObj->uploadFile($file, $destination);
	}
	function getFiles($file = null)
	{
        $data = $this->connObj->getFiles($file);
        return $this->normalizeMetaData($data, $this->provider);
	}
    function deleteFile($file)
    {
        $this->connObj->deleteFile($file);
    }

    function rename($file, $new_name){
        return $this->connObj->rename($file,$new_name);
    }

    function searchFile($keyword)
    {
        $data = $this->connObj->searchFile($keyword);
        return $this->normalizeMetaData($data, $this->provider);
    }

	function getLink($file)
	{

	}
	function getAccountInfo()
	{
        return $this->connObj->getAccountInfo();
	}

    function getPathName($file){
        return $this->connObj->getPathName($file);
    }

    private function normalizeMetaData($data, $provider)
    {

        $format = array();

        switch ($provider) {
            case "dropbox":
                foreach ($data as $k => $val) {
                    $val->is_dir == 1 ? $mime = null : $mime = $val->mime_type;
                    empty($val->shared_folder) ? $sh = false : $sh = true;
                    array_push($format,
                        array(
                            'name' => basename($k),
                            'path' => $val->path,
                            'size' => $val->size,
                            'bytes' => $val->bytes,
                            'mime_type' => $mime,
                            'is_dir' => $val->is_dir, // 1 == Folder, 0 = File
                            'modified' => $val->modified,
                            'shared' => $sh,
                            'token_id' => $this->token_id,
                            'conName' => $this->conName
                        ));
                }
                break;
            case "copy":
                foreach ($data as $k => $val) {
                    $val->type == "file" ? $is = 0 : $is = 1;
                    $is == 1 ? $mime = null : $mime = $val->mime_type;
                    $val->share_id != 0 ? $sh = true : $sh = false;
                    array_push($format,
                        array(
                            'name' => basename($val->path),
                            'path' => $val->path,
                            'size' => $this->humanFileSize($val->size),
                            'bytes' => $val->size,
                            'mime_type' => $mime,
                            'is_dir' => $is, // 1 == Folder, 0 = File
                            'modified' => date('Y m d H:i:s', $val->modified_time),
                            'shared' => $sh,
                            'token_id' => $this->token_id,
                            'conName' => $this->conName
                        ));
                }
                break;
            case "box":
                foreach ($data as $k => $val) {
//                    $val->type == "file"? $is = 0 : $is = 1;
//                    $is == 1 ? $mime = null : $mime = $val->mime_type;
//                    $val->share_id != 0 ? $sh = true : $sh = false;
                    array_push($format,
                        array(
                            'name' => $val['name'],
                            'path' => $val['path'],
                            'size' => $this->humanFileSize($val['size']),
                            'bytes' => $val['size'],
                            'mime_type' => $val['mime_type'],
                            'is_dir' => $val['is_dir'],
                            'modified' => date('Y m d H:i:s', strtotime($val['modified'])),
                            'shared' => $val['shared'],
                            'token_id' => $this->token_id,
                            'conName' => $this->conName
                        ));
                }
                break;
            case "onedrive":
                foreach ($data as $val) {
                    $val->isFolder() ? $is = 1 : $is = 0;
//                    $is == 1 ? $mime = null : $mime = $val->mime_type;
//                    $val->share_id != 0 ? $sh = true : $sh = false;
                    array_push($format,
                        array(
                            'name' => basename($val->getName()),
                            'path' => $val->getId(),
                            'size' => $this->humanFileSize($val->getSize()),
                            'bytes' => $val->getSize(),
                            'mime_type' => null,
                            'is_dir' => $is, // 1 == Folder, 0 = File
                            'modified' => date('Y m d H:i:s', $val->getUpdatedTime()),
                            'shared' => false, // dafuq is this?
                            'token_id' => $this->token_id,
                            'conName' => $this->conName
                        ));
                }
                break;
            default:
                return "Error!! Provider: $provider";
        }

        return $format;

    }

    private
    function humanFileSize($size)
    {
        if (!$size) {
            return "";
        } elseif (($size >= 1 << 30)) {
            return number_format($size / (1 << 30), 2) . "GB";
        } elseif (($size >= 1 << 20)) {
            return number_format($size / (1 << 20), 2) . "MB";
        } elseif (($size >= 1 << 10)) {
            return number_format($size / (1 << 10), 2) . "kB";
        } else {
            return number_format($size) . "B";
        }
    }
}

?>