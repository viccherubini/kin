<?php namespace jolt;
declare(encoding='UTF-8');

require_once(__DIR__.'/controller.php');
require_once(__DIR__.'/payload.php');
require_once(__DIR__.'/view.php');

class jolt {

	// HTTP values
	private $request = array();
	private $response = array();

	// Application settings
	private $settings = array();
	private $paths = array();

	// Timing
	private $start_time = 0.0;
	private $end_time = 0.0;
	private $execution_time = 0.0;



	
	const alphanum_replacement = '([a-z0-9_\-/%\.\*]*)';
	const numeric_replacement = '([\d]+)';

	const http = 'http://';
	const https = 'https://';
	const ext = '.php';
	
	const ds = DIRECTORY_SEPARATOR;
	
	public function __construct() {
		$this->paths = array(
			'application' => '',
			'asset' => '',
			'controller' => '',
			'css' => '',
			'image' => '',
			'js' => '',
			'validator' => '',
			'view' => ''
		);
		
		$this->settings = array(
			'allow_ssl' => true,
			'cookie_domain' => '',
			'force_ssl' => false,
			'secure_url' => '',
			'url' => '',
			'use_rewrite' => false
		);
		
		$this->request = array(
			'accept' => 'text/html',
			'path' => '/',
			'request_method' => 'GET',
			'type' => 'html',
			'route' => array(
				'route' => array(),
				'arguments' => array()
			),
			'route_404' => array(),
			'routes' => array()
		);
		
		$this->response = array(
			'content_type' => 'text/html',
			'response_code' => 405,
			'headers' => array()
		);
	}

	public function __destruct() {
		$this->route = array();
		$this->routes = array();
		$this->controller = null;
	}
	
	
	
	public function execute() {
		$this->start_timer();

		// Ensure there is at least one route and a 404 route

		$this->parse_request()
			->route_request();

		/*$rendering = '';
		try {
			$controller = $this->execute_controller();
			
			$view = new view;
			$view->set_view_path($this->paths['view'])
				->set_view_type($this->request['type'])
				->attach_payload($controller->payload)
				->render($controller->view);
			
			$rendering = $view->get_rendering();
			
			$this->response['headers'] = $controller->headers;
			$this->response['response_code'] = $controller->response_code;
		} catch (\Exception $e) {
			$rendering = $e->getMessage();
		}

		header_remove('Content-Type');
		header('Content-Type: '.$this->response['content_type'], true, $this->response['response_code']);

		foreach ($this->response['headers'] as $header => $value) {
			header("{$header}: {$value}");
		}*/

		$this->end_timer();
		return $rendering;
	}
	
	
	
	
	
	/* Public setters */
	public function set_paths(array $paths) {
		$this->paths = array_merge($this->paths, $paths);
		foreach ($this->paths as $k => $path) {
			$this->paths[$k] = (!empty($path) ? rtrim($path, self::ds).self::ds : $path);
		}
		
		foreach (array('controller' => 'controllers', 'validator' => 'validators', 'view' => 'views') as $k => $path) {
			$this->paths[$k] = (empty($this->paths[$k]) ? $this->paths['application'].$path.self::ds : $this->paths[$k]);
		}
		
		foreach (array('css' => 'css', 'image' => 'images', 'js' => 'js') as $k => $path) {
			$this->paths[$k] = (empty($this->paths[$k]) ? $this->paths['asset'].$path.self::ds : $this->paths[$k]);
		}
		return $this;
	}
	
	public function set_settings(array $settings) {
		$this->settings = array_merge($this->settings, $settings);
		
		$server_name = filter_input(INPUT_SERVER, 'SERVER_NAME');
		$root_script_name = dirname(filter_input(INPUT_SERVER, 'SCRIPT_NAME'));

		if (empty($root_script_name) || '/' === $root_script_name) {
			$this->settings['use_rewrite'] = true;
		}

		$url_protocol = ((array_key_exists('force_ssl', $this->settings) && $this->settings['force_ssl']) ? self::https : self::http);
		
		$this->settings['cookie_domain'] = $server_name;
		$this->settings['url'] = $url_protocol.$server_name.$root_script_name;
		$this->settings['secure_url'] = self::https.$server_name.$root_script_name;
		
		return $this;
	}
	
	public function set_routes(array $routes, array $route_404) {
		$mapper = function($e) {
			$e[1] = str_replace(array('%s', '%n'), array(jolt::alphanum_replacement, jolt::numeric_replacement), "#^{$e[1]}$#i");
			return $e;
		};
		
		$this->request['routes'] = array_map($mapper, $routes);
		$this->request['route_404'] = $route_404;
		return $this;
	}
	
	
	
	/* Public getters */
	public function get_execution_time() {
		return $this->execution_time;
	}
	
	public function get_routes() {
		return $this->routes;
	}
	
	
	
	/* Internal Methods */
	private function start_timer() {
		$this->start_time = microtime(true);
		return $this;
	}
	
	private function end_timer() {
		$this->end_time = microtime(true);
		$this->execution_time = ($this->end_time - $this->start_time);
		return $this;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	/* Methods to generate the request array. */
	private function parse_request() {
		$this->parse_header_accept()
			->parse_header_request_method()
			->parse_path();
			
		return $this;
	}
	
	private function parse_header_accept() {
		// Ignore multiple accept types
		$accept_bits = explode(',', filter_input(INPUT_SERVER, 'HTTP_ACCEPT'));
		if (count($accept_bits) > 0) {
			// Just get the first type, ignore the quality
			$accept_type = current(explode(';', $accept_bits[0]));
			
			$mime_type_bits = array();
			if (preg_match('#(.*)/(.*)#i', $accept_type, $mime_type_bits)) {
				$this->request['accept'] = $accept_type;
				$this->response['content_type'] = $accept_type;
				
				if ('*' !== $mime_type_bits[2]) {
					$this->request['type'] = $mime_type_bits[2];
				}
			}
		}
		return $this;
	}
	
	private function parse_header_request_method() {
		$this->request['request_method'] = strtoupper(filter_input(INPUT_SERVER, 'REQUEST_METHOD'));
		return $this;
	}
	
	private function parse_path() {
		$path = filter_input(INPUT_SERVER, 'PATH_INFO');
		if (!empty($path)) {
			$this->request['path'] = $path;
		}
		return $this;
	}
	
	
	
	/* Methods to parse the path. */
	private function route_request() {
		$routes_count = count($this->request['routes']);
		$matched_route = false;
		for ($i=0; $i<$routes_count; $i++) {
			$argv = array();
			
			$request_methods_match = ($this->request['routes'][$i][0] === $this->request['request_method']);
			$routes_match = (preg_match_all($this->request['routes'][$i][1], $this->request['path'], $argv, PREG_SET_ORDER) > 0);
			
			if ($request_methods_match && $routes_match) {
				$this->request['route']['route'] = $this->request['routes'][$i];
				$this->request['route']['arguments'] = array_slice($argv[0], 1);
				
				$matched_route = true;
				break;
			}
		}
		
		if (!$matched_route) {
			$this->request['route']['route'] = $this->request['route_404'];
		}
		return $this;
	}
	
	
	/* Methods to find the right controller and to build it. */
	private function execute_controller() {
		$route = $this->request['route']['route'];
		if (0 === count($route)) {
			throw new unrecoverable_exception('A valid route is not available.');
		}
		
		$controller_file = $this->paths['controller'].$route[2];
		if (!is_file($controller_file)) {
			throw new unrecoverable_exception("The controller was not found in the path specified: {$controller_file}.");
		}
		
		require_once($controller_file);
		
		$controller_class = $route[3];
		if (!class_exists($controller_class, false)) {
			throw new unrecoverable_exception("The controller class, {$controller_class}, was not found in the global namespace.");
		}
		
		$controller = new $controller_class;
		$controller->attach_payload(new payload);
		
		$controller_action = $route[4];
		try {
			$action = new \ReflectionMethod($controller, $controller_action);
		} catch (\ReflectionException $e) {
			throw new unrecoverable_exception("The controller action, {$controller_action}, is not a public member of the controller class {$controller_class}.");
		}
		
		$init_executed_successfully = true;
		if (method_exists($controller, 'init')) {
			$init_executed_successfully = $controller->init();
		}

		if ($init_executed_successfully && $action->isPublic()) {
			$action->invokeArgs($controller, $this->request['route']['arguments']);
		}
		
		if (method_exists($controller, 'shutdown')) {
			$controller->shutdown();
		}
		
		return $controller;
	}
	
	
	
	
}





class unrecoverable_exception extends \Exception {
	public function __construct($message) {
		parent::__construct("[Unrecoverable Exception] {$message}");
	}
}




class redirect_exception extends \Exception {

	private $location = null;

	public function __construct($location) {
		parent::__construct('');
		$this->location = $location;
	}

	public function get_location() {
		return $this->location;
	}

}