<?php namespace kin\db;

class model extends \StdClass {

	private $__fields = array();
	private $__xrels = array();

	const status_enabled = 1;
	const status_disabled = 0;
	
	const with_separator = '/';
	
	public function __construct($model=null, $with='') {
		$this->load($model, $with);
	}

	public function __call($method, $argv) {
		$k = substr(strtolower($method), 4);
		if (0 === count($argv)) {
			$v = $this->__get($k);
			return($v);
		} else {
			$v = current($argv);
			$this->__set($k, $v);
			return($this);
		}
	}

	public function __isset($k) {
		return(property_exists($this, $k));
	}

	public function __set($k, $v) {
		if (array_key_exists($k, $this->__fields)) {
			$this->$k = $v;
		}
		return(true);
	}

	public function __get($k) {
		if (isset($this->$k)) {
			return($this->$k);
		}
		return(null);
	}

	public function load($model, $with='') {
		$this->compile_fields()
			->load_field_values($model);

		$withs = explode(self::with_separator, $with);
		foreach ($this->__xrels as $xrel => $is_xrel) {
			$load_method = "hydrate_{$xrel}";
			
			if ($xrel == $withs[0] && method_exists($this, $load_method)) {
				array_shift($withs);
				$this->$load_method(implode(self::with_separator, $withs));
			}
		}
		return($this);
	}

	public function disable() {
		return($this->set_status(self::status_disabled));
	}

	public function enable() {
		return($this->set_status(self::status_enabled));
	}



	// Traits
	public function is_saved() {
		return($this->id > 0);
	}

	public function is_disabled() {
		return($this->status == self::status_disabled);
	}
	
	public function is_enabled() {
		return($this->status == self::status_enabled);
	}


	// Getters
	public function get_model_values() {
		$model_values = array();
		foreach ($this as $k => $v) {
			if ($this->is_field($v)) {
				$model_values[$k] = $v;
			}
		}
		return($model_values);
	}

	public function get_values() {
		$model_values = $this->get_model_values();
		foreach ($this as $k => $v) {
			if (is_array($v) && '__fields' != $k && '__xrels' != $k) {
				$model_values[$k] = array_map(function($e) {
					return($e->get_values());
				}, $v);
			}
		}
		return($model_values);
	}


	
	private function compile_fields() {
		foreach ($this as $k => $v) {
			if ($this->is_field($v)) {
				$this->__fields[$k] = true;
			} elseif ($this->is_xrel($k, $v)) {
				$this->__xrels[$k] = true;
			}
		}
		return($this);
	}
	
	private function load_field_values($model) {
		if (is_array($model) || is_object($model)) {
			foreach ($model as $k => $v) {
				$this->__set($k, $v);
			}
		}
		return($this);
	}
	
	private function is_field($v) {
		return(is_scalar($v) || is_null($v));
	}
	
	private function is_xrel($k, $v) {
		return(0 !== stripos($k, '_') && is_array($v));
	}
	
}