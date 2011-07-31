<?php namespace jolt;
declare(encoding='UTF-8');

require_once(__DIR__.'/exceptions/unrecoverable.php');

class router {

	private $path = '';
	
	private $arguments = array();
	private $route = array();
	private $routes = array();
	private $error_route = array();

	const alphanum_replacement = '([a-z0-9_\-/%\.\*]*)';
	const numeric_replacement = '([\d]+)';

	public function __construct() {
	
	}
	
	public function __destruct() {
	
	}



	public function route() {
		$this->check_routes()
			->check_error_route();
		
		if (empty($this->path)) {
			$this->route = $this->error_route;
			return true;
		}
		
		
		/*$routes_count = count($this->routes);
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
		}*/
		
		
	}


	
	
	public function set_path($path) {
		$this->path = trim($path);
		return $this;
	}
	
	public function set_arguments(array $arguments) {
		$this->arguments = $arguments;
		return $this;
	}
	
	public function set_error_route(array $error_route) {
		$this->error_route = $error_route;
		return $this;
	}
	
	public function set_routes(array $routes) {
		$search = array('%s', '%n');
		$replace = array(self::alphanum_replacement, self::numeric_replacement);
		
		$this->routes = array_map(function($e) use ($search, $replace) {
			$e[1] = str_replace($search, $replace, "#^{$e[1]}$#i");
			return $e;
		}, $routes);
		
		return $this;
	}
	
	
	
	public function get_route() {
		return $this->route;
	}
	
	public function get_routes() {
		return $this->routes;
	}
	
	
	
	private function check_routes() {
		if (0 === count($this->routes)) {
			throw new \jolt\exception\unrecoverable("The router must have at least one route before it can route a request.");
		}
		return $this;
	}
	
	private function check_error_route() {
		if (0 === count($this->error_route)) {
			throw new \jolt\exception\unrecoverable("The router must have an error route defined before it can route a request.");
		}
		return $this;
	}
}