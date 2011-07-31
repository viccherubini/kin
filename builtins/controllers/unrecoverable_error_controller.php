<?php namespace jolt;
declare(encoding='UTF-8');

class unrecoverable_error_controller extends controller {
	
	public function action_unrecoverable_error() {
		$this->render('unrecoverable-error');
	}

}