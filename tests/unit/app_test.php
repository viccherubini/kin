<?php namespace kin;
require_once(__DIR__."/../testcase.php");
require_once(__DIR__."/../../kin/lib/app.php");

class app_test extends testcase {
	
	public function test___construct__builds_response() {
		$app = new app;
		
		$this->assertInternalType("object", $app->response);
	}
	
	public function test_attach_settings__compiles_settings() {
		$settings = new settings;
		$settings->app_path = __DIR__;
		
		$app = new app;
		$app->attach_settings($settings);
		
		$this->assertNotEmpty($settings->controllers_path);
	}
	
	public function test_run__compiles_request() {
		$settings = $this->getMock("\\kin\\settings", array(), array(), "", true);
		
		$app = new app;
		$app->attach_settings($settings);
		
		$app->run();
		
		$this->assertInternalType("object", $app->request);
	}
	
	public function test_run__requires_settings() {
		$app = new app;
		$app->run();
		
		$this->assertInternalType("null", $app->get_route());
	}
	
}
