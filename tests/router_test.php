<?php namespace jolt_test;
declare(encoding='UTF-8');

use \jolt\router as router;

require_once(__DIR__.'/testcase.php');
require_once(__DIR__.'/../lib/router.php');

class router_test extends testcase {
	
	private $routes = array();
	private $error_route = array();


	public function test_set_routes__compiles_routes() {
		$routes = array(
			array('GET', '/', 'index_controller.php', 'index_controller', 'action_get_index')
		);
		
		$router = new router;
		$router->set_routes($routes);
		
		$this->assertGreaterThan(0, count($router->get_compiled_routes()));
	}
	
	/**
	 * @dataProvider provider_uncompiled_route__and__compiled_route
	 */
	public function test_set_route__compiles_routes_into_proper_regular_expressions($uncompiled_route, $compiled_route) {
		$routes = array($uncompiled_route);
	
		$router = new router;
		$router->set_routes($routes);
		
		$this->assertEquals($compiled_route, current($router->get_compiled_routes()));
	}
	
	/**
	 * @expectedException \jolt\exception\unrecoverable
	 */
	public function test_route__requires_compiled_routes() {
		$router = new router;
		$router->route();
	}
	
	/**
	 * @expectedException \jolt\exception\unrecoverable
	 */
	public function test_route__requires_error_route() {
		$routes = array(
			array('GET', '/', 'index_controller.php', 'index_controller', 'action_get_index')
		);
		
		$router = new router;
		$router->set_routes($routes);
		
		$router->route();
	}
	
	public function test_route__sets_error_route_if_path_empty() {
		$routes = array(
			array('GET', '/', 'index_controller.php', 'index_controller', 'action_get_index')
		);
		
		$error_route = array('GET', '/', 'error_controller.php', 'error_controller', 'action_error_404');
		
		$router = new router;
		$router->set_routes($routes)
			->set_error_route($error_route)
			->set_path('');
		
		$router->route();	
		$this->assertEquals($error_route, $router->get_route());
	}
	
	public function test_route__sets_error_route_if_request_method_empty() {
		$routes = array(
			array('GET', '/', 'index_controller.php', 'index_controller', 'action_get_index')
		);
		
		$error_route = array('GET', '/', 'error_controller.php', 'error_controller', 'action_error_404');
		
		$router = new router;
		$router->set_routes($routes)
			->set_error_route($error_route)
			->set_path('/')
			->set_request_method('');
		
		$router->route();	
		$this->assertEquals($error_route, $router->get_route());
	}
	
	/**
	 * @dataProvider provider_path__and__matching_route
	 */
	public function test_route__finds_route($path, $matching_route) {
		$routes = array(
			$matching_route
		);
		
		$error_route = array('GET', '/', 'error_controller.php', 'error_controller', 'action_error_404');
		
		$router = new router;
		$router->set_routes($routes)
			->set_error_route($error_route)
			->set_path($path)
			->set_request_method($matching_route[0]); // Always ensure the request methods match
		
		$router->route();
		$this->assertEquals($matching_route, $router->get_route());
	}
	
	/**
	 * @dataProvider provider_path__matching_route__and__arguments
	 */
	public function test_route__parses_arguments($path, $matching_route, $arguments) {
		$routes = array(
			$matching_route
		);
		
		$error_route = array('GET', '/', 'error_controller.php', 'error_controller', 'action_error_404');
		
		$router = new router;
		$router->set_routes($routes)
			->set_error_route($error_route)
			->set_path($path)
			->set_request_method($matching_route[0]); // Always ensure the request methods match
		
		$router->route();
		$this->assertEquals($arguments, $router->get_arguments());
	}
	
	public function test_route__sets_error_route_if_no_route_matched() {
		$request_method = 'GET';
		$path = '/path/to/freedom';
		
		$routes = array(
			array('GET', '/path/to/destiny', 'destiny_controller.php', 'destiny_controller', 'action_get_destiny')
		);
		
		$error_route = array('GET', '/', 'error_controller.php', 'error_controller', 'action_error_404');
		
		$router = new router;
		$router->set_routes($routes)
			->set_error_route($error_route)
			->set_path($path)
			->set_request_method($request_method); // Always ensure the request methods match
		
		$router->route();
		$this->assertEquals($error_route, $router->get_route());
	}
	
	
	
	public function provider_uncompiled_route__and__compiled_route() {
		return array(
			array(
				array('GET', '/', 'index_controller.php', 'index_controller', 'action_get_index'),
				array('GET', '#^/$#i', 'index_controller.php', 'index_controller', 'action_get_index')
			),
			array(
				array('GET', '/read/user/%n', 'user_controller.php', 'user_controller', 'action_get_read'),
				array('GET', '#^/read/user/([\d]+)$#i', 'user_controller.php', 'user_controller', 'action_get_read')
			),
			array(
				array('GET', '/search/%s', 'search_controller.php', 'search_controller', 'action_get_search'),
				array('GET', '#^/search/([a-z0-9_\-/%\.\*\+]*)$#i', 'search_controller.php', 'search_controller', 'action_get_search')
			)
		);
	}
	
	public function provider_path__and__matching_route() {
		return array(
			array(
				'/',
				array('GET', '/', 'index_controller.php', 'index_controller', 'action_get_index')
			),
			array(
				'/read/user/10',
				array('GET', '/read/user/%n', 'user_controller.php', 'user_controller', 'action_get_read')
			),
			array(
				'/search/some+search+string',
				array('GET', '/search/%s', 'search_controller.php', 'search_controller', 'action_get_search')
			)
		);
	}
	
	public function provider_path__matching_route__and__arguments() {
		return array(
			array(
				'/',
				array('GET', '/', 'index_controller.php', 'index_controller', 'action_get_index'),
				array()
			),
			array(
				'/read/user/10',
				array('GET', '/read/user/%n', 'user_controller.php', 'user_controller', 'action_get_read'),
				array(10)
			),
			array(
				'/read/user/10/and/delete/user/15',
				array('GET', '/read/user/%n/and/delete/user/%n', 'user_controller.php', 'user_controller', 'action_get_read_and_delete_user'),
				array(10, 15)
			),
			array(
				'/search/some+search+string',
				array('GET', '/search/%s', 'search_controller.php', 'search_controller', 'action_get_search'),
				array('some+search+string')
			),
			array(
				'/search/some+search+string/and/some+other+search+string',
				array('GET', '/search/%s/and/%s', 'search_controller.php', 'search_controller', 'action_get_advanced_search'),
				array('some+search+string', 'some+other+search+string')
			)
		);
	}
	
}