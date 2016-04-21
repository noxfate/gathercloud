<?php
/**
 * Created by PhpStorm.
 * User: jarvis
 * Date: 17/11/2558
 * Time: 16:41 น.
 */

namespace App\Library;


interface ModelInterface
{
    public function downloadFile($file);
    public function uploadFile($file, $destination = null);
    public function getFiles($file = null);
    public function deleteFile($file);
    public function getLink($file);
    public function getAccountInfo();
    public function rename($file, $new_name);
    public function getPathName($file);
    public function getAccessToken();
    public function SearchFile($keyword);
}