<?php namespace kintest;
declare(encoding='UTF-8');

use \kin\view as view;

require_once('vfsStream/vfsStream.php');

require_once(__DIR__.'/../testcase.php');
require_once(__DIR__.'/../../kin/lib/view.php');

class view_test extends testcase {

	private $view = 'view';
	private $path = 'views';
	
	/**
	 * @expectedException \kin\exception\unrecoverable
	 */
	public function test_render__requires_file_to_exist() {
		$view = new view;
		$view->render();
	}
	
	public function test_render__renders_view() {
		$file = $this->get_file();
		$payload = array(
			'contents' => array(
				'name' => 'Vic'
			)
		);
		
		\vfsStreamWrapper::register();
		\vfsStreamWrapper::setRoot(new \vfsStreamDirectory($this->path));

		$path = \vfsStreamWrapper::getRoot();
		$path->addChild(\vfsStream::newFile($file)->withContent("<strong>Hello, <?php echo \$payload['contents']['name']; ?>!</strong>"));

		$file_url = \vfsStream::url($this->path.'/'.$file);
		
		$view = new view;
		$view->set_payload($payload)
			->set_file($file_url);
		
		$view->render();
		$rendering = $view->get_rendering();
		
		$this->assertNotEmpty($rendering);
		$this->assertTag(array('tag' => 'strong', 'content' => 'Hello, Vic!'), $rendering);
	}

	/**
	 * @dataProvider provider_path
	 */
	public function test_set_path__always_appends_directory_separator($path, $expected_path) {
		$view = new view;
		$view->set_path($path);
		
		$this->assertEquals($expected_path, $view->get_path());
	}
	
	
	
	
	public function provider_path() {
		return array(
			array('path/', 'path/'),
			array('path', 'path/'),
			array('path/to/views/', 'path/to/views/'),
			array('path/////', 'path/')
		);
	}
	
	
	
	private function get_file() {
		return(implode('.', array(uniqid(true).'_'.$this->view, view::ext)));
	}
	
}
