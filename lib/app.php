<?php namespace jolt;
declare(encoding='UTF-8');

require_once(__DIR__.'/request.php');
require_once(__DIR__.'/response.php');
require_once(__DIR__.'/router.php');

class app {

	public $request = null;
	public $response = null;
	public $router = null;

	public function __construct() {
		$this->request = new request;
		$this->response = new response;
		$this->router = new router;
	}
	
	public function __destruct() {
		
	}


	public function execute() {
		$this->request
			->set_accept(filter_input(INPUT_SERVER, 'HTTP_ACCEPT'))
			->set_method(filter_input(INPUT_SERVER, 'REQUEST_METHOD'))
			->set_path(filter_input(INPUT_SERVER, 'PATH_INFO'));
			
		
	}
}