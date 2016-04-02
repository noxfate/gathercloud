<?php

namespace App\Library;

use Auth;
use App\Token;
use App\File;
use App\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;

class FileMapping
{
    private $usrId;

	public function __construct($usrId)
	{
        $this->usrId = $usrId;
	}

    public function getFirstLevel()
    {
        $que = User::find($this->usrId)->tokens;

        $data = collect();
        foreach ($que as $d ) {
            $root = File::roots()->where('token_id', $d->id)->first();
            if ($root !== null){
                $data = $data->merge($root->getImmediateDescendants());
            }
        }
        return $data;
    }

	// Return multidimentional array contains of metaData of similar name
    public function searchFiles($keyword)
    {
        $token = User::find($this->usrId)->tokens;
        $result = collect();
        $keyword = '%'.$keyword.'%';
        foreach ($token as $tk){
            $result = $result->merge(File::where('token_id', $tk->id)
                ->where('name', 'LIKE', $keyword)
                ->get());
        }
        return $result;
    }

	// Return Array of metaData in the selected Level
    public function traverseInsideFolder($folderPath = 'root', $token_id)
    {
//        $token = User::find($this->usrId)->tokens->find($token_id);
        $root = File::where('token_id', $token_id)
            ->where('path', $folderPath)
            ->first();
        $file = $root->children()->get();

        return $file;
    }

//	public function traverseInsideFolder($data, $path, $provider)
//	{
//		$index = $this->getFolderIndex($path, $provider, $data);
//
//		if ($index !== false){
//			return $data[$index]['is_dir'];
//		}else{
//			foreach ($data as $k) {
//				if ($k['is_dir']){
//					$a = $this->traverseInsideFolder($k['is_dir'], $path, $provider);
//					if (!is_null($a)){
//						return $a;
//					}
//				}
//			}
//		}
//	}

//	public function searchFiles($data, $keyword, $result)
//	{
//		if (empty($keyword))
//			return null;
//        $result = array();
//		foreach ($data as $k) {
//            if ($k['is_dir']) {
//                $a = $this->searchFiles($k['is_dir'], $keyword, $result);
//                if (!is_null($a))
//                    $result = array_merge($result, $a);
//            }
//            if (stripos($k['name'], $keyword) !== false) {
//                $result[] = $k;
//            }
//
//        }
//		return $result;
//
//	}

//	// Use to Retrieve Folder index which has the same Name but different Provider
//	private function getFolderIndex($path, $provider , $array_data)
//	{
//		foreach ($array_data as $key => $val) {
//			# code...
//			if ($val['path'] == $path && $val['provider'] == $provider)
//				return $key;
//		}
//		return false;
//	}

}

?>
