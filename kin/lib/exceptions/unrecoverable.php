<?php namespace kin\exception;

class unrecoverable extends \Exception {
	public function __construct($message, $code=0) {
		parent::__construct("[Unrecoverable Exception] {$message}", (int)$code);
	}
}