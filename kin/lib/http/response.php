<?php namespace kin\http;
declare(encoding='UTF-8');

class response {

	private $content_type = '';
	private $response_code = 200;
	
	private $headers = array();
	private $content = null;

	public function __construct() {
		
	}
	
	
	
	public function respond() {
		header_remove('content-type');
		header('content-type: '.$this->content_type, true, $this->response_code);
		
		foreach ($this->headers as $header => $value) {
			$full_header = implode(': ', array($header, $value));
			
			header_remove($header);
			header($full_header, true, $this->response_code);
		}
		
		$memory_usage_kb = round((memory_get_peak_usage()/1024), 3);
		header('X-Memory-Usage: '.$memory_usage_kb.'KB');
		
		return($this->content);
	}
	
	
	
	public function set_content($content) {
		$this->content = $content;
		return($this);
	}
	
	public function set_content_type($content_type) {
		$this->content_type = $content_type;
		return($this);
	}
	
	public function set_response_code($response_code) {
		$this->response_code = (int)$response_code;
		return($this);
	}
	
	public function set_headers(array $headers) {
		$this->headers = $headers;
		return($this);
	}

}