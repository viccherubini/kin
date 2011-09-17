<?php namespace kintest\http;
declare(encoding='UTF-8');

use \kin\http\response as response,
	\kintest\testcase as testcase;

require_once(__DIR__.'/../../testcase.php');
require_once(__DIR__.'/../../../kin/lib/http/response.php');

class response_test extends testcase {
	
	public function test_respond__returns_empty_response_with_location_header_and_correct_response_code() {
		$response = new response;
		$response->set_headers(array('location' => 'http://leftnode.com'));
		$response->set_response_code(301);
		$response->set_content('<strong>You will never see this!</strong>');
		
		$this->assertEmpty($response->respond());
	}
	
	public function test_respond__returns_nonempty_response_without_correct_response_code() {
		$response = new response;
		$response->set_headers(array('location' => 'http://leftnode.com'));
		$response->set_response_code(200);
		$response->set_content('<strong>You will never see this!</strong>');
		
		$this->assertNotEmpty($response->respond());
	}
	
}