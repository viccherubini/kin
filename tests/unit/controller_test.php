<?php namespace kintest;
declare(encoding='UTF-8');

use \kin\controller as controller;

require_once(__DIR__.'/testcase.php');
require_once(__DIR__.'/../../kin/controller.php');

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
		$this->assertEquals($payload['errors'][$field], $error);
	}
	
	
	
	/**
	 * @expectedException \PHPUnit_Framework_Error
	 */
	public function test_add_model__requires_model_object() {
		$controller = new controller;
		
		$controller->add_model('hello, world');
	}
	
	public function test_add_model__adds_model_name() {
		$model_class = 'kin\model';
		
		$model = $this->getMock($model_class, array('get_values'));
		$model->expects($this->once())
			->method('get_values')
			->will($this->returnValue(array()));
		
		$controller = new controller;
		$controller->add_model($model);
		
		$payload = $controller->get_payload();
		$this->assertEquals($model_class, get_parent_class($payload['models'][0][0]));
	}
	
	public function test_add_model__adds_model_values() {
		$model_class = 'kin\model';
		$model_values = array('id' => mt_rand(1, 1000), 'password' => uniqid());
		
		$model = $this->getMock($model_class, array('get_values'));
		$model->expects($this->once())
			->method('get_values')
			->will($this->returnValue($model_values));
		
		$controller = new controller;
		$controller->add_model($model);
		
		$payload = $controller->get_payload();
		$this->assertEquals($model_values, $payload['models'][0][1]);
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
	
	public function test_register__adds_to_contents() {
		$field = 'password';
		$value = uniqid();
		
		$controller = new controller;
		$controller->register($field, $value);
		
		$payload = $controller->get_payload();
		$this->assertEquals($value, $payload['contents'][$field]);
	}
	
}
