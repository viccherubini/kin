<?php namespace kin\app;

class controller {
	
	public $helper = null;
	
	public $raw_request = "";
	public $request_parsers = array();
	public $request = array();
	
	// Data for the response
	public $headers = array();
	public $payload = array();
	
	public $content_type = "";
	public $response_code = self::response_200;
	public $view = "";

	const response_200 = 200;
	const response_201 = 201;
	const response_301 = 301;
	const response_302 = 302;
	const response_400 = 400;
	const response_401 = 401;
	const response_403 = 403;
	const response_404 = 404;
	const response_405 = 405;
	const response_409 = 409;
	const response_500 = 500;
	const response_501 = 501;
	
	
	public function __construct() {
		$this->payload = array();
		$this->raw_request = file_get_contents("php://input");
		
		$this->attach_request_parser("application/x-www-form-urlencoded", function($raw_request) {
			$request = array(); parse_str($raw_request, $request);
			return($request);
		});
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
	
	
	
	public function attach_request_parser($content_type, \Closure $parser) {
		$this->request_parsers[$content_type] = $parser;
		return($this);
	}
	
	public function get($key="", $default="") {
		if (empty($key)) {
			return($_GET);
		} else {
			return($this->find_array_value_by_key($key, $_GET, $default));
		}
	}
	
	public function request($key="", $default="") {
		if (empty($key)) {
			return($this->request);
		} else {
			return($this->find_array_value_by_key($key, $this->request, $default));
		}
	}
	
	public function parse_request() {
		$hits = array();
		$content_type = strtolower(filter_input(INPUT_SERVER, "CONTENT_TYPE"));
		
		if (preg_match("#^([a-z0-9\-]+/[a-z0-9\-]+).*$#i", $content_type, $hits)) {
			if (array_key_exists($hits[1], $this->request_parsers)) {
				$this->request = call_user_func($this->request_parsers[$hits[1]],
					$this->raw_request);
			}
		}
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
	

	
	private function find_array_value_by_key($key, $array, $default) {
		return(array_key_exists($key, $array) ? $array[$key] : $default);
	}

}
