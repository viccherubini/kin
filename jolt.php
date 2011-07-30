<?php namespace jolt;
declare(encoding='UTF-8');

require_once(__DIR__.'/controller.php');
require_once(__DIR__.'/payload.php');
require_once(__DIR__.'/view.php');

class jolt {

	// Application settings
	private $settings = array();

	// Routes
	private $route = array();
	private $route_404 = array();
	private $route_arguments = array();
	private $routes = array();
	private $path = null;
	private $request_method = null;
	
	// Content types
	private $http_accept_type = null;
	private $view_type = null;
	private $default_view_type = 'html';
	private $default_content_type = 'text/html';
	
	// Timing
	private $start_time = 0.0;
	private $end_time = 0.0;
	private $execution_time = 0.0;
	
	// Controllers
	private $application_paths = array();
	private $asset_paths = array();
	private $controller = null;
	private $controller_file_path = null;
	
	// Paths and URLs
	private $application_path = null;
	private $asset_path = null;
	
	private $controller_path = null;
	private $validator_path = null;
	private $view_path = null;
	
	private $css_path = null;
	private $image_path = null;
	private $js_path = null;
	
	private $use_rewrite = false;
	private $url = null;
	private $secure_url = null;


	
	const alphanum_replacement = '([a-z0-9_\-/%\.\*]*)';
	const numeric_replacement = '([\d]+)';

	const http = 'http://';
	const https = 'https://';
	const ext = '.php';
	

	
	public function __construct() {
		$this->application_paths = array(
			'controller_path' => 'controllers',
			'validator_path' => 'validators',
			'view_path' => 'views'
		);
		
		$this->asset_paths = array(
			'css_path' => 'css',
			'image_path' => 'image',
			'js_path' => 'js'
		);
	}

	public function __destruct() {
		$this->route = array();
		$this->routes = array();
		$this->controller = null;
	}
	
	
	
	public function execute() {
		$this->start_timer();

		// Handle the incoming request
		$this->parse_request_method()
			->parse_http_accept_type()
			->parse_view_type()
			->determine_default_view_type()
			->parse_path()
			->parse_route();


		$view = new view;
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
		
		return $rendering;
	}
	
	
	
	
	
	
	
	
	public function set_application_settings(array $settings) {
		$this->settings = $settings;
		$this->parse_application_paths()
			->parse_assets_paths()
			->build_urls();
		return $this;
	}
	
	public function set_routes(array $routes) {
		$this->routes = $this->compile_routes_to_regular_expressions($routes);
		return $this;
	}
	
	public function set_route_404(array $route) {
		$this->route_404 = $route;
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

	private function compile_routes_to_regular_expressions(array $routes) {
		$mapper = function($e) {
			$e[1] = str_replace(array('%s', '%n'), array(jolt::alphanum_replacement, jolt::numeric_replacement), "#^{$e[1]}$#i");
			return $e;
		};
		return array_map($mapper, $routes);
	}
	
	
	
	/* Application, Route and Path Parsing */
	private function parse_application_paths() {
		if (array_key_exists('application_path', $this->settings)) {
			$this->application_path = trim(realpath($this->settings['application_path']).DIRECTORY_SEPARATOR);
		}
		
		foreach ($this->application_paths as $k => $path) {
			$this->$k = $this->application_path.$path.DIRECTORY_SEPARATOR;
		}
		return $this;
	}
	
	private function parse_assets_paths() {
		if (array_key_exists('asset_path', $this->settings)) {
			$this->asset_path = rtrim($this->settings['asset_path'], '/').DIRECTORY_SEPARATOR;
		}
		
		foreach ($this->asset_paths as $k => $path) {
			$this->$k = $this->asset_path.$path.DIRECTORY_SEPARATOR;
		}
		return $this;
	}
	
	private function build_urls() {
		$server_name = filter_input(INPUT_SERVER, 'SERVER_NAME');
		$root_script_name = dirname(filter_input(INPUT_SERVER, 'SCRIPT_NAME'));

		if (empty($root_script_name) || '/' === $root_script_name) {
			$this->use_rewrite = true;
		}

		$cookie_domain = $server_name;
		$url_protocol = ((array_key_exists('force_ssl', $this->settings) && $this->settings['force_ssl']) ? self::https : self::http);
		
		$this->url = $url_protocol.$server_name.$root_script_name;
		$this->secure_url = self::https.$server_name.$root_script_name;
		
		return $this;
	}
	
	private function parse_request_method() {
		$this->request_method = strtoupper(filter_input(INPUT_SERVER, 'REQUEST_METHOD'));
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
	
	private function parse_path() {
		$this->path = filter_input(INPUT_SERVER, 'PATH_INFO');
		if (empty($this->path)) {
			$this->path = '/';
		}
		return $this;
	}
	
	private function parse_route() {
		$routes_count = count($this->routes);
		for ($i=0; $i<$routes_count; $i++) {
			$argv = array();
			if ($this->routes[$i][0] === $this->request_method && preg_match_all($this->routes[$i][1], $this->path, $argv, PREG_SET_ORDER) > 0) {
				$this->route = $this->routes[$i];
				$this->route_arguments = array_slice($argv[0], 1);
				break;
			}
		}
		
		if (0 === count($this->route)) {
			$this->route = $this->route_404;
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