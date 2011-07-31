<?php namespace jolt;
declare(encoding='UTF-8');

require_once(__DIR__.'/request.php');
require_once(__DIR__.'/response.php');

class app {

	public $request = null;
	public $response = null;

	public function __construct() {
		$this->request = new request;
		$this->response = new response;
	}
	
	public function __destruct() {
		
	}


	
}