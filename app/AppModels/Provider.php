<?php

namespace App\AppModels;

use Auth;
use App\User;
use App\Token;
use App\Providers;

class Provider
{
    private $provider_value;
    private $provider_logo;
    private $owner;
    private $connObj;
    private $token_id;
    private $storage;
    private $connection_name;

    function __construct($connection_name)
    {
        $this->connection_name = $connection_name;
        $tk = Token::where('connection_name', $connection_name)
            ->where('user_id', Auth::user()->id)
            ->firstOrFail();
        $pvd = Providers::where("id",$tk->provider_id)->first();
        $this->provider_value = $pvd->reference_name;
        $this->provider_logo = $pvd->provider_logo;
        $this->owner = $tk->user_id;
        $this->token_id  = $tk->id;
        $token = array(
            'access_token' => $tk->access_token,
            'expired_in' => $tk->expired_in,
            'refresh_token' => $tk->refresh_token,
        );
        $className = '\\App\\Library\\' . $this->provider_value . 'Interface';
        $this->connObj = new $className((object)$token);
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
//    public function getStorage($readable = false)
//    {
//        $this->storage = array(
//            'quota' => 0,
//            'used' => 0,
//            'remain' => 0
//        );
//        $st = $this->connObj->getAccountInfo();
//        switch($this->provider){
//            case "dropbox":
//                $st = $st->quota_info;
//                $this->storage['quota'] = $st->quota;
//                $this->storage['used'] = $st->shared + $st->normal;
//                $this->storage['remain'] = $st->quota - ($st->shared + $st->normal);
//                break;
//            case "copy":
//                $st = json_decode($st);
//                $this->storage['quota'] = $st->storage->quota;
//                $this->storage['used'] = $st->storage->used;
//                $this->storage['remain'] = $st->storage->quota - $st->storage->used;
//                break;
//            case "onedrive":
//                break;
//            case "box":
//                break;
//            case "googledrive":
//                break;
//            default:
//                return "Error!! Provider: $this->provider";
//        }
//
//        if ($readable){
//            foreach ($this->storage as $key => $val){
//                $this->storage[$key] = $this->humanFileSize($val);
//            }
//        }
//
//        return $this->storage;
//    }

    function downloadFile($file , $des = null)
    {
        return $this->connObj->downloadFile($file,$des);
    }
    function uploadFile($file, $destination = null)
    {
        $res = $this->connObj->uploadFile($file, $destination);
        $res = $this->connObj->normalizeMetaData($res,$this->provider_logo,$this->connection_name);
        return $res;
    }
    function getFiles($file = null)
    {
        $data = $this->connObj->getFiles($file);
        $data = $this->connObj->normalizeMetaData($data,$this->provider_logo,$this->connection_name);
        $data = $this->humanFileSize($data);
        return $data;
    }
    function deleteFile($file)
    {
        $res = $this->connObj->deleteFile($file);
        return $res;
    }

    function rename($file, $new_name){
        $res = $this->connObj->rename($file,$new_name);
        return ($res) ? 'true' : 'false';

    }

    function searchFile($keyword)
    {
        $data = $this->connObj->searchFile($keyword);
        $data = $this->connObj->normalizeMetaData($data,$this->provider_logo,$this->connection_name);
        $data = $this->humanFileSize($data);
        return $data;
    }

    function getLink($file)
    {
        $link = $this->connObj->getLink($file);
        return $link;
    }
    function getAccountInfo()
    {
        return $info = $this->connObj->getAccountInfo();
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

    private function humanFileSize($data)
    {
        foreach ($data as $k => $val) {
            $size = $val['bytes'];
            if (!$size) {
                $data[$k]['size'] = "";
            } elseif (($size >= 1 << 30)) {
                $data[$k]['size'] = number_format($size / (1 << 30), 2) . "GB";
            } elseif (($size >= 1 << 20)) {
                $data[$k]['size'] = number_format($size / (1 << 20), 2) . "MB";
            } elseif (($size >= 1 << 10)) {
                $data[$k]['size'] = number_format($size / (1 << 10), 2) . "kB";
            } else {
                $data[$k]['size'] = number_format($size) . "B";
            }
        }
        return $data;
    }

    public function humanStorageSize($data)
    {
        $data = (array)$data;
        $percent = floor(($data['used'] / $data['quota']) * 100);
        foreach($data as $key => $d){
            if(is_numeric($d)){
                if (($d >= 1 << 30)) {
                    $data[$key] = number_format($d / (1 << 30), 2) . "GB";
                } elseif (($data[$key] >= 1 << 20)) {
                    $data[$key] = number_format($d / (1 << 20), 2) . "MB";
                } elseif (($data[$key] >= 1 << 10)) {
                    $data[$key] = number_format($d / (1 << 10), 2) . "KB";
                } else {
                    $data[$key] = number_format($d) . "B";
                }
            }
        }
        $data['percent'] = $percent;
        return (object)$data;
    }
}

?>