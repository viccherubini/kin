<?php namespace metrics;
declare(encoding='UTF-8');

class index_controller extends \jolt\controller {

	public function action_index() {
		$this->register('hello_world', 'Hello, world!');
		$this->render('index/index');
	}
	
	public function action_index_post() {
		$form = new \stdClass;
		$form->id = 10;
		$form->username = 'vic';
		
		$this->register('abc', 'this is the abc value')
			->register('form', $form);
			
		$this->render('index/post');
	}

}