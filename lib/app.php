<?php namespace jolt;
declare(encoding='UTF-8');

require_once(__DIR__.'/compiler.php');
require_once(__DIR__.'/request.php');
require_once(__DIR__.'/response.php');
require_once(__DIR__.'/router.php');

class app {

	private static $compiler = null;
	private static $request = null;
	private static $response = null;
	private static $router = null;

	private static $routes = array();
	
	
	public static function execute() {
		self::build_objects();
		
		$http_headers = filter_input_array(INPUT_SERVER, array(
			'HTTP_ACCEPT' => array(),
			'REQUEST_METHOD' => array(),
			'PATH_INFO' => array()
		));
		
		self::request
			->set_accept($http_headers['HTTP_ACCEPT'])
			->set_method($http_headers['REQUEST_METHOD'])
			->set_path($http_headers['PATH_INFO']);
		
		// Compile the settings
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
	
	
	public static function set_routes(array $routes) {
		self::$routes = $routes;
	}
	
	public static function set_paths(array $paths) {
		self::$paths = $path;
	}
	
	public static function set_settings(array $settings) {
		self::$settings = $settings;
	}
	
	
	
	private static function build_objects() {
		self::$compiler = new compiler;
		self::$request = new request;
		self::$response = new response;
		self::$router = new router;
	}
	
}