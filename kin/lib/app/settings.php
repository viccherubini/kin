<?php namespace kin;
require_once(__DIR__."/../exceptions/unrecoverable.php");

class settings {

	public $settings = array();
	public $custom = array();
	public $paths = array();

	public function __construct($app_path=null) {
		$this->paths = array(
			"app_path" => true,
			"controllers_path" => true,
			"validators_path" => true,
			"views_path" => true,
			"assets_path" => true,
			"css_path" => true,
			"js_path" => true,
			"images_path" => true
		);
		
		$this->settings = array(
			"app_path" => "",
			"controllers_path" => "",
			"validators_path" => "",
			"views_path" => "",
			"assets_path" => "assets/",
			"css_path" => "",
			"js_path" => "",
			"images_path" => "",
			"accept" => "",
			"server_name" => "",
			"allow_ssl" => true,
			"force_ssl" => false,
			"url" => "",
			"secure_url" => "",
			"rewrite" => false
		);
		
		if (!empty($app_path)) {
			$this->app_path = $app_path;
		}
	}
	
	public function __set($k, $v) {
		if (array_key_exists($k, $this->settings) && gettype($v) === gettype($this->settings[$k])) {
			if (array_key_exists($k, $this->paths)) {
				$v = $this->append_ending_slash($v);
			}
			$this->settings[$k] = $v;
		}
		return(true);
	}
	
	public function __get($k) {
		if (array_key_exists($k, $this->settings)) {
			return($this->settings[$k]);
		}
		return(null);
	}
	
	public function __isset($k) {
		return(array_key_exists($k, $this->settings) && !empty($this->settings[$k]));
	}
	
	
	
	public function compile() {
		if (empty($this->settings["app_path"])) {
			throw new unrecoverable("The settings can not begin compilation, an app_path is not set.");
		}
		
		$script_name = dirname(filter_input(INPUT_SERVER, "SCRIPT_NAME"));
		$script_root = ltrim($script_name, "/");
		$this->rewrite = empty($script_root);
		
		$app_path = $this->settings["app_path"];
		$this->compile_app_subpath("controllers_path", "controllers")
			->compile_app_subpath("validators_path", "validators")
			->compile_app_subpath("views_path", "views");
		
		$this->compile_assets_subpath("css_path", "css")
			->compile_assets_subpath("js_path", "js")
			->compile_assets_subpath("images_path", "images");
			
		if (empty($this->settings["server_name"])) {
			$default_options = array("options" => array("default" => "localhost"));
			$this->server_name = filter_input(INPUT_SERVER, "SERVER_NAME", FILTER_SANITIZE_URL, $default_options);
		}
		
		if (empty($this->settings["url"])) {
			$url_protocol = ($this->allow_ssl && $this->force_ssl ? "https://" : "http://");
			$this->url = rtrim($url_protocol.$this->server_name.$script_name, "/")."/";
		}
		
		if (empty($this->settings["secure_url"])) {
			$url_protocol = ($this->allow_ssl ? "https://" : "http://");
			$this->secure_url = rtrim($url_protocol.$this->server_name.$script_name, "/")."/";
		}

		return(true);
	}
	
	public function add_custom($k, $v) {
		if (!array_key_exists($k, $this->settings)) {
			$this->custom[$k] = $v;
		}
		return($this);
	}
	
	public function get_settings() {
		return($this->settings);
	}
	
	public function get_custom() {
		return($this->custom);
	}
	
	public function get_custom_by_key($k) {
		if (array_key_exists($k, $this->custom)) {
			return($this->custom[$k]);
		}
		return(null);
	}
	
	
	
	private function append_ending_slash($v) {
		return(rtrim($v, "/")."/");
	}
	
	private function compile_app_subpath($key, $path) {
		if (empty($this->settings[$key])) {
			$this->$key = $this->settings["app_path"].$path;
		}
		return($this);
	}
	
	private function compile_assets_subpath($key, $path) {
		if (empty($this->settings[$key])) {
			$this->$key = $this->settings["assets_path"].$path;
		}
		return($this);
	}
	
}
