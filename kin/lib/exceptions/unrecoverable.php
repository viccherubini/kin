<?php namespace kin\exception;
declare(encoding='UTF-8');

class unrecoverable extends \Exception {
	public function __construct($message) {
		parent::__construct("[Unrecoverable Exception] {$message}");
	}
}