<?php

namespace App\Library;

use Auth;
use App\Token;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;

class FileMapping
{

	private $data;

	public function __construct($data = null)
	{
		$this->data = $data;
	}

	// public function createFileMapping()
	// {
		
	// }

	// Return multidimentional array contains of metaData of similar name
	public function searchFiles($data, $keyword, $result)
	{
		if (empty($keyword))
			return null;
        $result = array();
		foreach ($data as $k) {
            if ($k['is_dir']) {
                $a = $this->searchFiles($k['is_dir'], $keyword, $result);
                if (!is_null($a))
                    $result = array_merge($result, $a);
            }
            if (stripos($k['name'], $keyword) !== false) {
                $result[] = $k;
            }

        }
		return $result;

		
	}

	// Return Array of metaData in the selected Level
	public function traverseInsideFolder($data, $path, $provider)
	{
		
		$index = $this->getFolderIndex($path, $provider, $data);
		
		if ($index !== false){
			return $data[$index]['is_dir'];
		}else{
			foreach ($data as $k) {
				if ($k['is_dir']){
					$a = $this->traverseInsideFolder($k['is_dir'], $path, $provider);
					if (!is_null($a)){
						return $a;
					}
				}
			}
		}
	}

	// Use to Retrieve Folder index which has the same Name but different Provider
	private function getFolderIndex($path, $provider , $array_data)
	{
		foreach ($array_data as $key => $val) {
			# code...
			if ($val['path'] == $path && $val['provider'] == $provider)
				return $key;
		}
		return false;
	}

	public function dataEncode()
	{

	}

	public function dataDecode()
	{
		
	}
}

?>
