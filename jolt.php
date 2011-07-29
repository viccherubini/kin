<?php namespace jolt;
declare(encoding='UTF-8');


class jolt {

	private $route = array();
	private $route_404 = array();
	private $route_arguments = array();
	private $routes = array();
	
	private $application_path = null;
	private $http_accept_type = null;
	private $path = null;
	
	private $start_time = 0.0;
	private $end_time = 0.0;
	private $execution_time = 0.0;
	
	// Controllers
	private $controller = null;
	private $controller_file_path = null;
	private $controllers_path = null;

	
	const alphanum_replacement = '([a-z0-9_\-/%\.\*]*)';
	const numeric_replacement = '([\d]+)';

	const controllers_path = 'controllers';
	const views_path = 'views';
	

	public function __construct() {
		
	}

	public function __destruct() {
		$this->route = array();
		$this->routes = array();
	}
	
	
	
	public function execute() {
		$this->start_timer();
	
		$this->parse_http_accept_type()
			->parse_path()
			->parse_route();
		
		try {
			$this->build_controller_file_path()
				->load_controller_file()
				->build_controller()
				->execute_controller();
		
			
		} catch (jolt_exception $e) {
			// Figure out how to add this exception to the payload correctly

		} catch (redirect_exception $e) {
			$this->controller->add_header('location', $e->get_location());
		}
		
		print_r($this->controller->get_payload());
		
		// Go through and add all of the headers to the response
		
		
		$this->end_timer();
	}
	
	
	public function set_application_path($application_path) {
		$this->application_path = trim(realpath($application_path).DIRECTORY_SEPARATOR);
		$this->controllers_path = $this->application_path.self::controllers_path.DIRECTORY_SEPARATOR;
		
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
	
	/* Route and Path Parsing */
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
			if (preg_match_all($this->routes[$i][1], $this->path, $argv, PREG_SET_ORDER) > 0) {
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
	
	private function parse_http_accept_type() {
		$http_accept_bits = explode(',', filter_input(INPUT_SERVER, 'HTTP_ACCEPT'));
		if (count($http_accept_bits) > 0) {
			$this->http_accept_type = trim(strtolower($http_accept_bits[0]));
		}
		return $this;
	}
	
	
	/* Controller Manipulation and Building */
	private function build_controller_file_path() {
		$this->controller_file_path = $this->controllers_path.$this->route[2];
		return $this;		
	}
	
	private function load_controller_file() {
		if (!is_file($this->controller_file_path)) {
			throw new jolt_exception('controller not found in path specified: '.$this->controller_file_path);
		}
		
		require_once($this->controller_file_path);
		return $this;
	}
	
	private function build_controller() {
		$controller_class = $this->route[3];
		if (!class_exists($controller_class, false)) {
			throw new jolt_exception('controller class not found in global namespace.');
		}
		
		$this->controller = new $controller_class;
		return $this;
	}
	
	private function execute_controller() {
		try {
			$action = new \ReflectionMethod($this->controller, $this->route[4]);
		} catch (\ReflectionException $e) {
			throw new jolt_exception($e->getMessage());
		}
		
		$init_executed_successfully = true;
		if (method_exists($this->controller, 'init')) {
			$init_executed_successfully = $this->controller->init();
		}

		if ($init_executed_successfully && $action->isPublic()) {
			if ($action->isStatic()) {
				$action->invokeArgs(null, $this->route_arguments);
			} else {
				$action->invokeArgs($this->controller, $this->route_arguments);
			}
		}

		if (method_exists($this->controller, 'shutdown')) {
			$this->controller->shutdown();
		}
		
		return $this;
	}
	/*
	
			
		$rendered_controller = ob_get_clean();*/
	
	
}





abstract class controller {
	
	private $headers = array();
	private $payload = array();
	
	public function __construct() {
	
	}
	
	public function __destruct() {
	
	}
	
	
	public function __set($k, $v) {
		$this->payload[$k] = $v;
		return $this;
	}
	
	public function __get($k) {
		if (array_key_exists($k, $this->payload)) {
			return $this->payload[$k];
		}
		return null;
	}
	
	public function add_header($header, $value) {
		$this->headers[$header] = $value;
		return $this;
	}
	
	public function register($k, $v) {
		return $this->__set($k, $v);
	}
	
	public function get_payload() {
		return $this->payload;
	}
	
	
}





class jolt_exception extends \Exception {

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






abstract class response {

	private $response = array();

	public function __construct() {
		$this->response = array(
			'content' => '',
			'errors' => array(),
			'message' => '',
			'model' => array(),
			'name' => '',
			'object' => NULL,
			'redirect' => '',
			'token' => ''
		);
	}
	
	public function __destruct() {
		$this->response = array();
	}
	
	public function __set($k, $v) {
		$this->response[$k] = $v;
		return $this;
	}

	public function __get($k) {
		if (array_key_exists($k, $this->response)) {
			return $this->response[$k];
		}
		return null;
	}
	
	public function __call($method, $argv) {
		if (0 === count($argv)) {
			return $this->__get($method);
		} else {
			return $this->__set($method, current($argv));
		}
	}
	
	public function get_response() {
		return $this->response;
	}


	abstract public function build();
}


class response_json extends response {
	public function build() {
		return json_encode($this->get_response());
	}
	
}

class response_html extends response {
	

}
