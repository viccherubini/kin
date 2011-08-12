<?php namespace kin\http;
declare(encoding='UTF-8');

class request {

	private $accept = 'text/html';
	private $path = '/';
	private $method = 'GET';
	private $type = 'html';

	public function __construct() {
	
	}
	
	
	public function get($key, $default=null, $expected=array()) {
		return($this->get_superglobal_value($key, \INPUT_GET, $default, $expected));
	}
	
	public function post($key, $default=null, $expected=array()) {
		return($this->get_superglobal_value($key, \INPUT_POST, $default, $expected));
	}

	public function put($key, $default=null, $expected=array()) {
	}
	
	public function delete($key, $default=null, $expected=array()) {
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
		$method = trim($method);
		if (!empty($method)) {
			$this->method = strtoupper($method);
		}
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
	
	private function get_superglobal_value($key, $superglobal, $default, $expected) {
		$return = $default;
		
		if (filter_has_var($superglobal, $key)) {
			$return = filter_input($superglobal, $key);
			
			if (is_int($default)) {
				$return = (int)$return;
			} elseif (is_float($default)) {
				$return = (float)$return;
			} elseif (is_array($default)) {
				$return = (array)$return;
				
				if (is_array($expected) && count($expected) > 0) {
					$return = array_merge($expected, $return);
				}
			}
		}
		
		return($return);
	}
	
}