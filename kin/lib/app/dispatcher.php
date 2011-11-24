<?php namespace kin;
require_once(__DIR__.'/../exceptions/unrecoverable.php');

class dispatcher {
	
	public $init_successful = true;
	public $arguments = array();
	public $action = null;
	public $class = null;
	public $controller = null;

	public function __construct() { }
	
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
			->parse_request()
			->dispatch_controller_init()
			->dispatch_action($action));
	}
	
	public function is_init_successful() {
		return($this->init_successful);
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
			throw new unrecoverable("The dispatcher must have a controller object attached before it can begin dispatching.");
		}
		return($this);
	}
	
	private function check_action() {
		if (empty($this->action)) {
			throw new unrecoverable("The dispatcher must have a controller action set before it can begin dispatching.");
		}
		return($this);
	}
	
	private function check_action_is_public($action) {
		if (!$action->isPublic()) {
			throw new unrecoverable("The controller action, {$this->action}, is not a public member of the controller class {$this->class}.");
		}
		return($this);
	}
	
	private function build_action() {
		try {
			$action = new \ReflectionMethod($this->controller, $this->action);
		} catch (\ReflectionException $e) {
			throw new unrecoverable("The controller action, {$this->action}, is not a member of the controller class {$this->class}.");
		}
		return($action);
	}
	
	private function parse_request() {
		$this->controller->parse_request();
		return($this);
	}
	
	private function dispatch_controller_init() {
		if (method_exists($this->controller, "init")) {
			$this->init_successful = $this->controller->init();
		}
		return($this);
	}
	
	private function dispatch_controller_shutdown() {
		if (method_exists($this->controller, "shut_down")) {
			return($this->controller->shut_down());
		}
		return(true);
	}
	
	private function dispatch_action($action) {
		if ($this->init_successful) {
			try {
				$action->invokeArgs($this->controller, $this->arguments);
			} catch (\Exception $e) {
				if (method_exists($this->controller, "handle_exception")) {
					$this->controller->handle_exception($e);
				} else {
					throw new unrecoverable("The controller action, {$this->action}, threw an exception that was not caught: ".$e->getMessage());
				}
			}
			$this->dispatch_controller_shutdown();
		}
		return(true);
	}
	
}
