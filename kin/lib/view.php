<?php namespace kin;
declare(encoding='UTF-8');

require_once(__DIR__.'/exceptions/unrecoverable.php');

class view {

	private $file = '';
	private $path = '';
	private $type = '';
	private $rendering = '';
	
	private $payload = array();
	
	const ext = 'php';

	public function __construct() {
		
	}


	
	public function render() {
		$file_path = $this->path.$this->compile_view_name();
		if (!is_file($file_path)) {
			throw new \kin\exception\unrecoverable("The renderer can not find the view file, {$file_path}. Rendering can not continue.");
		}

		$payload = $this->payload;
		ob_start();
			require($file_path);
		$this->rendering = ob_get_clean();

		return($this);
	}



	public function set_file($file) {
		$this->file = trim($file);
		return($this);
	}
	
	public function set_path($path) {
		$this->path = rtrim($path, '/').'/';
		return($this);
	}
	
	public function set_type($type) {
		$this->type = strtolower(trim($type));
		return($this);
	}
	
	public function set_payload(array $payload) {
		$this->payload = $payload;
		return($this);
	}
	
	
	
	public function get_path() {
		return $this->path;
	}
	
	public function get_rendering() {
		return $this->rendering;
	}



	private function compile_view_name() {
		$view_bits = array($this->file);
		if (!empty($this->type)) {
			$view_bits[] = $this->type;
		}
		
		if (false === strripos($this->file, self::ext)) {
			$view_bits[] = self::ext;
		}
		
		return(implode('.', $view_bits));
	}
	
}