<?php namespace jolt;
declare(encoding='UTF-8');

require_once(__DIR__.'/controller.php');
require_once(__DIR__.'/payload.php');
require_once(__DIR__.'/view.php');

class jolt {

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
			'routes' => array(),
			'route_404' => array(),
			'route' => array(
				'route' => array(),
				'arguments' => array()
			)
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

		$this->parse_request()
			->route_request();

		
		/*$view = new view;
		$view->set_css_path($this->css_path)
			->set_image_path($this->image_path)
			->set_js_path($this->js_path)
			->set_url($this->url)
			->set_secure_url($this->secure_url)
			->set_use_rewrite($this->use_rewrite)
			->set_view_path($this->view_path)
			->set_view_type($this->view_type);

		$rendering = '';
		//try {
			
			// Handle the outgoing response.
			$this->build_controller_file_path()
				->load_controller_file()
				->build_controller()
				->execute_controller();

			$payload = new payload;
			$payload->model($this->controller->payload);

			$rendering = $view->attach_payload($payload)
				->render($this->controller->view)
				->get_rendering();
			
			
			
		//} catch (redirect_exception $e) {
		// Add a location header
		//} catch (\Exception $e) {
		//	$rendering = $e->getMessage();
		//}
		
		header_remove('Content-Type');
		header('Content-Type: '.$this->controller->content_type, true, $this->controller->response_code);
		
		$this->end_timer();
		
		return $rendering;*/
	}
	
	
	
	
	
	
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

	private function compile_routes(array $routes) {
		
	}
	
	
	
	/* Application, Route and Path Parsing */
	
	
	
	private function build_urls() {
		
		
		return $this;
	}
	
	
	
	private function parse_http_accept_type() {
		$http_accept_bits = explode(',', filter_input(INPUT_SERVER, 'HTTP_ACCEPT'));
		if (count($http_accept_bits) > 0) {
			$this->http_accept_type = trim(strtolower($http_accept_bits[0]));
		}
		return $this;
	}
	
	private function parse_view_type() {
		$type_bits = explode('/', $this->http_accept_type);
		if (count($type_bits) > 0) {
			$this->view_type = end($type_bits);
		}
		return $this;
	}
	
	private function determine_default_view_type() {
		if ('*' === $this->view_type || empty($this->view_type)) {
			$this->view_type = $this->default_view_type;
		}
		return $this;
	}
	
	
	
	
	/* Controller Manipulation and Building */
	private function build_controller_file_path() {
		$this->controller_file_path = $this->controller_path.$this->route[2];
		return $this;		
	}
	
	private function load_controller_file() {
		if (!is_file($this->controller_file_path)) {
			throw new \Exception("The Controller was not found in the path specified: {$this->controller_file_path}.");
		}
		
		require_once($this->controller_file_path);
		return $this;
	}
	
	private function build_controller() {
		$controller_class = $this->route[3];
		if (!class_exists($controller_class, false)) {
			throw new \Exception("The Controller Class, {$controller_class}, was not found in the global namespace.");
		}
		
		$this->controller = new $controller_class;
		$this->controller->set_content_type($this->http_accept_type);
		return $this;
	}
	
	private function execute_controller() {
		try {
			$action = new \ReflectionMethod($this->controller, $this->route[4]);
		} catch (\ReflectionException $e) {
			throw new \Exception($e->getMessage());
		}
		
		$init_executed_successfully = true;
		if (method_exists($this->controller, 'init')) {
			$init_executed_successfully = $this->controller->init();
		}

		if ($init_executed_successfully && $action->isPublic()) {
			$action->invokeArgs($this->controller, $this->route_arguments);
		}
		
		if (method_exists($this->controller, 'shutdown')) {
			$this->controller->shutdown();
		}
		
		return $this;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	/* Methods to generate the request array */
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
			
			if (preg_match('#(.*)/(.*)#i', $accept_type)) {
				$this->request['accept'] = $accept_type;
				$this->response['content_type'] = $accept_type;
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
	
	
	
	/* Methods to parse the path */
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