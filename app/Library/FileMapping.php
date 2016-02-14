<?php

namespace App\Library;

use Auth;
use App\Token;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;

class FileMapping
{

	private $data;

	public function __construct($data)
	{
		$this->data = $data;
	}

	public function createFileMapping()
	{
		
	}

	public function searchFiles()
	{

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
