<?php namespace kin;

class simple_object {
	
	private $simple = array();
	
	public function __construct($array)
		$this->simple = $array;
	}
	
	public function __destruct() {
		
	}
	
	public function __get($k) {
		return(array_key_exists($k, $this->simple) ? $this->simple[$k] : null);
	}
	
	public function __set($k, $v) {
		$this->simple[$k] = $v;
	}
	
}