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
    /**
     * Implement construct
     *
     * @param (object) $token
     * =>(string)access_token
     * =>(integer)expired_in
     * =>(string)refresh_token
     *
     * function __construct($token)
     */



    /**
     * @return object
     * =>(string)email
     * =>(integer)quota
     * =>(integer)used
     * =>(integer)remain
     */
    public function getAccountInfo();

    /**
     * @return object
     * =>(string)access_token
     * =>(integer)expired_in
     * =>(string)refresh_token
     */
    public function getToken();

    /**
     * @param string or null $file
     * @return list of file metadata (value depend on provider)
     */
    public function getFiles($file = null);

    /**
     * @param string $file
     * @return string of Location
     */
    public function downloadFile($file);

    /**
     * @param string $file
     * @param null $destination
     * @return boolean
     *
     */
    public function uploadFile($file, $destination = null);

    /**
     * @param string $file
     * @return string of public share url
     */
    public function getLink($file);

    /**
     * @param string $keyword
     * @return list of file metadata (value depend on provider)
     */
    public function searchFile($keyword);

    /**
     * @param string $file
     * @return boolean
     */
    public function deleteFile($file);

    /**
     * @param string $file
     * @param string $new_name
     * @return boolean
     */
    public function rename($file, $new_name);

    /**
     * @param string $file
     * @return string format 'example/example/example'
     */
    public function getPathName($file);

    /**
     * @param output of method getFiles() $list_data
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
    public function normalizeMetaData($list_data, $provider_logo, $connection_name);

}