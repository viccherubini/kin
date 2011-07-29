<?php namespace metrics;
declare(encoding='UTF-8');

class index_controller extends \jolt\controller {

	public function action_index() {
		$this->register('hello_world', 'Hello, world!');
	}

}
