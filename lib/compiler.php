<?php namespace jolt;
declare(encoding='UTF-8');

require_once(__DIR__.'/exceptions/unrecoverable.php');

class compiler {

	private $class = '';
	private $file = '';
	private $path = '';

	private $controller = null;
	
	public function __construct() {
	
	}
	
	public function __destruct() {
	
	}



	public function compile() {
		$this->check_class()
			->check_file();
		
		$file_path = $this->path.$this->file;
		if (!is_file($file_path)) {
			throw new \jolt\exception\unrecoverable("The compiler can not find the controller file, {$file_path}. Compilation can not continue.");
		}
		
		require_once($file_path);
		if (!class_exists($this->class, false)) {
			throw new \jolt\exception\unrecoverable("The compiler can not find the controller class, {$this->class} in the controller file, {$file_path}. Compilation can not continue.");
		}
		
		$ref = new \ReflectionClass($this->class);
		$this->controller = $ref->newInstance();
		
		return true;
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
		$this->path = rtrim($path, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
		return $this;
	}



	public function get_controller() {
		return $this->controller;
	}
	
	
	
	private function check_class() {
		if (empty($this->class)) {
			throw new \jolt\exception\unrecoverable("The compiler must have a controller class before it can begin compilation.");
		}
		return $this;
	}
	
	private function check_file() {
		if (empty($this->file)) {
			throw new \jolt\exception\unrecoverable("The compiler must have a controller file before it can begin compilation.");
		}
		return $this;
	}
	
}