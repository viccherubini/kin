<?php namespace kintest\http;
declare(encoding='UTF-8');

use \kin\http\request as request,
	\kintest\testcase as testcase;

require_once(__DIR__.'/../../testcase.php');
require_once(__DIR__.'/../../../kin/lib/http/request.php');

class request_test extends testcase {
	
	/**
	 * @dataProvider provider_accept_headers
	 */
	public function test_set_accept__normalizes_accept_format($actual_header, $normalized_header) {
		$request = new request;
		$request->set_accept($actual_header);
		
		$this->assertEquals($normalized_header, $request->get_accept());
	}
	
	/**
	 * @dataProvider provider_accept_headers_and_type
	 */
	public function test_set_accept__sets_type($actual_header, $type) {
		$request = new request;
		$request->set_accept($actual_header);
		
		$this->assertEquals($type, $request->get_type());
	}
	
	public function test_set_method__always_uppercase() {
		$request = new request;
		$request->set_method('post');
		
		$this->assertEquals('POST', $request->get_method());
	}
	
	public function test_set_method__can_not_be_empty() {
		$request = new request;
		$expected_method = $request->get_method();
		
		$request->set_method('');
		$this->assertEquals($expected_method, $request->get_method());
	}
	
	public function test_set_path__cannot_be_empty() {
		$request = new request;
		$request->set_path('');
		
		$this->assertNotEquals('', $request->get_path());
	}
	
	public function test_set_type__cannot_be_star() {
		$request = new request;
		$request->set_type('*');
		
		$this->assertNotEquals('*', $request->get_type());
	}
	
	
	
	public function provider_accept_headers() {
		return array(
			array('', 'text/html'), // Default value
			array('text/', 'text/html'), // Malformed header
			array('*/*', '*/*'),
			array('text/html', 'text/html'),
			array('text/plain', 'text/plain'),
			array('application/json', 'application/json'),
			array('audio/*; q=0.2, audio/basic', 'audio/*'),
			array('text/plain; q=0.5, text/html, text/x-dvi; q=0.8, text/x-c', 'text/plain'),
			array('text/html;level=1', 'text/html')
		);
	}
	
	public function provider_accept_headers_and_type() {
		return array(
			array('', 'html'),
			array('text/', 'html'),
			array('*/*', 'html'),
			array('text/html', 'html'),
			array('text/plain', 'plain'),
			array('application/json', 'json'),
			array('audio/*; q=0.2, audio/basic', 'html'),
			array('text/plain; q=0.5, text/html, text/x-dvi; q=0.8, text/x-c', 'plain'),
			array('text/html;level=1', 'html')
		);
	}
	
}