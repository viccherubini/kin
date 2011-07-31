<?php namespace jolt;
declare(encoding='UTF-8');

class request {

	private $accept = 'text/html';
	private $path = '/';
	private $method = 'GET';
	private $type = 'html';

	public function __construct() {
	
	}
	
	public function __destruct() {
	
	}
	
	

	public function set_accept($accept) {
		// Ignore multiple accept types
		$accept_bits = explode(',', $accept);
		if (count($accept_bits) > 0) {
			// Just get the first type, ignore the quality
			$accept_type = current(explode(';', $accept_bits[0]));
			
			$mime_type_bits = array();
			if (preg_match('#(.*)/(.*)#i', $accept_type, $mime_type_bits)) {
				$this->accept = $accept_type;
				$this->set_type($mime_type_bits[2]);
			}
		}
		return $this;
	}
	
	public function set_method($method) {
		$this->method = strtoupper($method);
		return $this;
	}
	
	public function set_path($path) {
		if (!empty($path)) {
			$this->path = $path;
		}
		return $this;
	}
	
	public function set_type($type) {
		$type = trim($type);
		if ('*' !== $type) {
			$this->type = $type;
		}
		return $this;
	}



	public function get_accept() {
		return $this->accept;
	}
	
	public function get_method() {
		return $this->method;
	}
	
	public function get_path() {
		return $this->path;
	}
	
	public function get_type() {
		return $this->type;
	}
	
}