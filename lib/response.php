<?php namespace jolt;
declare(encoding='UTF-8');

class response {

	private $content_type = 'text/html';
	private $response_code = 405;
	
	private $headers = array();
	
	private $payload = null;

	public function __construct() {
		$this->headers = array(
			'content-type' => $this->content_type
		);
	}
	
	public function __destruct() {
	
	}

}