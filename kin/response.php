<?php namespace kin;
declare(encoding='UTF-8');

class response {

	private $content_type = 'text/html';
	private $response_code = 200;
	
	private $headers = array();
	private $content = null;

	public function __construct() {
		
	}
	
	
	
	public function respond() {
		if (0 === count($this->headers)) {
			$this->headers = headers_list();
		}
		
		header_remove('content-type');
		header('content-type: '.$this->content_type, true, $this->response_code);
		
		foreach ($this->headers as $header => $value) {
			$full_header = implode(': ', array($header, $value));
			
			header_remove($header);
			header($full_header, true, $this->response_code);
		}
		
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