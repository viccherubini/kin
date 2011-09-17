<?php namespace kintest;
declare(encoding='UTF-8');

class index_controller extends \kin\app\controller {

	public function action_get_index() {
		$this->set_content_type('');
		$this->render('response');
	}

}