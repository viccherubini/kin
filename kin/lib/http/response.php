<?php namespace kin\http;

class response {

	public $start_time = 0.0;
	public $response_code = 200;
	
	public $content_type = "";
	
	public $headers = array();
	public $content = null;
	
	

	public function __construct() {
		
	}
	
	
	
	public function respond() {
		header_remove("Content-Type");
		header("Content-Type: ".$this->content_type, true, $this->response_code);
		
		$found_location_header = false;
		foreach ($this->headers as $header => $value) {
			$full_header = implode(": ", array($header, $value));
			
			header_remove($header);
			header($full_header, true, $this->response_code);
			
			if ("location" === strtolower($header)) {
				$found_location_header = true;
			}
		}
		
		header("X-Memory-Usage: ".round((memory_get_peak_usage()/1024), 3)."KB");
		if ($this->start_time > 0) {
			header("X-Exec-Time: ".round((microtime(true)-$this->start_time), 5)."s");
		}
		
		if ($found_location_header && in_array($this->response_code, array(301, 302))) {
			$this->content = "";
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
	
	public function set_start_time($start_time) {
		$this->start_time = (float)$start_time;
		return($this);
	}
	
	
	public function get_content_type() {
		return($this->content_type);
	}
	
	public function get_response_code() {
		return($this->response_code);
	}

}