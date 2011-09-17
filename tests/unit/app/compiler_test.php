<?php namespace kintest\app;
declare(encoding='UTF-8');

use \kin\app\compiler as compiler,
	\kintest\testcase as testcase;

require_once('vfsStream/vfsStream.php');

require_once(__DIR__.'/../../testcase.php');
require_once(__DIR__.'/../../../kin/lib/app/compiler.php');

class compiler_test extends testcase {
	
	private $class = 'index_controller';
	private $path = 'controllers';

	/**
	 * @expectedException \kin\exception\unrecoverable
	 */
	public function test_compile__requires_class() {
		$compiler = new compiler;
		
		$compiler->compile();
	}
	
	/**
	 * @expectedException \kin\exception\unrecoverable
	 */
	public function test_compile__requires_file() {
		$compiler = new compiler;
		$compiler->set_class($this->class);
		
		$compiler->compile();
	}
	
	/**
	 * @expectedException \kin\exception\unrecoverable
	 */
	public function test_compile__requires_file_to_exist() {
		$compiler = new compiler;
		$compiler->set_class($this->class)
			->set_path($this->path)
			->set_file($this->get_file());
		
		$compiler->compile();
	}
	
	/**
	 * @expectedException \kin\exception\unrecoverable
	 */
	public function test_compile__requires_class_to_exist() {
		$file = $this->get_file();
		
		\vfsStreamWrapper::register();
		\vfsStreamWrapper::setRoot(new \vfsStreamDirectory($this->path));

		$path = \vfsStreamWrapper::getRoot();
		$path->addChild(\vfsStream::newFile($file)->withContent('<?php class abc { }'));

		$file_url = \vfsStream::url($this->path.DIRECTORY_SEPARATOR.$file);

		$compiler = new compiler;
		$compiler->set_class($this->class)
			->set_file($file_url);

		$compiler->compile();
	}
	
	public function test_compile__controller_built() {
		$file = $this->get_file();
		
		\vfsStreamWrapper::register();
		\vfsStreamWrapper::setRoot(new \vfsStreamDirectory($this->path));

		$path = \vfsStreamWrapper::getRoot();
		$path->addChild(\vfsStream::newFile($file)->withContent('<?php class '.$this->class.' { }'));

		$file_url = \vfsStream::url($this->path.DIRECTORY_SEPARATOR.$file);
		
		$compiler = new compiler;
		$compiler->set_class($this->class)
			->set_file($file_url);
			
		$compiler->compile();

		$this->assertTrue(is_object($compiler->get_controller()));
	}
	
	
	
	private function get_file() {
		return uniqid(true).'_'.$this->class.'.php';
	}
	
}
