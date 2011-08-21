<?php namespace kin\app;
declare(encoding='UTF-8');

class controller {
	
	public $helper = null;
	
	private $headers = array();
	private $payload = array();
	
	private $content_type = '';
	private $response_code = 200;
	private $view = '';

	protected $request = null;
	
	const response_200 = 200;
	const response_201 = 201;
	
	const response_301 = 301;
	const response_302 = 302;
	
	const response_400 = 400;
	const response_403 = 403;
	const response_404 = 404;
	const response_405 = 405;
	const response_409 = 409;
	
	const response_500 = 500;
	const response_501 = 500;
	
	public function __construct() {
		$this->payload = array(
			'contents' => array(),
			'errors' => array(
				'contents' => array(),
				'errors' => array(),
				'message' => ''
			),
			'models' => array(),
			'has_errors' => false
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
		return(null);
	}
	
	
	
	public function attach_helper(helper $helper) {
		$this->helper = $helper;
		return($this);
	}
	
	public function attach_request(\kin\http\request $request) {
		$this->request = $request;
		return($this);
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
	
	public function add_contents(array $contents) {
		$this->payload['contents'] = $contents;
		return($this);
	}
	
	public function add_error_contents(array $contents) {
		$this->toggle_has_errors();
		
		$this->payload['errors']['contents'] = $contents;
		return($this);
	}
	
	public function add_error($field, $error) {
		$this->toggle_has_errors();
		
		$this->payload['errors']['errors'][$field] = trim($error);
		return($this);
	}
	
	public function add_message($e) {
		$this->toggle_has_errors();
		
		$message = $e;
		if (is_object($e) && ($e instanceof \Exception)) {
			$message = $e->getMessage();
		}
		$this->payload['errors']['message'] = $message;
		return($this);
	}

	public function add_model(\kin\db\model $model) {
		if (is_object($model)) {
			$this->payload['models'][] = $model->get_values();
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
	
	public function has_errors() {
		return($this->payload['has_errors']);
	}
	
	public function has_view() {
		return(!empty($this->view));
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
	
	
	
	private function toggle_has_errors() {
		$this->payload['has_errors'] = true;
		return($this);
	}
	
}