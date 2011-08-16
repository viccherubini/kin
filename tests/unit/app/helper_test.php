<?php namespace kintest\app;
declare(encoding='UTF-8');

use \kin\app\helper as helper,
	\kintest\testcase as testcase;

require_once(__DIR__.'/../../testcase.php');
require_once(__DIR__.'/../../../kin/lib/app/helper.php');

class helper_test extends testcase {
	
	/**
	 * @expectedException \PHPUnit_Framework_Error
	 */
	public function test_attach_settings__requires_settings_object() {
		$helper = new helper;
		$helper->attach_settings(null);
	}
}