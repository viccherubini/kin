<?php namespace kin;
require_once(__DIR__."/../../testcase.php");
require_once(__DIR__."/../../../kin/lib/app/controller.php");

class controller_test extends testcase {
	
	public function test___set__adds_to_payload() {
		$value = uniqid();
		
		$controller = new controller;
		$controller->field = $value;
		
		$payload = $controller->get_payload();
		$this->assertEquals($value, $payload["field"]);
	}
	
	public function test___get__returns_from_payload() {
		$value = uniqid();
		
		$controller = new controller;
		$controller->field = $value;
		
		$this->assertEquals($value, $controller->field);
	}
	
	public function test_add_header__header_cannot_be_content_type() {
		$content_type = "text/html";
		
		$controller = new controller;
		$controller->add_header("content-type", $content_type);
		
		$this->assertEmpty($controller->get_headers());
		$this->assertEquals($content_type, $controller->get_content_type());
	}
	
	public function test_add_header__header_is_lowercase() {
		$header_uppercase = "X-KIN-VERSION";
		$header_lowercase = strtolower($header_uppercase);
		$value = "1.0.0";
		
		$controller = new controller;
		$controller->add_header($header_uppercase, $value);
		
		$headers = $controller->get_headers();
		$this->assertEquals($header_lowercase, key($headers));
	}
	
	public function test_has_content_type__is_true_when_content_type_set() {
		$controller = new controller;
		$controller->set_content_type("text/xml");
		
		$this->assertTrue($controller->has_content_type());
	}
	
	public function test_redirect__adds_location_header() {
		$controller = new controller;
		$controller->redirect("http://leftnode.com/");
		
		$this->assertArrayHasKey("location", $controller->get_headers());
	}
	
	public function test_redirect__sets_302_response_code_by_default() {
		$controller = new controller;
		$controller->redirect("http://leftnode.com/");
		
		$this->assertEquals(controller::response_302, $controller->get_response_code());
	}
	
	public function test_redirect__allows_301_response_code() {
		$controller = new controller;
		$controller->redirect("http://leftnode.com/", controller::response_301);
		
		$this->assertEquals(controller::response_301, $controller->get_response_code());
	}
	
	public function test_redirect__does_not_allow_response_code_other_than_302_or_301() {
		$controller = new controller;
		$controller->redirect("http://leftnode.com/", 405);
		
		$this->assertEquals(controller::response_302, $controller->get_response_code());
	}
	
	public function test_register__adds_to_payload() {
		$field = "password";
		$value = uniqid();
		
		$controller = new controller;
		$controller->register($field, $value);
		
		$payload = $controller->get_payload();
		$this->assertEquals($value, $payload[$field]);
	}
	
}
