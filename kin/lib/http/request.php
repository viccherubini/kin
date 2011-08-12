<?php namespace kin\http;
declare(encoding='UTF-8');

class request {

	private $accept = 'text/html';
	private $path = '/';
	private $method = 'GET';
	private $type = 'html';

	private $stream_contents = array();

	public function __construct() {
		$stream_data = file_get_contents('php://input');
		if (!empty($stream_data)) {
			parse_str($stream_data, $this->stream_contents);
		}
	}
	
	
	
	public function get($key, $default=null) {
		return($this->find_array_value_by_key($key, $_GET, $default));
	}
	
	public function post($key, $default=null) {
		return($this->find_array_value_by_key($key, $_POST, $default));
	}

	public function put($key, $default=null) {
		return($this->find_array_value_by_key($key, $this->stream_contents, $default));
	}
	
	public function delete($key, $default=null) {
		return($this->find_array_value_by_key($key, $this->stream_contents, $default));
	}
	
	public function server($key, $default=null) {
		return($this->find_array_value_by_key($key, $_SERVER, $default));
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
	
	public function get_stream_contents() {
		return($this->stream_contents);
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
	
	private function find_array_value_by_key($key, $array, $default) {
		return(array_key_exists($key, $array) ? $array[$key] : $default);
	}
	
}