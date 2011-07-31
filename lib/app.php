<?php namespace jolt;
declare(encoding='UTF-8');

require_once(__DIR__.'/compiler.php');
require_once(__DIR__.'/request.php');
require_once(__DIR__.'/response.php');
require_once(__DIR__.'/router.php');

class app {

	public $compiler = null;
	public $request = null;
	public $response = null;
	public $router = null;

	public function __construct() {
		$this->compiler = new compiler;
		$this->request = new request;
		$this->response = new response;
		$this->router = new router;
	}
	
	public function __destruct() {
		
	}


	public function execute() {
		$this->request
			->set_accept(filter_input(INPUT_SERVER, 'HTTP_ACCEPT'))
			->set_method(filter_input(INPUT_SERVER, 'REQUEST_METHOD'))
			->set_path(filter_input(INPUT_SERVER, 'PATH_INFO'));
			
		// Build the request object
		// Build the router object and attach the routes and request data
		// Route the data
		// Build the compiler and set the data from the route
		// Build the dispatcher and set the controller and data from the route
		// Dispatch the controller action
		// Get the controller and grab it's data (the payload)
		// Build a view object and attach the payload data from the controller, render it
		// Set all of the headers in the response object, set the rendered view
		// Execute the response object and return the final HTTP response
	}
	
	
	
	
	
	
}