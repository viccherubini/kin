<?php namespace jolt_test;
declare(encoding='UTF-8');

use \jolt\router as router;

require_once(__DIR__.'/testcase.php');
require_once(__DIR__.'/../lib/router.php');

class router_test extends testcase {
	
	private $routes = array();
	private $error_route = array();
	
	public function setUp() {
		$this->routes = array(
			 array('GET', '/', 'index_controller.php', 'metrics\index_controller', 'action_get_index')
			,array('POST', '/', 'index_controller.php', 'metrics\index_controller', 'action_post_index')
			,array('GET', '/read/user/%n', 'user_controller.php', 'metrics\user_controller', 'action_get_read')
			,array('GET', '/read/user/%n/and/delete/user/%n', 'user_controller.php', 'metrics\user_controller', 'action_get_read_and_delete_user')
			,array('POST', '/search/%s', 'search_controller.php', 'metrics\search_controller', 'action_post_search')
		);
		
		$this->error_route = array('GET', '/', 'error_controller.php', 'metrics\error_controller', 'action_error_404');
	}
	
	/**
	 * @dataProvider provider_uncompiled_and_compiled_routes
	 */
	public function test_set_routes__compiles_routes($uncompiled_route, $compiled_route) {
		$router = new router;
		$router->set_routes(array(array('GET', $uncompiled_route)));
		
		$routes = $router->get_routes();
		$this->assertEquals($compiled_route, $routes[0][1]);
	}
	
	/**
	 * @expectedException \jolt\exception\unrecoverable
	 */
	public function test_route__requires_routes() {
		$router = new router;
		$router->route();
	}
	
	/**
	 * @expectedException \jolt\exception\unrecoverable
	 */
	public function test_route__requires_error_route() {
		$router = new router;
		$router->set_routes($this->routes);
		
		$router->route();
	}
	
	public function test_route__sets_error_route_if_path_empty() {
		$router = new router;
		$router->set_routes($this->routes)
			->set_error_route($this->error_route)
			->set_path('');
		
		$router->route();	
		$this->assertEquals($this->error_route, $router->get_route());
	}
	
	
	public function test_route__finds_route() {
		
		
	}
	
	public function test_route__parses_arguments() {
		
	}
	
	
	public function provider_uncompiled_and_compiled_routes() {
		return array(
			array('/', '#^/$#i'),
			array('/read/user/%n', '#^/read/user/([\d]+)$#i'),
			array('/read/user/%n/and/delete/user/%n', '#^/read/user/([\d]+)/and/delete/user/([\d]+)$#i'),
			array('/search/%s', '#^/search/([a-z0-9_\-/%\.\*]*)$#i'),
			array('/search/%s/delete/%s', '#^/search/([a-z0-9_\-/%\.\*]*)/delete/([a-z0-9_\-/%\.\*]*)$#i')
		);
	}
	
}