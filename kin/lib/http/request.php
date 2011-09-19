<?php namespace kin\http;

class request {

	private $path = '/';
	private $method = 'GET';

	private $acceptable_types = array();
	private $renderable_types = array();
	
	private $stream_contents = array();
	
	const accept_any = '*/*';
	const default_accept = 'text/html';
	const default_type = 'html';

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
	
	
	
	public function set_accept_header($accept_header) {
		$accept_header = strtolower(str_replace(' ', '', $accept_header));
		$acceptable_types = explode(',', $accept_header);

		foreach ($acceptable_types as $accept) {
			if (preg_match('#^(.+)/(.+)$#i', $accept)) {
				$accept_quality = explode(';', $accept);

				$quality = 1.0;
				if (isset($accept_quality[1]) && false !== strpos($accept_quality[1], 'q=')) {
					$quality = (float)str_replace('q=', '', $accept_quality[1]);
				}
				
				$accept = trim($accept_quality[0]);
				if (self::accept_any == $accept) {
					$accept = self::default_accept;
				}
				
				if (false !== strpos($accept, '*')) {
					$accept_bits = explode('/', $accept);
					if ('*' == $accept_bits[0]) {
						$accept_bits[0] = $accept_bits[1];
					} elseif ('*' == $accept_bits[1]) {
						$accept_bits[1] = $accept_bits[0];
					}
					$accept = implode('/', $accept_bits);
				}
			} else {
				$quality = 0.0;
				$accept = self::default_accept;
			}
			
			$this->acceptable_types[] = array($accept, $quality);
		}
		
		usort($this->acceptable_types, function($a, $b) {
			if ($a[1] == $b[1]) {
				return(0);
			}
			return($a[1] > $b[1] ? -1 : 1);
		});
		
		$this->acceptable_types = array_map(function($v) {
			return($v[0]);
		}, $this->acceptable_types);
		
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



	public function get_acceptable_types() {
		return($this->acceptable_types);
	}
	
	public function get_method() {
		return($this->method);
	}
	
	public function get_path() {
		return($this->path);
	}
	
	public function get_stream_contents() {
		return($this->stream_contents);
	}
	
	

	private function find_array_value_by_key($key, $array, $default) {
		return(array_key_exists($key, $array) ? $array[$key] : $default);
	}
	
}