<?php namespace kintest;
declare(encoding='UTF-8');

use \kin\dispatcher as dispatcher;

require_once(__DIR__.'/testcase.php');
require_once(__DIR__.'/../../kin/dispatcher.php');

class dispatcher_test extends testcase {
	
	/**
	 * @expectedException \PHPUnit_Framework_Error
	 */
	public function test_attach_controller__requires_controller_object() {
		$dispatcher = new dispatcher;
		$dispatcher->attach_controller(null);
	}
	
	/**
	 * @expectedException \kin\exception\unrecoverable
	 */
	public function test_dispatch__requires_controller() {
		$dispatcher = new dispatcher;
		$dispatcher->set_action('action_process');
		
		$dispatcher->dispatch();
	}
	
	/**
	 * @expectedException \kin\exception\unrecoverable
	 */
	public function test_dispatch__requires_action() {
		$controller = $this->getMock('kin\controller');
		
		$dispatcher = new dispatcher;
		$dispatcher->attach_controller($controller);
		
		$dispatcher->dispatch();
	}
	
	/**
	 * @expectedException \kin\exception\unrecoverable
	 */
	public function test_dispatch__requires_action_to_exist_in_controller() {
		$controller = $this->getMock('kin\controller');
		
		$dispatcher = new dispatcher;
		$dispatcher->attach_controller($controller)
			->set_action('action_process');
		
		$dispatcher->dispatch();
	}
	
	/**
	 * @expectedException \kin\exception\unrecoverable
	 */
	public function _test_dispatch__requires_action_to_be_public() {
		$action = 'action_process';
		
		$controller = $this->getMock('kin\controller', array($action));
		$controller->expects($this->once())
			->method($action)
			->will($this->returnValue(true));
			
		$method = new \ReflectionMethod($controller, $action);
		$method->setAccessible(false);
		
		$dispatcher = new dispatcher;
		$dispatcher->attach_controller($controller)
			->set_action($action);
		
		$dispatcher->dispatch();
	}
	
	/**
	 * @expectedException \kin\exception\unrecoverable
	 */
	public function test_dispatch__catches_all_uncaught_controller_exceptions() {
		$action = 'action_process';
		
		$controller = $this->getMock('kin\controller', array($action));
		$controller->expects($this->once())
			->method($action)
			->will($this->throwException(new \Exception('Unit Testing Exception')));
		
		$dispatcher = new dispatcher;
		$dispatcher->attach_controller($controller)
			->set_action($action);
		
		$dispatcher->dispatch();
	}
	
	public function test_dispatch__action_returns_argument() {
		$action = 'action_process';
		$arguments = array(15);
		
		$controller = $this->getMock('kin\controller', array($action));
		$controller->expects($this->once())
			->method($action)
			->will($this->returnArgument(0));
			
		$dispatcher = new dispatcher;
		$dispatcher->attach_controller($controller)
			->set_action($action)
			->set_arguments($arguments);
		
		$this->assertEquals($arguments[0], $dispatcher->dispatch());
	}
	
}
