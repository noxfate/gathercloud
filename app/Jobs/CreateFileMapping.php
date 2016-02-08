<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Contracts\Bus\SelfHandling;

class CreateFileMapping extends Job implements SelfHandling
{

    private $obj;
    private $provider;
    private $done;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($conn, $prov)
    {
        $this->obj = $conn;
        $this->provider = $prov; 
        $this->done = array();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $f = $this->processData();
        $js = json_encode($f);
        dd($js);
    }

    private function processData($path = '/')
    {
        $files = array();
        $rec_data = $this->normalizeMetaData($this->obj->getFiles($path), $this->provider);
        foreach ($rec_data as $d) {
            # code...
            if (!$d['is_dir']){
                array_push($files, $d);
            }else{
                $d['is_dir'] = $this->processData($d['path']);
                array_push($files, $d);

            }
        }
        return $files;

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
                            'shared' => $sh
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
                            'shared' => $sh
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
                            'shared' => $val['shared']
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
                            'shared' => false
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
