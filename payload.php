<?php namespace jolt;
declare(encoding='UTF-8');

class payload {

	public $payload = array();

	public function __construct() {
		$this->payload = array(
			'content' => '',
			'errors' => array(),
			'message' => '',
			'model' => array(),
			'name' => '',
			'object' => NULL,
			'redirect' => '',
			'token' => ''
		);
	}
	
	public function __destruct() {
		$this->payload = array();
	}
	
	public function __set($k, $v) {
		$this->payload[$k] = $v;
		return $this;
	}

	public function __get($k) {
		if (array_key_exists($k, $this->payload)) {
			return $this->payload[$k];
		}
		return null;
	}
	
	public function __call($method, $argv) {
		if (0 === count($argv)) {
			return $this->__get($method);
		} else {
			return $this->__set($method, current($argv));
		}
	}
	
}