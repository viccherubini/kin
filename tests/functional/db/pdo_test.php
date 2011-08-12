<?php namespace kintest\db;
declare(encoding='UTF-8');

use \kin\db\pdo as pdo,
	\kin\db\model as model,
	\kintest\testcase as testcase,
	\kinfixture\fixture1 as fixture1;

require_once(__DIR__.'/../../testcase.php');
require_once(__DIR__.'/../../../kin/lib/db/model.php');
require_once(__DIR__.'/../../../kin/lib/db/pdo.php');

require_once(__DIR__.'/../../fixtures/fixture1.php');

class pdo_test extends testcase {
	
	protected $pdo = null;
	
	public function test_save__inserts_model_to_database() {
		$fixture1 = $this->create_fixture1();
		
		$this->assertGreaterThan(0, $fixture1->id);
		$this->assertNotEmpty($fixture1->id);
	}
	
	public function _test_save__upates_model_in_database() {
		$fixture1 = $this->create_fixture1();
		
		$this->assertGreaterThan(0, $fixture1->id);
		$this->assertNotEmpty($fixture1->id);
		
		$identifier = mt_rand(1, 100000);
		$fixture1->set_identifier($identifier);
		$fixture1 = $this->pdo->save($fixture1);
		
		$this->assertNotEmpty($fixture1->updated);
	}
	
	public function _test_save__inserts_multiple_models_to_database() {
		$fixture1 = $this->create_fixture1();
		$fixture2 = $this->create_fixture1();
		
		$this->assertGreaterThan(0, $fixture1->id);
		$this->assertGreaterThan(0, $fixture2->id);
		$this->assertNotEquals($fixture1->id, $fixture2->id);
	}
	
	public function _test_delete__removes_object_from_database() {
		$fixture1 = $this->create_fixture1();
		$this->pdo->delete($fixture1);
		
		$this->assertEmpty($this->pdo->select('select * from fixture1')->fetchAll());
	}
	
	public function _test_prep__prepares_query() {
		$this->pdo->prep('select * from fixture1');
		$this->assertInternalType('object', $this->pdo->get_stmt());
	}
	
	public function _test_select__finds_object() {
		$fixture1 = $this->create_fixture1();
		$fixture2 = $this->pdo->select('select * from fixture1 where id = :id',
			array('id' => $fixture1->id))
			->fetchObject('\kinfixture\fixture1');

		$this->assertEquals($fixture1->id, $fixture2->id);
		$this->assertEquals($fixture1->identifier, $fixture2->identifier);
	}
	
	public function _test_select__returns_null_on_invalid_query() {
		$fixture1 = $this->create_fixture1();
		
		$invalid_fixture = $this->pdo->select('select * from invalid_fixture where id = :id',
			array('id' => $fixture1->id));
			
		$this->assertInternalType('null', $invalid_fixture);
	}
	
	
	
	
	protected function create_fixture1() {
		$name = uniqid();
		$identifier = mt_rand(1, 100000);
		
		$fixture1 = new fixture1;
		$fixture1->set_name($name)
			->set_identifier($identifier);

		return($this->pdo->save($fixture1));
	}
}