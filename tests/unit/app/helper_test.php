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
	
	
	
	public function test_css__appends_extension_only_if_local_file() {
		$settings = $this->getMock('\kin\app\settings');
		
		$helper = new helper;
		$helper->attach_settings($settings);
		
		$css_file = '/path/to/css';
		$css_file_with_extension = $css_file.'.css';
		
		$link_tag = $helper->css($css_file);
		
		$matcher = array(
			'tag' => 'link',
			'attributes' => array('href' => $css_file_with_extension)
		);
		
		$this->assertTag($matcher, $link_tag);
	}
	
}