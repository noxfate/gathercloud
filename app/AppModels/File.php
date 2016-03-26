<?php

namespace App\AppModels;

abstract class File
{
	private $name;
	private $path;
	private $size;
	private $bytes;
	private $mime_type;
	private $is_dir;
	private $modified;
	private $shared;
	public static $provider;

	abstract public function fname();
}

?>