<?php namespace jolt;
declare(encoding='UTF-8');

class compiler {

	private $class = '';
	private $file = '';
	private $path = '';

	public function __construct() {
	
	}
	
	public function __destruct() {
	
	}



	public function compile() {
		
	}
	
	
	
	public function set_class($class) {
		$this->class = trim($class);
		return $this;
	}
	
	public function set_file($file) {
		$this->file = trim($file);
		return $this;
	}
	
	public function set_path($path) {
		$this->path = trim($path);
		return $this;
	}



	

}