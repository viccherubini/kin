<?php namespace jolt_test;
declare(encoding='UTF-8');

use \jolt\app as app;

require_once(__DIR__.'/testcase.php');
require_once(__DIR__.'/../lib/app.php');

class app_test extends testcase {
	
	public function test___construct__builds_request() {
		$app = new app;
		$this->assertInstanceOf('\jolt\request', $app->request);
	}
	
	public function test___construct__builds_response() {
		$app = new app;
		$this->assertInstanceOf('\jolt\response', $app->response);
	}
	
	public function test___construct__builds_router() {
		$app = new app;
		$this->assertInstanceOf('\jolt\router', $app->router);
	}
	
	
	
}