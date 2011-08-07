<?php namespace kin;
declare(encoding='UTF-8');

require_once(__DIR__.'/compiler.php');
require_once(__DIR__.'/dispatcher.php');
require_once(__DIR__.'/request.php');
require_once(__DIR__.'/response.php');
require_once(__DIR__.'/route.php');
require_once(__DIR__.'/router.php');
require_once(__DIR__.'/settings.php');
require_once(__DIR__.'/view.php');

class app {

	public $compiler = null;
	public $dispatcher = null;
	public $request = null;
	public $response = null;
	public $router = null;
	public $settings = null;
	public $view = null;

	private $routes = array();
	private $exception_routes = array();
	
	public function __construct() {
		$this->compile_request();
		$this->response = new response;
	}
	
	
	public function attach_all_routes(array $routes, array $exception_routes) {
		$this->routes = $routes;
		$this->exception_routes = $exception_routes;
		return($this);
	}
	
	public function attach_settings(settings $settings) {
		$this->settings = $settings;
		return($this);
	}
	
	
	
	public function run() {
		try {
			$this->settings->compile();
			
			$this->response
				->set_content_type($this->settings->content_type);
			
			// Build the router object and attach the routes and request data
			$router = new router;
			$router->set_path($this->request->get_path())
				->set_request_method($this->request->get_method())
				->set_routes($this->routes)
				->set_exception_routes($this->exception_routes)
				->route();
			$route = $router->get_route();
			
			// Build the compiler and set the data from the route
			$compiler = new compiler;
			$compiler->set_class($route->get_class())
				->set_file($route->get_controller())
				->set_path($this->settings->controllers_path)
				->compile();
			$controller = $compiler->get_controller();
			
			// Build the dispatcher and set the controller and data from the route
			$dispatcher = new dispatcher;
			$dispatcher->attach_controller($controller)
				->set_action($route->get_action())
				->set_arguments($route->get_arguments())
				->dispatch();
			$controller = $dispatcher->get_controller();
			
			$view = new view;
			$view->set_payload($controller->get_payload())
				->set_file($controller->get_view())
				->set_path($this->settings->views_path)
				->set_type($this->settings->type)
				->render();
			$content = $view->get_rendering();
			
			$this->response
				->set_headers($controller->get_headers())
				->set_response_code($controller->get_response_code())
				->set_content($content);
		} catch (\Exception $e) {
			$this->response->set_content($e->getMessage());
		}
		
		return($this->response->respond());
	}
	
	
	
	private function compile_request() {
		$http_headers = filter_input_array(INPUT_SERVER, array(
			'HTTP_ACCEPT' => array(),
			'REQUEST_METHOD' => array(),
			'PATH_INFO' => array()
		));
		
		$this->request = new request;
		$this->request->set_accept($http_headers['HTTP_ACCEPT'])
			->set_method($http_headers['REQUEST_METHOD'])
			->set_path($http_headers['PATH_INFO']);
			
		return($this);
	}
}