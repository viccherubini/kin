<?php namespace jolt;
declare(encoding='UTF-8');

class payload {

	public $contents = array();
	public $errors = array();
	public $models = array();
	
	public $message = '';
	public $name = '';
	public $object = '';
	public $redirect = '';
	public $token = '';

	public function __construct() {
	}
	
	public function __destruct() {
	}
	
	public function __call($method, $argv) {
		if (0 === count($argv)) {
			return $this->__get($method);
		} else {
			return $this->__set($method, current($argv));
		}
	}
	
	public function add_exception($e) {
		$message = $e;
		if (is_object($e) && ($e instanceof \Exception)) {
			$message = $e->getMessage();
		}
		$this->message = $message;
		return $this;
	}

	public function add_model(model $model) {
		$this->models[] = $model->get_values();
		$this->name = get_class($model);
		return $this;
	}
	
	public function add_response_models(array $models) {
		if (count($models) > 0) {
			reset($models);
			$model_values = array_reduce($models, function($mvs, $e) {
				if (is_object($e)) {
					$mvs[] = $e->get_values();
				}
				return $mvs;
			});
			
			$this->models = $model_values;
			$this->name = get_class($models[0]);
		}
		return $this;
	}
	
	public function to_array() {
		return array(
			'contents' => $this->contents,
			'errors' => $this->errors,
			'models' => $this->models,
			'message' => $this->message,
			'name' => $this->name,
			'object' => $this->object,
			'redirect' => $this->redirect,
			'token' => $this->token
		);
	}
	
}