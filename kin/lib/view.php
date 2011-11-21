<?php namespace kin;
require_once(__DIR__."/exceptions/unrecoverable.php");

class view {

	public $helper = null;
	public $content_type = "";
	public $file = "";
	public $path = "";
	public $rendering = "";
	public $acceptable_types = array();
	public $payload = array();
	
	const ext = "php";

	public function __construct() { }

	public function attach_helper(helper $helper) {
		$this->helper = $helper;
		return($this);
	}
	
	public function render() {
		$found_view = false;
		foreach ($this->acceptable_types as $content_type) {
			$content_type_bits = explode("/", $content_type);
			$file_path = $this->path.$this->compile_view_filename($content_type_bits[1]);

			if (is_file($file_path)) {
				$found_view = true;
				$this->set_content_type($content_type)
					->render_file($file_path);
				break;
			}
		}
		
		if (!$found_view) {
			throw new unrecoverable("The renderer can not find any acceptable views. Please request this application with at least one acceptable type.", 406);
		}
		return($this);
	}
	
	public function safe($v) {
		return htmlentities($v, ENT_COMPAT, "UTF-8");
	}

	public function set_acceptable_types(array $acceptable_types) {
		$this->acceptable_types = $acceptable_types;
		return($this);
	}
	
	public function set_payload(array $payload) {
		$this->payload = $payload;
		return($this);
	}
	
	public function set_content_type($content_type) {
		$this->content_type = trim($content_type);
		return($this);
	}
	
	public function set_file($file) {
		$this->file = trim($file);
		return($this);
	}
	
	public function set_path($path) {
		$this->path = rtrim($path, "/")."/";
		return($this);
	}
	
	public function get_content_type() {
		return($this->content_type);
	}
	
	public function get_path() {
		return $this->path;
	}
	
	public function get_rendering() {
		return $this->rendering;
	}



	private function compile_view_filename($type) {
		return(implode(".", array($this->file, $type, self::ext)));
	}
	
	private function render_file($file_path) {
		$payload = $this->payload;
		ob_start();
			extract($this->payload);
			require($file_path);
		$this->rendering = ob_get_clean();
		return($this);
	}
	
}
