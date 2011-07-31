<?php namespace jolt;
declare(encoding='UTF-8');

abstract class controller {
	
	public $headers = array();
	
	public $response_code = 200;
	
	public $payload = null;
	public $view = null;
	
	public function __construct() {
	
	}
	
	public function __destruct() {
	
	}
	
	public function __set($k, $v) {
		$this->payload->contents[$k] = $v;
		return $this;
	}
	
	public function __get($k) {
		if (array_key_exists($k, $this->payload->contents)) {
			return $this->payload->contents[$k];
		}
		return null;
	}
	
	public function attach_payload(payload $payload) {
		$this->payload = $payload;
		return $this;
	}
	
	public function add_header($header, $value) {
		if ('content-type' !== strtolower($header)) {
			$this->headers[$header] = $value;
		}
		return $this;
	}

	public function register($k, $v) {
		return $this->__set($k, $v);
	}
	
	public function render($view) {
		$this->view = trim($view);
	}
	
	public function set_content_type($content_type) {
		$this->content_type = trim($content_type);
		return $this;
	}

	public function set_response_code($response_code) {
		$this->response_code = intval($response_code);
		return $this;
	}
	
}