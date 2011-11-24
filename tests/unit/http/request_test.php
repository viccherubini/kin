<?php namespace kin;
require_once(__DIR__."/../../testcase.php");
require_once(__DIR__."/../../../kin/lib/http/request.php");

class request_test extends testcase {
	
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
		$request->set_method("post");
		
		$this->assertEquals("POST", $request->get_method());
	}
	
	public function test_set_method__can_not_be_empty() {
		$request = new request;
		$expected_method = $request->get_method();
		
		$request->set_method("");
		$this->assertEquals($expected_method, $request->get_method());
	}
	
	public function test_set_path__cannot_be_empty() {
		$request = new request;
		$request->set_path("");
		
		$this->assertNotEquals("", $request->get_path());
	}
	

	
	public function provider_accept_headers() {
		return array(
			array("", array("text/html")),
			array("*/*", array("text/html")),
			array("*/", array("text/html")),
			array("*/audio", array("audio/audio")),
			array("text/", array("text/html")),
			array("audio/*", array("audio/audio")),
			array("text/html", array("text/html")),
			array("text/plain", array("text/plain")),
			array("application/json", array("application/json")),
			array("audio/*; q=0.2, audio/basic", array("audio/basic", "audio/audio")),
			array("text/plain; q=0.5, text/html, text/x-dvi; q=0.8, text/x-c", array("text/x-c", "text/html", "text/x-dvi", "text/plain")),
			array("text/html;level=1", array("text/html")),
			array("image/jpeg;q=0.5,text/html,application/json;q=0.75,application/javascript;q=0.8,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8", array("application/xhtml+xml", "text/html", "application/xml", "text/html", "application/javascript", "application/json", "image/jpeg"))
		);
	}

}
