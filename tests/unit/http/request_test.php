<?php namespace kintest\http;
use \kin\http\request as request,
	\kintest\testcase as testcase;

require_once(__DIR__.'/../../testcase.php');
require_once(__DIR__.'/../../../kin/lib/http/request.php');

class request_test extends testcase {
	
	public function test_get__finds_value_by_key_in_superglobal() {
		$id = $_GET['id'] = mt_rand(1, 10000);
		
		$request = new request;
		
		$this->assertEquals($id, $request->get('id'));
	}
	
	public function test_get__returns_default_value_when_key_not_found() {
		$default_id = 0;
		
		$request = new request;
		
		$this->assertEquals(0, $request->get('id', $default_id));
	}
	
	
	
	public function test_post__finds_value_by_key_in_superglobal() {
		$id = $_POST['id'] = mt_rand(1, 10000);
		
		$request = new request;
		
		$this->assertEquals($id, $request->post('id'));
	}
	
	public function test_post__returns_default_value_when_key_not_found() {
		$default_id = 0;
		
		$request = new request;
		
		$this->assertEquals(0, $request->post('id', $default_id));
	}
	
	
	
	public function test_server__returns_value_by_key_in_superglobal() {
		$id = $_SERVER['id'] = mt_rand(1, 10000);
		
		$request = new request;
		
		$this->assertEquals($id, $request->server('id'));
	}
	
	public function test_server__returns_default_value_when_key_not_found() {
		$default_id = 0;
		
		$request = new request;
		
		$this->assertEquals(0, $request->server('id', $default_id));
	}
	
	
	
	/**
	 * @dataProvider provider_accept_headers
	 */
	public function test_set_accept__orders_mime_types_by_quality($accept_header, $acceptable_types) {
		$request = new request;
		$request->set_accept_header($accept_header);
		
		$this->assertEquals($acceptable_types, $request->get_acceptable_types());
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

	
	
	
	public function provider_accept_headers() {
		return array(
			array('', array('text/html')),
			array('*/*', array('text/html')),
			array('*/', array('text/html')),
			array('*/audio', array('audio/audio')),
			array('text/', array('text/html')),
			array('audio/*', array('audio/audio')),
			array('text/html', array('text/html')),
			array('text/plain', array('text/plain')),
			array('application/json', array('application/json')),
			array('audio/*; q=0.2, audio/basic', array('audio/basic', 'audio/audio')),
			array('text/plain; q=0.5, text/html, text/x-dvi; q=0.8, text/x-c', array('text/x-c', 'text/html', 'text/x-dvi', 'text/plain')),
			array('text/html;level=1', array('text/html')),
			array('image/jpeg;q=0.5,text/html,application/json;q=0.75,application/javascript;q=0.8,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8', array('application/xhtml+xml', 'text/html', 'application/xml', 'text/html', 'application/javascript', 'application/json', 'image/jpeg'))
		);
	}

}