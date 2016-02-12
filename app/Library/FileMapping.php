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
	public function traverseInsideFolder($data, $path, $level = 0)
	{
		// print_r($data);
		echo "-------------<br>";
		print_r("lvl: ".$level);
		echo "<br>-------------<br>";		
		print_r(array_column($data, 'path'));
		echo "<br>-------------<br>";

		$index = array_search($path, array_column($data, 'path'));
	

		if ($index !== false){
			echo "Found!";
			print_r($data[$index]['is_dir']);
			return $data[$index]['is_dir'];
		}else{
			foreach ($data as $k) {
				if ($k['is_dir']){
					$this->traverseInsideFolder($k['is_dir'], $path, $level+1);
				}
			}
		}
	}

	public function dataEncode()
	{

	}

	public function dataDecode()
	{
		
	}
}

?>
