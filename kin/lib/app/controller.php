<?php namespace kin\app;

class controller {
	
	public $helper = null;
	
	public $headers = array();
	public $payload = array();
	
	public $content_type = "";
	public $response_code = self::response_200;
	public $view = "";

	public $request = null;
	
	const response_200 = 200;
	const response_201 = 201;
	const response_301 = 301;
	const response_302 = 302;
	const response_400 = 400;
	const response_403 = 403;
	const response_404 = 404;
	const response_405 = 405;
	const response_409 = 409;
	const response_500 = 500;
	const response_501 = 501;
	
	public function __construct() {
		$this->payload = array();
	}
	
	public function __destruct() {
		$this->payload = array();
	}
	
	public function __set($k, $v) {
		$this->payload[$k] = $v;
		return($this);
	}
	
	public function __get($k) {
		if (array_key_exists($k, $this->payload)) {
			return $this->payload[$k];
		}
		return(null);
	}
	
	
	
	public function attach_helper(helper $helper) {
		$this->helper = $helper;
		return($this);
	}
	
	public function attach_request(\kin\http\request $request) {
		$this->request = $request;
		return($this);
	}
	
	
	
	public function add_header($header, $value) {
		$header = strtolower(trim($header));
		if ("content-type" !== $header) {
			$this->headers[$header] = $value;
		} else {
			$this->set_content_type($value);
		}
		return($this);
	}
	
	public function redirect($location, $response_code=self::response_302) {
		if (!in_array($response_code, array(self::response_301, self::response_302), true)) {
			$response_code = self::response_302;
		}
		
		$this->add_header("location", $location)
			->set_response_code($response_code);
		return($this);
	}
	
	public function register($k, $v) {
		return($this->__set($k, $v));
	}
	
	public function render($view) {
		$this->view = trim($view);
		return($this);
	}
	
	
	
	public function has_content_type() {
		return(!empty($this->content_type));
	}
	
	public function has_view() {
		return(!empty($this->view));
	}
	
	
	
	public function set_content_type($content_type) {
		$this->content_type = strtolower($content_type);
		return($this);
	}
	
	public function set_response_code($response_code) {
		$this->response_code = (int)$response_code;
		return($this);
	}
	
	
	
	public function get_helper() {
		return($this->helper);
	}

	public function get_headers() {
		return($this->headers);
	}
	
	public function get_payload() {
		return($this->payload);
	}
	
	public function get_content_type() {
		return($this->content_type);
	}
	
	public function get_response_code() {
		return($this->response_code);
	}
	
	public function get_view() {
		return($this->view);
	}
	
}