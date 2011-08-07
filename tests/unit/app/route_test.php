<?php namespace kintest\app;
declare(encoding='UTF-8');

use \kin\app\route as route,
	\kintest\testcase as testcase;

require_once(__DIR__.'/../../testcase.php');
require_once(__DIR__.'/../../../kin/lib/app/route.php');

class route_test extends testcase {

	/**
	 * @expectedException \kin\exception\unrecoverable
	 */
	public function test___construct__method_must_be_one_of_get_post_put_or_delete() {
		$route = new route('OPTIONS', '/', 'controller.php', 'controller', 'action');
	}
	
	/**
	 * @expectedException \kin\exception\unrecoverable
	 */
	public function test___construct__route_can_not_be_empty() {
		$route = new route(route::get, '', 'controller.php', 'controller', 'action');
	}
	
	/**
	 * @expectedException \kin\exception\unrecoverable
	 */
	public function test___construct__route_must_start_with_forward_slash() {
		$route = new route(route::get, 'abc/def/123', 'controller.php', 'controller', 'action');
	}

	/**
	 * @expectedException \kin\exception\unrecoverable
	 * @dataProvider provider_invalid_route
	 */
	public function test___construct__route_must_be_valid($invalid_route) {
		$route = new route(route::get, $invalid_route, 'controller.php', 'controller', 'action');
	}
	
	/**
	 * @dataProvider provider_uncompiled_route__and__compiled_route
	 */
	public function test___construct__compiled_route($route, $compiled_route) {
		$route = new route(route::get, $route, 'controller.php', 'controller', 'action');
		
		$this->assertEquals($compiled_route, $route->get_compiled_route());
	}
	
	/**
	 * @dataProvider provider_valid_uncompiled_route
	 */
	public function test___construct__route_is_valid($uncompiled_route) {
		$route = new route(route::get, $uncompiled_route, 'controller.php', 'controller', 'action');
		
		$this->assertEquals($uncompiled_route, $route->get_route());
	}
	
	
	public function provider_invalid_route() {
		return array(
			array('//'),
			array('///'),
			array('abc')
		);
	}
	
	public function provider_uncompiled_route__and__compiled_route() {
		return array(
			array('/', '#^/$#i'),
			array('/read/user/%n', '#^/read/user/([\d]+)$#i'),
			array('/search/%s', '#^/search/([a-z0-9_\-/%\.\*\+]*)$#i')
		);
	}
	
	public function provider_valid_uncompiled_route() {
		return array(
			array('/'),
			array('/abc'),
			array('/abc9'),
			array('/long_route'),
			array('/long-route'),
			array('/abc99'),
			array('/long_route/'),
			array('/abc/'),
			array('/abc0/'),
			array('/abc/def'),
			array('/abc/def/'),
			array('/abc/%n/'),
			array('/abc/def/efg/%n'),
			array('/abc/def/%s/%n'),
			array('/abc.def/%s/%n'),
			array('/abc/usr/%n/blah/%s'),
			array('/tutorial/%s.html'),
			array('/search/result-%n.html'),
			array('/abc./'),
			array('/abc.'),
			array('/abc/%n/def/%n')
		);
	}
	
}
