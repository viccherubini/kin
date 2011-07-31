<?php namespace jolt;
declare(encoding='UTF-8');

require_once(__DIR__.'/exceptions/unrecoverable.php');

class dispatcher {

	private $arguments = array();

	private $action = null;
	private $controller = null;

	public function __construct() {
		
	}
	
	public function __destruct() {
		
	}
	
	
	
	public function attach_controller(controller $controller) {
		$this->controller = $controller;
		return $this;
	}
	
	
	
	public function dispatch() {
		if (is_null($this->controller)) {
			throw new \jolt\exception\unrecoverable("The dispatcher must have a controller object attached before it can begin dispatching.");
		}
		
		if (empty($this->action)) {
			throw new \jolt\exception\unrecoverable("The dispatcher must have a controller action set before it can begin dispatching.");
		}

		$class = get_class($this->controller);
		try {
			$action = new \ReflectionMethod($this->controller, $this->action);
		} catch (\ReflectionException $e) {
			throw new \jolt\exception\unrecoverable("The controller action, {$this->action}, is not a member of the controller class {$class}.");
		}
		
		if (!$action->isPublic()) {
			throw new \jolt\exception\unrecoverable("The controller action, {$this->action}, is not a public member of the controller class {$class}.");
		}
		
		try {
			$action_value = $action->invokeArgs($this->controller, $this->arguments);
		} catch (\Exception $e) {
			throw new \jolt\exception\unrecoverable("The controller action, {$this->action}, threw an exception that was not caught: ".$e->getMessage());
		}
		
		return $action_value;
	}
	
	
	
	public function set_action($action) {
		$this->action = trim($action);
		return $this;
	}

	public function set_arguments(array $arguments) {
		$this->arguments = $arguments;
		return $this;
	}
	
	
	
	public function get_controller() {
		return $this->controller;
	}
	
}