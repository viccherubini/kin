<?php namespace jolt;
declare(encoding='UTF-8');

class response {

	private $content_type = 'text/html';
	private $response_code = 405;
	
	private $headers = array();
	private $content = null;

	public function __construct() {
		
	}
	
	public function __destruct() {
	
	}
	
	
	
	public function respond() {
		foreach ($this->headers as $header => $value) {
			header("{$header}: {$value}");
		}
		return true;
	}
	
	public function set_content($content) {
		$this->content = $content;
		return $this;
	}
	
	public function set_content_type($content_type) {
		$this->content_type = $content_type;
		return $this;
	}
	
	public function set_response_code($response_code) {
		$this->response_code = (int)$response_code;
		return $this;
	}
	
	public function set_headers(array $headers) {
		$this->headers = $headers;
		return $this;
	}

}