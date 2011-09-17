<?php namespace kinfixture;

class user extends \kin\db\model {

	public $id = 0;
	public $created = '';
	public $updated = '';
	public $name = '';
	public $identifier = '';
	public $status = 0;

	public $friends = array();


	public function load_friends() {
		if ($this->id > 0) {
				$this->friends[0] = new \StdClass;
				$this->friends[0]->id = mt_rand(1, 1000);
		}
		return($this);
	}

}
