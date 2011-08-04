<?php namespace jolt;
declare(encoding='UTF-8');

require_once(__DIR__.'/exceptions/unrecoverable.php');

class route {
	
	private $method = '';
	private $route = '';
	private $controller = '';
	private $class = '';
	private $action = '';
	
	private $compiled_route = '';
	
	const get = 'GET';
	const post = 'POST';
	const put = 'PUT';
	const delete = 'DELETE';
	
	const alphanum_replacement = '([a-z0-9_\-/%\.\*\+]*)';
	const numeric_replacement = '([\d]+)';
	const route_regex = '#^%s$#i';

	public function __construct($method, $route, $controller, $class, $action) {
		$this->check_method_is_valid($method)
			->check_route_is_valid($route);
		
		$this->method = $method;
		$this->route = $route;
		$this->controller = $controller;
		$this->class = $class;
		$this->action = $action;
		
		$this->compile();
	}
	
	public function get_route() {
		return $this->route;
	}

	public function get_compiled_route() {
		return $this->compiled_route;
	}

	private function check_method_is_valid($method) {
		$method = strtoupper($method);
		if (!in_array($method, array(self::get, self::post, self::put, self::delete))) {
			throw new \jolt\exception\unrecoverable("The route can not be properly constructed. The method {$method} is not one of GET, POST, PUT, or DELETE.");
		}
		
		return $this;
	}

	private function check_route_is_valid($route) {
		if ('/' === $route) {
			return $this;
		}
		
		$this->check_route_is_nonempty($route)
			->check_route_starts_with_slash($route);
		
		if (0 === preg_match('#^/([a-z]+)([a-z0-9_\-/%\.\*]*)$#i', $route)) {
			throw new \jolt\exception\unrecoverable("The route {$route} is not valid.");
		}
		
		return $this;
	}
	
	private function check_route_is_nonempty($route) {
		$route_length = strlen($route);
		if (0 === $route_length) {
			throw new \jolt\exception\unrecoverable("The route can not be empty.");
		}
		return $this;
	}
	
	private function check_route_starts_with_slash($route) {
		if ('/' !== $route[0]) {
			throw new \jolt\exception\unrecoverable("The route must begin with a forward slash: /.");
		}
		return $this;
	}

	private function compile() {
		$search = array('%s', '%n');
		$replace = array(self::alphanum_replacement, self::numeric_replacement);
		
		$this->compiled_route = sprintf(self::route_regex, str_replace($search, $replace, $this->route));
		return $this;
	}
	
}