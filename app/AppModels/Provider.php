<?php

namespace App\AppModels;

use Auth;
use App\Token;
use App\Cache;

class Provider
{
	private $provider;
    private $connObj;

	function __construct($conName)
	{
        $tk = Token::where('connection_name', $conName)
            ->where('user_id', Auth::user()->id)
            ->get();
        $this->provider = $tk[0]->provider;

        switch ($this->provider) {
            case "dropbox":
                $this->connObj = new \App\Library\DropboxInterface((array)\GuzzleHttp\json_decode($tk[0]->access_token));
                break;
            case "copy":
                $this->connObj = new \App\Library\CopyInterface((array)\GuzzleHttp\json_decode($tk[0]->access_token));
                break;
            case "box":
                $this->connObj = new \App\Library\BoxInterface((array)\GuzzleHttp\json_decode($tk[0]->access_token));
                break;
            case "onedrive":
                $this->connObj = new \App\Library\OneDriveInterface((array)\GuzzleHttp\json_decode($tk[0]->access_token));
                break;
            default:
                return "Error!! Provider: $this->provider";
        }
		
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

	function downloadFile($file, $destination = null)
	{
        $this->connObj->downloadFile($file, $destination);
	}
	function uploadFile($file, $destination = null)
	{
        $this->connObj->uploadFile($file, $destination);
	}
	function getFiles($file = null)
	{
        $data = $this->connObj->getFiles($file);
        return $this->normalizeMetaData($data, $this->provider);
	}
	function deleteFile($file)
	{

	}
	function getLink($file)
	{

	}
	function getAccountInfo()
	{
        return $this->connObj->getAccountInfo();
	}

    private function normalizeMetaData($data, $provider)
    {
//        $name = '';
//        $path = '';
//        $size = '';
//        $bytes = 0;
//        $mime_type = '';
//        $file_type = '';
//        $last_modified = '';
//        $shared = false;
//        $provider = '';

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
                            'provider' => $provider
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
                            'provider' => $provider
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
                            'provider' => $provider
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
                            'provider' => $provider
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