<?php
/**
 * Created by PhpStorm.
 * User: jarvis
 * Date: 17/11/2558
 * Time: 16:41 น.
 */

namespace App\Library;


abstract class ModelAbstract
{
    abstract public function downloadFile($file, $destination = null);
    abstract public function uploadFile($file, $destination = null);
    abstract public function getFiles($file = null);
    abstract public function deleteFile($file);
    abstract public function getLink($file);
    abstract public function getAccountInfo();
}