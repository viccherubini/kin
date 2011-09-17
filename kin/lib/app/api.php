<?php namespace kin\app;

class api {

	public $settings = null;

	public function __construct() {
	
	}
	
	public function __destruct() {
	
	}

	public function attach_settings(settings $settings) {
		$this->settings = $settings;
		return($this);
	}
	
}