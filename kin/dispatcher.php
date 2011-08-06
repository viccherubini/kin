<?php namespace kin;
declare(encoding='UTF-8');

require_once(__DIR__.'/exceptions/unrecoverable.php');

class dispatcher {

	private $arguments = array();

	private $action = null;
	private $class = null;
	private $controller = null;

	public function __construct() {
		
	}
	
	
	
	public function attach_controller(controller $controller) {
		$this->controller = $controller;
		$this->class = get_class($controller);
		return($this);
	}
	
	public function dispatch() {
		$this->check_controller()
			->check_action();
		
		$action = $this->build_action();
		return($this->check_action_is_public($action)
			->dispatch_action($action));
	}
	
	
	
	public function set_action($action) {
		$this->action = trim($action);
		return($this);
	}

	public function set_arguments(array $arguments) {
		$this->arguments = $arguments;
		return($this);
	}
	
	
	
	public function get_controller() {
		return($this->controller);
	}
	
	
	
	private function check_controller() {
		if (is_null($this->controller)) {
			throw new \kin\exception\unrecoverable("The dispatcher must have a controller object attached before it can begin dispatching.");
		}
		return($this);
	}
	
	private function check_action() {
		if (empty($this->action)) {
			throw new \kin\exception\unrecoverable("The dispatcher must have a controller action set before it can begin dispatching.");
		}
		return($this);
	}
	
	private function check_action_is_public($action) {
		if (!$action->isPublic()) {
			throw new \kin\exception\unrecoverable("The controller action, {$this->action}, is not a public member of the controller class {$this->class}.");
		}
		return($this);
	}
	
	private function build_action() {
		try {
			$action = new \ReflectionMethod($this->controller, $this->action);
		} catch (\ReflectionException $e) {
			throw new \kin\exception\unrecoverable("The controller action, {$this->action}, is not a member of the controller class {$this->class}.");
		}
		return($action);
	}
	
	private function dispatch_action($action) {
		try {
			return($action->invokeArgs($this->controller, $this->arguments));
		} catch (\Exception $e) {
			throw new \kin\exception\unrecoverable("The controller action, {$this->action}, threw an exception that was not caught: ".$e->getMessage());
		}
	}
	
}