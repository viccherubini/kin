<?php namespace kin\app;
declare(encoding='UTF-8');

class helper {

	private $settings = null;

	public function __construct() {
	
	}
	
	
	
	public function attach_settings(settings $settings) {
		$this->settings = $settings;
		return($this);
	}
	
}