<?php namespace kin;
require_once(__DIR__.'/../../testcase.php');
require_once(__DIR__.'/../../../kin/lib/app/router.php');

class router_test extends testcase {
	
	private $empty_route = null;
	
	public function setUp() {
		$this->empty_route = $this->getMock('kin\route', array(), array(), '', false);
	}
	
	/**
	 * @expectedException \kin\unrecoverable
	 */
	public function test_set_routes__requires_at_least_one_route() {
		$router = new router;
		$router->set_routes(array(0, 'twelve', new \StdClass));
	}
	
	public function test_set_routes__filters_invalid_routes() {
		$routes = array(0, 'twelve', new \StdClass, $this->empty_route);
		$expected_routes = array($this->empty_route);
		
		$router = new router;
		$router->set_routes($routes);
		
		$this->assertEquals(count($expected_routes), count($router->get_routes()));
	}
	
	
	
	/**
	 * @expectedException \kin\unrecoverable
	 */
	public function test_set_exception_routes__requires_at_least_one_route() {
		$router = new router;
		$router->set_exception_routes(array(0, 'twelve', new \StdClass));
	}
	
	/**
	 * @expectedException \kin\unrecoverable
	 */
	public function test_set_exception_routes__requires_at_least_one_404_route() {
		$router = new router;
		$router->set_exception_routes(array(200 => $this->empty_route));
	}
	
	
	
	/**
	 * @expectedException \kin\unrecoverable
	 */
	public function test_route__requires_at_least_one_route() {
		$router = new router;
		$router->set_exception_routes(array(404 => $this->empty_route));
		
		$router->route();
	}
	
	/**
	 * @expectedException \kin\unrecoverable
	 */
	public function test_route__requires_at_least_one_exception_route() {
		$router = new router;
		$router->set_routes(array($this->empty_route));
		
		$router->route();
	}
	
	/**
	 * @expectedException \kin\unrecoverable
	 */
	public function test_route__requires_at_least_one_exception_404_route() {
		$router = new router;
		$router->set_routes(array($this->empty_route))
			->set_exception_routes(array(200 => $this->empty_route));
		
		$router->route();
	}
	
	public function test_route__finds_exception_404_route_if_path_empty() {
		$exception_controller = 'exception_controller';
		
		$exception_404_route = $this->getMock('kin\route', array('get_controller'), array(), '', false);
		$exception_404_route->expects($this->once())
			->method('get_controller')
			->will($this->returnValue($exception_controller));
		
		$router = new router;
		$router->set_request_method('GET')
			->set_routes(array($this->empty_route))
			->set_exception_routes(array(404 => $exception_404_route));
			
		$router->route();
		
		$this->assertEquals($exception_controller, $router->get_route()->get_controller());
	}
	
	public function test_route__finds_exception_404_route_if_request_method_empty() {
		$exception_controller = 'exception_controller';
		
		$exception_404_route = $this->getMock('kin\route', array('get_controller'), array(), '', false);
		$exception_404_route->expects($this->once())
			->method('get_controller')
			->will($this->returnValue($exception_controller));
		
		$router = new router;
		$router->set_path('/path/to/route')
			->set_routes(array($this->empty_route))
			->set_exception_routes(array(404 => $exception_404_route));
			
		$router->route();
		
		$this->assertEquals($exception_controller, $router->get_route()->get_controller());
	}
	
	public function test_route__finds_exception_404_route_if_no_routes_match() {
		$exception_controller = 'exception_controller';
		
		$exception_404_route = $this->getMock('kin\route', array('get_controller'), array(), '', false);
		$exception_404_route->expects($this->once())
			->method('get_controller')
			->will($this->returnValue($exception_controller));
		
		$unmatchable_route = $this->getMock('kin\route', array('get_method', 'get_compiled_route'), array(), '', false);
		$unmatchable_route->expects($this->once())
			->method('get_method')
			->will($this->returnValue('PUT'));
		$unmatchable_route->expects($this->once())
			->method('get_compiled_route')
			->will($this->returnValue('#^/$#i'));
		
		$router = new router;
		$router->set_path('/path/to/route')
			->set_request_method('GET')
			->set_routes(array($unmatchable_route))
			->set_exception_routes(array(404 => $exception_404_route));
			
		$router->route();
		
		$this->assertEquals($exception_controller, $router->get_route()->get_controller());
	}
	
	public function test_route__finds_matched_route() {
		$matchable_controller = 'route_controller';
		
		$exception_404_route = $this->empty_route;
		
		$matchable_route = $this->getMock('kin\route', array('get_method', 'get_compiled_route', 'get_controller', 'set_arguments'), array(), '', false);
		$matchable_route->expects($this->any())
			->method('get_method')
			->will($this->returnValue('GET'));
		$matchable_route->expects($this->once())
			->method('get_compiled_route')
			->will($this->returnValue('#^/$#i'));
		$matchable_route->expects($this->once())
			->method('get_controller')
			->will($this->returnValue($matchable_controller));
		$matchable_route->expects($this->once())
			->method('set_arguments')
			->will($this->returnValue($matchable_route));
		
		$router = new router;
		$router->set_path('/')
			->set_request_method($matchable_route->get_method())
			->set_routes(array($matchable_route))
			->set_exception_routes(array(404 => $exception_404_route));
			
		$router->route();
		
		$this->assertEquals($matchable_controller, $router->get_route()->get_controller());
	}
	
	public function test_route__finds_arguments_with_matched_route() {
		$arguments = array(10, 'victor+cherubini');
		$path = '/load/user/'.$arguments[0].'/and/set/his/name/to/'.$arguments[1];
		
		$exception_404_route = $this->empty_route;
		
		$matchable_route = $this->getMock('kin\route', array('get_method', 'get_compiled_route', 'set_arguments'), array(), '', false);
		$matchable_route->expects($this->any())
			->method('get_method')
			->will($this->returnValue('GET'));
		$matchable_route->expects($this->once())
			->method('get_compiled_route')
			->will($this->returnValue('#^/load/user/([\d]+)/and/set/his/name/to/([a-z0-9_\-/%\.\*\+]*)$#i'));
		$matchable_route->expects($this->once())
			->method('set_arguments')
			->will($this->returnValue($matchable_route));
		
		$router = new router;
		$router->set_path($path)
			->set_request_method($matchable_route->get_method())
			->set_routes(array($matchable_route))
			->set_exception_routes(array(404 => $exception_404_route));
			
		$router->route();
		
		$this->assertEquals($arguments, $router->get_arguments());
	}

}
