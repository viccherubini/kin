<?php namespace kintest;
declare(encoding='UTF-8');

use \jolt\app as app;

require_once(__DIR__.'/testcase.php');
require_once(__DIR__.'/../../kin/app.php');

class app_test extends testcase {
	
	public function test_true() {
		$this->assertTrue(true);
	}
	
}
