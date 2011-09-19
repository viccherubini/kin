<?php namespace kin\exception;

class unrecoverable extends \Exception {
	public function __construct($message) {
		parent::__construct("[Unrecoverable Exception] {$message}");
	}
}