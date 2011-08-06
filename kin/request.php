<?php namespace kin;
declare(encoding='UTF-8');

class request {

	private $accept = 'text/html';
	private $path = '/';
	private $method = 'GET';
	private $type = 'html';

	public function __construct() {
	
	}
	
	

	public function set_accept($accept) {
		$accept_bits = $this->find_first_accept_type($accept);
		if (count($accept_bits) > 0) {
			$accept = $this->parse_out_accept_quality($accept_bits[0]);
			$accept = $this->format_accept_type($accept);
			$this->find_type_from_accept($accept);
			
			$this->accept = $accept;
		}
		return($this);
	}
	
	public function set_method($method) {
		$this->method = strtoupper($method);
		return($this);
	}
	
	public function set_path($path) {
		if (!empty($path)) {
			$this->path = $path;
		}
		return($this);
	}
	
	public function set_type($type) {
		$type = trim($type);
		if ('*' !== $type) {
			$this->type = $type;
		}
		return($this);
	}



	public function get_accept() {
		return($this->accept);
	}
	
	public function get_method() {
		return($this->method);
	}
	
	public function get_path() {
		return($this->path);
	}
	
	public function get_type() {
		return($this->type);
	}
	
	
	
	private function find_first_accept_type($accept) {
		return(explode(',', $accept));
	}
	
	private function parse_out_accept_quality($accept) {
		return(current(explode(';', $accept)));
	}
	
	private function format_accept_type($accept) {
		if (preg_match('#(.+)/(.+)#i', $accept)) {
			return($accept);
		}
		return($this->accept);
	}
	
	private function find_type_from_accept($accept) {
		$accept_bits = explode('/', $accept);
		if (isset($accept_bits[1])) {
			$this->set_type($accept_bits[1]);
		}
		return($this);
	}
	
}