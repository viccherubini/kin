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
		$accept_bits = $this->find_first_accept_type($accept);
		if (count($accept_bits) > 0) {
			$accept = $this->parse_out_accept_quality($accept_bits[0]);
			
			$mime_type = array();
			if (preg_match('#(.*)/(.*)#i', $accept, $mime_type)) {
				$this->accept = $accept;
				$this->find_type_from_accept($mime_type);
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
	
	
	
	private function find_first_accept_type($accept) {
		return explode(',', $accept);
	}
	
	private function parse_out_accept_quality($accept) {
		return current(explode(';', $accept));
	}
	
	private function find_type_from_accept($mime_type) {
		if (isset($mime_type[2])) {
			$this->set_type($mime_type[2]);
		}
		return $this;
	}
	
}