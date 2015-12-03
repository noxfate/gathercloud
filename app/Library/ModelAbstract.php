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
    abstract function downloadFile($file, $destination = null);
    abstract function uploadFile($file, $destination = null);
    abstract function getFiles($file = null);
    abstract function deleteFile($file);
    abstract function getLink($file);
    abstract function getAccountInfo();
}