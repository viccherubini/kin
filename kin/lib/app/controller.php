<?php namespace kin\app;
declare(encoding='UTF-8');

class controller {
	
	private $headers = array();
	private $payload = array();
	
	private $content_type = '';
	private $response_code = 200;
	private $view = '';
	
	const response_301 = 301;
	const response_302 = 302;
	
	public function __construct() {
		$this->payload = array(
			'contents' => array(),
			'errors' => array(),
			'models' => array(),
			'message' => ''
		);
	}
	
	public function __destruct() {
		$this->payload = array();
	}
	
	public function __set($k, $v) {
		$this->payload['contents'][$k] = $v;
		return($this);
	}
	
	public function __get($k) {
		if (array_key_exists($k, $this->payload['contents'])) {
			return $this->payload['contents'][$k];
		}
		return null;
	}
	
	public function add_header($header, $value) {
		$header = strtolower(trim($header));
		if ('content-type' !== $header) {
			$this->headers[$header] = $value;
		} else {
			$this->set_content_type($value);
		}
		return($this);
	}
	
	public function add_error($field, $error) {
		$field = trim($field);
		$this->payload['errors'][$field] = trim($error);
		return($this);
	}
	
	public function add_message($e) {
		$message = $e;
		if (is_object($e) && ($e instanceof \Exception)) {
			$message = $e->getMessage();
		}
		$this->payload['message'] = $message;
		return($this);
	}

	public function add_model(\kin\db\model $model) {
		if (is_object($model)) {
			$this->payload['models'][] = array(get_class($model), $model->get_values());
		}
		return($this);
	}
	
	public function add_models(array $models) {
		foreach ($models as $model) {
			$this->add_model($model);
		}
		return($this);
	}
	
	public function has_content_type() {
		return(!empty($this->content_type));
	}
	
	public function redirect($location, $response_code=self::response_302) {
		if (!in_array($response_code, array(self::response_301, self::response_302), true)) {
			$response_code = self::response_302;
		}
		
		$this->add_header('location', $location)
			->set_response_code($response_code);
		return($this);
	}
	
	public function register($k, $v) {
		return($this->__set($k, $v));
	}
	
	public function render($view) {
		$this->view = trim($view);
		return($this);
	}

	
	
	public function set_content_type($content_type) {
		$this->content_type = strtolower($content_type);
		return($this);
	}
	
	public function set_response_code($response_code) {
		$this->response_code = (int)$response_code;
		return($this);
	}
	
	
	
	public function get_headers() {
		return($this->headers);
	}
	
	public function get_payload() {
		return($this->payload);
	}
	
	public function get_content_type() {
		return($this->content_type);
	}
	
	public function get_response_code() {
		return($this->response_code);
	}
	
	public function get_view() {
		return($this->view);
	}
	
}