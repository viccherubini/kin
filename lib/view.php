<?php namespace jolt;
declare(encoding='UTF-8');

require_once(__DIR__.'/exceptions/unrecoverable.php');

class view {

	//private $css_path = null;
	//private $image_path = null;
	//private $js_path = null;
	//private $view_path = null;
	
	//private $secure_url = null;
	//private $url = null;
	//private $use_rewrite = false;
	private $file = '';
	private $path = '';
	private $type = '';
	private $rendering = '';
	
	private $payload = array();

	//private $javascripts = array();

	public function __construct() {
		
	}

	public function __destruct() {

	}
	
	//public function attach_payload(payload $payload) {
	//	$this->payload = $payload;
	//	return $this;
	//}

	public function render() {
		$view = $this->file.'.'.$this->type.'.php';
		$file_path = $this->path.$view;
		if (!is_file($file_path)) {
			throw new \jolt\exception\unrecoverable("The renderer can not find the view file, {$file_path}. Compilation can not continue.");
		}

		$payload = $this->payload;
		ob_start();
			require($file_path);
		$this->rendering = ob_get_clean();

		return $this;
	}

	public function safe($v) {
		return htmlentities($v, ENT_COMPAT, 'UTF-8');
	}

	
	
	public function set_file($file) {
		$this->file = trim($file);
		return $this;
	}
	
	public function set_path($path) {
		$this->path = rtrim($path, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
		return $this;
	}
	
	public function set_type($type) {
		$this->type = strtolower(trim($type));
		return $this;
	}
	
	public function set_payload(array $payload) {
		$this->payload = $payload;
		return $this;
	}
	
	
	
	public function get_rendering() {
		return $this->rendering;
	}

}