<?php namespace kintest\app;
declare(encoding='UTF-8');

use \kin\app\controller as controller,
	\kintest\testcase as testcase;

require_once(__DIR__.'/../../testcase.php');
require_once(__DIR__.'/../../../kin/lib/app/controller.php');

class controller_test extends testcase {
	
	public function test___set__adds_to_contents() {
		$value = uniqid();
		
		$controller = new controller;
		$controller->field = $value;
		
		$payload = $controller->get_payload();
		$this->assertEquals($value, $payload['contents']['field']);
	}
	
	
	
	public function test___get__returns_from_contents() {
		$value = uniqid();
		
		$controller = new controller;
		$controller->field = $value;
		
		$this->assertEquals($value, $controller->field);
	}
	
	
	
	public function test_add_header__header_cannot_be_content_type() {
		$content_type = 'text/html';
		
		$controller = new controller;
		$controller->add_header('content-type', $content_type);
		
		$this->assertEmpty($controller->get_headers());
		$this->assertEquals($content_type, $controller->get_content_type());
	}
	
	public function test_add_header__header_is_lowercase() {
		$header_uppercase = 'X-JOLT-VERSION';
		$header_lowercase = strtolower($header_uppercase);
		$value = '1.0.0';
		
		$controller = new controller;
		$controller->add_header($header_uppercase, $value);
		
		$headers = $controller->get_headers();
		$this->assertEquals($header_lowercase, key($headers));
	}
	
	
	
	public function test_add_error__error_added_to_stack() {
		$field = 'username';
		$error = 'Username can not be empty';
		
		$controller = new controller;
		$controller->add_error($field, $error);
		
		$payload = $controller->get_payload();
		$this->assertEquals($payload['errors']['errors'][$field], $error);
	}
	
	public function test_add_error__toggles_has_errors_flag() {
		$controller = new controller;
		
		$controller->add_error('username', 'Username can not be empty');
		
		$this->assertTrue($controller->has_errors());
	}
	
	
	
	public function test_add_error_contents__toggles_has_errors_flag() {
		$controller = new controller;
		
		$controller->add_error_contents(array('name' => 'Kin Framework'));
		
		$this->assertTrue($controller->has_errors());
	}
	
	
	
	/**
	 * @expectedException \PHPUnit_Framework_Error
	 */
	public function test_add_model__requires_model_object() {
		$controller = new controller;
		
		$controller->add_model('hello, world');
	}
	
	public function test_add_model__adds_model_values() {
		$model_values = array('id' => mt_rand(1, 1000), 'password' => uniqid());
		
		$model = $this->getMock('\kin\db\model', array('get_values'));
		$model->expects($this->once())
			->method('get_values')
			->will($this->returnValue($model_values));
		
		$controller = new controller;
		$controller->add_model($model);
		
		$payload = $controller->get_payload();
		$this->assertEquals($model_values, $payload['models'][0]);
	}
	
	
	
	/**
	 * @expectedException \PHPUnit_Framework_Error
	 */
	public function test_add_models__requires_array() {
		$controller = new controller;
		
		$controller->add_models(null);
	}
	
	
	
	public function test_has_content_type__is_true_when_content_type_set() {
		$controller = new controller;
		$controller->set_content_type('text/xml');
		
		$this->assertTrue($controller->has_content_type());
	}
	
	
	
	public function test_redirect__adds_location_header() {
		$controller = new controller;
		$controller->redirect('http://leftnode.com/');
		
		$this->assertArrayHasKey('location', $controller->get_headers());
	}
	
	public function test_redirect__sets_302_response_code_by_default() {
		$controller = new controller;
		$controller->redirect('http://leftnode.com/');
		
		$this->assertEquals(controller::response_302, $controller->get_response_code());
	}
	
	public function test_redirect__allows_301_response_code() {
		$controller = new controller;
		$controller->redirect('http://leftnode.com/', controller::response_301);
		
		$this->assertEquals(controller::response_301, $controller->get_response_code());
	}
	
	public function test_redirect__does_not_allow_response_code_other_than_302_or_301() {
		$controller = new controller;
		$controller->redirect('http://leftnode.com/', 405);
		
		$this->assertEquals(controller::response_302, $controller->get_response_code());
	}
	
	
	
	public function test_register__adds_to_contents() {
		$field = 'password';
		$value = uniqid();
		
		$controller = new controller;
		$controller->register($field, $value);
		
		$payload = $controller->get_payload();
		$this->assertEquals($value, $payload['contents'][$field]);
	}
	
}