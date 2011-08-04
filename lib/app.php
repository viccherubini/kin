<?php namespace jolt;
declare(encoding='UTF-8');

require_once(__DIR__.'/compiler.php');
require_once(__DIR__.'/dispatcher.php');
require_once(__DIR__.'/request.php');
require_once(__DIR__.'/response.php');
require_once(__DIR__.'/router.php');
require_once(__DIR__.'/view.php');

class app {

	private static $compiler = null;
	private static $dispatcher = null;
	private static $request = null;
	private static $response = null;
	private static $router = null;

	private static $routes = array();
	private static $error_route = array();
	
	private static $paths = array();
	private static $settings = array();
	
	public static function execute() {
		// Compile the settings
		
		// Build the request object
		$http_headers = filter_input_array(INPUT_SERVER, array(
			'HTTP_ACCEPT' => array(),
			'REQUEST_METHOD' => array(),
			'PATH_INFO' => array()
		));

		try {
			$request = new request;
			$request->set_accept($http_headers['HTTP_ACCEPT'])
				->set_method($http_headers['REQUEST_METHOD'])
				->set_path($http_headers['PATH_INFO']);
			
			// Build the router object and attach the routes and request data
			$router = new router;
			$router->set_path($request->get_path())
				->set_request_method($request->get_method())
				->set_routes(self::$routes)
				->set_error_route(self::$error_route)
				->route();
		
		
			// Build the compiler and set the data from the route
			$compiler = new compiler;
			$compiler->set_class($router->get_class())
				->set_file($router->get_file())
				->set_path(self::$paths['controllers'])
				->compile();
			$controller = $compiler->get_controller();
		
			// Build the dispatcher and set the controller and data from the route
			$dispatcher = new dispatcher;
			$dispatcher->attach_controller($controller)
				->set_action($router->get_action())
				->set_arguments($router->get_arguments())
				->dispatch();
			$controller = $dispatcher->get_controller();
			
			$view = new view;
			$view->set_payload($controller->get_payload())
				->set_file($controller->get_view())
				->set_path(self::$paths['views'])
				->set_type($request->get_type())
				->render();
			$content = $view->get_rendering();
			
			$response = new response;
			$response->set_headers($controller->get_headers())
				->set_content_type($request->get_accept())
				->set_response_code($controller->get_response_code())
				->set_content($view->get_rendering())
				->respond();
				
			return $content;
		} catch (\Exception $e) {
			echo $e->getMessage();
		}
		
		return '';
	}
	
	
	public static function set_routes(array $routes) {
		self::$routes = $routes;
	}
	
	public static function set_error_route(array $error_route) {
		self::$error_route = $error_route;
	}
	
	public static function set_paths(array $paths) {
		self::$paths = $paths;
	}
	
	public static function set_settings(array $settings) {
		self::$settings = $settings;
	}
	
	
	
	private static function build_objects() {
		
	}
	
}