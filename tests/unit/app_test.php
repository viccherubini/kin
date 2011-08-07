<?php namespace kintest;
declare(encoding='UTF-8');

use \kin\app as app;

require_once(__DIR__.'/testcase.php');
require_once(__DIR__.'/../../kin/app.php');

class app_test extends testcase {
	
	public function test___construct__compiles_request() {
		$app = new app;
		
		$this->assertInternalType('object', $app->request);
	}
	
	public function test___construct__builds_response() {
		$app = new app;
		
		$this->assertInternalType('object', $app->response);
	}
	
}