<?php namespace jolt;
declare(encoding='UTF-8');

require_once(__DIR__.'/exceptions/unrecoverable.php');

class router {

	// Data passed in from the request
	private $path = '';
	private $request_method = '';
	
	// All of the routes
	private $compiled_routes = array();
	private $uncompiled_routes = array();
	private $error_route = array();
	
	// The matched route and arguments
	private $arguments = array();
	private $route = array();
	
	const alphanum_replacement = '([a-z0-9_\-/%\.\*\+]*)';
	const numeric_replacement = '([\d]+)';

	public function __construct() {
	
	}
	
	public function __destruct() {
	
	}



	public function route() {
		$this->check_compiled_routes()
			->check_error_route();
		
		$matched_route = false;
		if (empty($this->path) || empty($this->request_method)) {
			$this->route = $this->error_route;
			return true;
		}
		
		$routes_count = count($this->compiled_routes);
		for ($i=0; $i<$routes_count; $i++) {
			$arguments = array();
			
			$request_methods_match = ($this->compiled_routes[$i][0] === $this->request_method);
			$routes_match = (preg_match_all($this->compiled_routes[$i][1], $this->path, $arguments, PREG_SET_ORDER) > 0);
			
			if ($request_methods_match && $routes_match) {
				$this->route = $this->uncompiled_routes[$i];
				$this->arguments = array_slice($arguments[0], 1);
				
				$matched_route = true;
				break;
			}
		}
		
		if (!$matched_route) {
			$this->route = $this->error_route;
		}
		
		return true;
	}


	
	
	public function set_path($path) {
		$this->path = trim($path);
		return $this;
	}
	
	public function set_request_method($request_method) {
		$this->request_method = strtoupper($request_method);
		return $this;
	}
	
	public function set_error_route(array $error_route) {
		$this->error_route = $error_route;
		return $this;
	}
	
	public function set_routes(array $routes) {
		$this->uncompiled_routes = $routes;
		$this->compiled_routes = $this->compile_routes($routes);
		return $this;
	}
	
	
	
	public function get_arguments() {
		return $this->arguments;
	}
	
	public function get_route() {
		return $this->route;
	}
	
	public function get_compiled_routes() {
		return $this->compiled_routes;
	}
	
	
	
	private function check_compiled_routes() {
		if (0 === count($this->compiled_routes)) {
			throw new \jolt\exception\unrecoverable("The router must have at least one compiled route before it can route a request.");
		}
		return $this;
	}
	
	private function check_error_route() {
		if (0 === count($this->error_route)) {
			throw new \jolt\exception\unrecoverable("The router must have an error route defined before it can route a request.");
		}
		return $this;
	}
	
	private function compile_routes($routes) {
		$search = array('%s', '%n');
		$replace = array(self::alphanum_replacement, self::numeric_replacement);
		
		$routes = array_map(function($e) use ($search, $replace) {
			$e[1] = str_replace($search, $replace, "#^{$e[1]}$#i");
			return $e;
		}, $routes);
		
		return $routes;
	}
	
}