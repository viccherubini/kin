<?php namespace kintest;
declare(encoding='UTF-8');

use \kin\app as app,
	\kin\app\route as route,
	\kin\app\router as router,
	\kin\app\settings as settings,
	\kintest\testcase as testcase;

require_once(__DIR__.'/../testcase.php');
require_once(__DIR__.'/../../kin/lib/app.php');
require_once(__DIR__.'/../../kin/lib/app/controller.php');

class app_test extends testcase {
	
	public function test_run__sets_default_content_type_if_router_fails() {
		$content_type = 'application/vnd.kin';
		
		$settings = new settings(__DIR__);
		$settings->content_type = $content_type;
		
		$app = new app;
		$app->attach_settings($settings);
		
		$app->run();
		
		$this->assertEquals($content_type, $app->get_response()->get_content_type());
	}
	
	public function test_run__sets_default_request_accept_type_if_failure_and_no_settings_content_type() {
		$app = new app;
		
		$content_type = $app->get_request()->get_accept();
		
		$app->run();
		
		$this->assertEquals($content_type, $app->get_response()->get_content_type());
	}
	
	public function test_run__sets_default_accept_from_settings() {
		$accept = 'application/xml+atom';
		
		$settings = new settings(__DIR__);
		$settings->accept = $accept;
		
		$app = new app;
		$app->attach_settings($settings);
		
		$app->run();
		
		$this->assertEquals($accept, $app->get_request()->get_accept());
	}
	
	public function test_run__sets_default_content_type_if_controller_has_none() {
		$content_type = 'application/json';
		
		$settings = new settings(__DIR__.'/../fixtures/application/');
		$settings->content_type = $content_type;
		
		$routes = array(new route(route::get, '/', 'index_controller.php', 'kintest\index_controller', 'action_get_index'));
		$exception_routes = array(router::route_404 => new route(route::get, '/', 'error_controller.php', 'kintest\error_controller.php', 'action_error404'));
		
		$app = new app;
		$app->attach_all_routes($routes, $exception_routes)
			->attach_settings($settings);
		
		$app->run();
		
		$this->assertEquals($content_type, $app->get_controller()->get_content_type());
	}
	
	public function test_run__gets_content_type_from_accept_if_controller_has_no_content_type() {
		$accept = 'application/json';
		
		$settings = new settings(__DIR__.'/../fixtures/application/');
		$settings->accept = $accept;
		
		$routes = array(new route(route::get, '/', 'index_controller.php', 'kintest\index_controller', 'action_get_index'));
		$exception_routes = array(router::route_404 => new route(route::get, '/', 'error_controller.php', 'kintest\error_controller.php', 'action_error404'));
		
		$app = new app;
		$app->attach_all_routes($routes, $exception_routes)
			->attach_settings($settings);
		
		$app->run();
		
		$this->assertEquals($accept, $app->get_controller()->get_content_type());
	}

	
}