<?php namespace kin\db;
declare(encoding='UTF-8');

abstract class pdo extends \PDO {

	protected $model = null;
	protected $stmt = null;

	protected $query = '';
	protected $query_hash = '';
	protected $query_fields = '';
	protected $query_values = '';
	
	protected $connected = false;
	
	protected $model_fields = array();
	protected $model_values = array();

	// These are duplicated in \kin\db\model as class constants.
	public $status_enabled = 1;
	public $status_disabled = 0;

	public function __construct($dsn, $username=null, $password=null, $options=array()) {
		if (!empty($dsn)) {
			parent::__construct($dsn, $username, $password, $options);
			
			$this->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_SILENT);
			$this->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
			
			$this->connected = true;
		}
	}

	// Helper functions
	public function id() {
		return($this->lastInsertId());
	}

	public function now($time=0, $short=false) {
		$time = (0 === $time ? time() : $time);
		$format = (false === $short ? 'Y-m-d H:i:s' : 'Y-m-d');

		return(date($format, $time));
	}

	public function prep($query) {
		$this->stmt = $this->prepare($query);
		return($this);
	}
	
	public function close() {
		if (is_object($this->stmt)) {
			$this->stmt->closeCursor();
		}
		return($this);
	}

	// Searching methods
	public function find_all($object='stdClass', $parameters=array()) {
		if (!is_object($this->stmt)) {
			return(array());
		}

		$this->bind_parameters($this->stmt, $parameters)
			->execute();
		return($this->stmt->fetchAll(\PDO::FETCH_CLASS, $object));
	}

	public function select($query, $parameters=array()) {
		$this->prep($query);
		if (is_object($this->stmt)) {
			$this->stmt = $this->bind_parameters($this->stmt, $parameters);
			$this->stmt->execute();
			return($this->stmt);
		}
		return(null);
	}
	
	public function select_one($query, $object='stdClass', $parameters=array()) {
		return($this->select($query, $parameters)
			->fetchObject($object));
	}
	
	public function select_exists($query, $parameters=array()) {
		$field_count = (int)$this->select($query, $parameters)
			->fetchColumn(0);
		return(0 === $field_count ? false : true);
	}

	// Modification methods
	public function delete(model $model) {
		if (!$model->is_saved()) {
			return(false);
		}

		$this->set_model($model)
			->set_table();
		$query = "DELETE FROM {$this->table} WHERE id = :id";

		return($this->modify($query, array('id' => $this->model->get_id())));
	}

	public function modify($query, $parameters=array()) {
		$this->stmt = $this->prep($query)
			->bind_parameters($this->stmt, $parameters);
		return($this->stmt->execute());
	}

	public function save(model $model) {
		$this->set_model($model)
			->set_table();

		if (!$this->model->is_saved()) {
			$is_insert = true;
			$this->set_model_created_date()
				->build_model_values('id', 'updated')
				->build_model_fields()
				->build_insert_query_field_string()
				->build_insert_query_value_string()
				->build_insert_query();
		} else {
			$is_insert = false;
			$this->set_model_updated_date()
				->build_model_values('created')
				->build_model_fields()
				->build_update_query_field_string()
				->build_update_query();
			
			$this->model_values['pid'] = $this->model_values['id'];
		}
		
		$this->create_query_hash()
			->execute_save();

		if ($is_insert) {
			$id = $this->id();
			$this->model->set_id($id);
		}

		return($this->model);
	}
	
	
	
	public function set_model(model $model) {
		$this->model = $model;
		return($this);
	}
	
	public function set_table($table='') {
		if (empty($table) && !is_null($this->model)) {
			$table = $this->determine_table_name_from_model();
		}
		$this->table = $table;
		return($this);
	}
	
	
	
	public function get_stmt() {
		return($this->stmt);
	}
	
	
	
	public function is_connected() {
		return($this->connected);
	}
	

	
	// Model manipulation
	private function set_model_created_date() {
		if (!is_null($this->model) && isset($this->model->created)) {
			$this->model->set_created($this->now());
		}
		return($this);
	}
	
	private function set_model_updated_date() {
		if (!is_null($this->model) && isset($this->model->updated)) {
			$this->model->set_updated($this->now());
		}
		return($this);
	}
	
	private function unset_model_value($field) {
		if (array_key_exists($field, $this->model_values)) {
			unset($this->model_values[$field]);
		}
		return($this);
	}
	
	private function build_model_values() {
		if (!is_null($this->model)) {
			$ignore_filter = array_combine(func_get_args(), func_get_args());
			$this->model_values = array_diff_key($this->model->get_values(), $ignore_filter);
		}
		return($this);
	}
	
	private function build_model_fields() {
		$this->model_fields = array_keys($this->model_values);
		return($this);
	}
	
	// Query manipulation
	private function create_query_hash() {
		$query_hash = sha1($this->query);
		if ($query_hash !== $this->query_hash) {
			$this->query_hash = $query_hash;
			$this->prep($this->query);
		}
		return($this);
	}
	
	private function execute_save() {
		if (is_object($this->stmt)) {
			$this->stmt = $this->bind_parameters($this->stmt, $this->model_values);
			$this->stmt->execute();
		}
		return($this);
	}

	private function bind_parameters(\PDOStatement $stmt, array $parameters) {
		foreach ($parameters as $parameter => $value) {
			$type = \PDO::PARAM_STR;
			if (is_int($value)) {
				$type = \PDO::PARAM_INT;
			} elseif (is_bool($value)) {
				$type = \PDO::PARAM_BOOL;
			} elseif (is_null($value)) {
				$type = \PDO::PARAM_NULL;
			}
			$stmt->bindValue($parameter, $value, $type);
		}
		return($stmt);
	}

	// Helper private methods
	private function determine_table_name_from_model() {
		$table = '';
		if (!is_null($this->model)) {
			$table_bits = explode('\\', strtolower(get_class($this->model)));
			$table = end($table_bits);
		}
		return($table);
	}
	
	
	
	// Abstract methods
	abstract protected function build_insert_query_field_string();
	abstract protected function build_insert_query_value_string();
	abstract protected function build_insert_query();
	
	abstract protected function build_update_query_field_string();
	abstract protected function build_update_query();

}