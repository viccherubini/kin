<?php namespace kintest;
declare(encoding='UTF-8');

use \kin\app as app;

require_once(__DIR__.'/../testcase.php');
require_once(__DIR__.'/../../kin/lib/app.php');

class app_test extends testcase {
	
	public function test___construct__compiles_request() {
		$app = new app;
		
		$this->assertInternalType('object', $app->request);
	}
	
	public function test___construct__builds_response() {
		$app = new app;
		
		$this->assertInternalType('object', $app->response);
	}
	
	
	
	public function test_attach_settings__compiles_settings() {
		$settings = new \kin\settings;
		$settings->app_path = __DIR__;
		
		$app = new app;
		$app->attach_settings($settings);
		
		$this->assertNotEmpty($settings->controllers_path);
	}
	
}