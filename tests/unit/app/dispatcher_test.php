<?php namespace kin;
require_once(__DIR__.'/../../testcase.php');
require_once(__DIR__.'/../../../kin/lib/app/dispatcher.php');

class dispatcher_test extends testcase {
	
	/**
	 * @expectedException \PHPUnit_Framework_Error
	 */
	public function test_attach_controller__requires_controller_object() {
		$dispatcher = new dispatcher;
		$dispatcher->attach_controller(null);
	}
	
	/**
	 * @expectedException \kin\unrecoverable
	 */
	public function test_dispatch__requires_controller() {
		$dispatcher = new dispatcher;
		$dispatcher->set_action('action_process');
		
		$dispatcher->dispatch();
	}
	
	/**
	 * @expectedException \kin\unrecoverable
	 */
	public function test_dispatch__requires_action() {
		$controller = $this->getMock('kin\controller');
		
		$dispatcher = new dispatcher;
		$dispatcher->attach_controller($controller);
		
		$dispatcher->dispatch();
	}
	
	/**
	 * @expectedException \kin\unrecoverable
	 */
	public function test_dispatch__requires_action_to_exist_in_controller() {
		$controller = $this->getMock('kin\controller');
		
		$dispatcher = new dispatcher;
		$dispatcher->attach_controller($controller)
			->set_action('action_process');
		
		$dispatcher->dispatch();
	}
	
	public function test_dispatch__executes_init_method() {
		$controller = $this->getMock('kin\controller', array('parse_request', 'action_process', 'init'));
		$controller->expects($this->any())
			->method('parse_request')
			->will($this->returnValue(true));
		$controller->expects($this->any())
			->method('action_process')
			->will($this->returnValue(true));
			
		$controller->expects($this->once())
			->method('init')
			->will($this->returnValue(true));
		
		$dispatcher = new dispatcher;
		$dispatcher->attach_controller($controller)
			->set_action('action_process');
		
		$dispatcher->dispatch();
		
		$this->assertTrue($dispatcher->is_init_successful());
	}
	
	/**
	 * @expectedException \kin\unrecoverable
	 */
	public function test_dispatch__catches_all_uncaught_controller_exceptions() {
		$controller = $this->getMock('kin\controller', array('parse_request', 'action_process'));
		$controller->expects($this->any())
			->method('parse_request')
			->will($this->returnValue(true));
		$controller->expects($this->once())
			->method('action_process')
			->will($this->throwException(new \Exception('Unit Testing Exception')));
		
		$dispatcher = new dispatcher;
		$dispatcher->attach_controller($controller)
			->set_action('action_process');
		
		$dispatcher->dispatch();
	}
	
	public function test_dispatch__action_returns_false_when_shut_down_fails_to_execute() {
		$controller = $this->getMock('kin\controller', array('parse_request', 'action_process', 'shut_down'));
		$controller->expects($this->any())
			->method('parse_request')
			->will($this->returnValue(true));
		$controller->expects($this->once())
			->method('action_process')
			->will($this->returnArgument(0));
		$controller->expects($this->once())
			->method('shut_down')
			->will($this->returnValue(false));
			
		$dispatcher = new dispatcher;
		$dispatcher->attach_controller($controller)
			->set_action('action_process')
			->set_arguments(array(15));
		
		$this->assertFalse($dispatcher->dispatch());
	}
	
	public function test_dispatch__action_returns_true_when_action_successfully_executes() {
		$controller = $this->getMock('kin\controller', array('parse_request', 'action_process'));
		$controller->expects($this->any())
			->method('parse_request')
			->will($this->returnValue(true));
		$controller->expects($this->once())
			->method('action_process')
			->will($this->returnArgument(0));
			
		$dispatcher = new dispatcher;
		$dispatcher->attach_controller($controller)
			->set_action('action_process')
			->set_arguments(array(15));
		
		$this->assertTrue($dispatcher->dispatch());
	}
	
}
