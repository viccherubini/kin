<?php namespace kin\db\pdo;
use \kin\db\pdo as pdo;

require_once(__DIR__.'/../pdo.php');

class sqlite extends pdo {
	
	protected function build_insert_query_field_string() {
		$this->query_fields = implode(',', array_keys($this->model_values));
		return($this);
	}
	
	protected function build_insert_query_value_string() {
		$this->query_values = implode(',', array_map(function($v) {
			return(":{$v}");
		}, $this->model_fields));
		return($this);
	}
	
	protected function build_insert_query() {
		$this->query = 'INSERT INTO '.$this->table.'('.$this->query_fields.') VALUES ('.$this->query_values.')';
		return($this);
	}
	
	
	
	
	protected function build_update_query_field_string() {
		$this->query_fields = implode(',', array_map(function($v) {
			return ("{$v} = :{$v}");
		}, $this->model_fields));
		return($this);
	}
	
	protected function build_update_query() {
		$this->query = 'UPDATE '.$this->table.' SET '.$this->query_fields.' WHERE id = :pid';
		return($this);
	}
	
}