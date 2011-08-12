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
	
	protected $settings = array();
	
	public function setUp() {
		$settings_file = __DIR__.'/../../settings.ini';
		if (!is_file($settings_file)) {
			echo("Please copy the settings.ini.template file to settings.ini and update the values to reflect your test system.".PHP_EOL.PHP_EOL);
			exit(1);
		}
		$this->settings = parse_ini_file($settings_file, true);
	}
	
	public function test_save__inserts_model_to_database() {
		$fixture1 = $this->create_fixture();
		
		$this->assertGreaterThan(0, $fixture1->id);
		$this->assertNotEmpty($fixture1->id);
	}
	
	public function test_save__upates_model_in_database() {
		$fixture1 = $this->create_fixture();
		
		$this->assertGreaterThan(0, $fixture1->id);
		$this->assertNotEmpty($fixture1->id);
		
		$identifier = mt_rand(1, 100000);
		$fixture1->set_identifier($identifier);
		$fixture1 = $this->pdo->save($fixture1);
		
		$this->assertNotEmpty($fixture1->updated);
	}
	
	public function test_save__inserts_multiple_models_to_database() {
		$fixture1 = $this->create_fixture();
		$fixture2 = $this->create_fixture();
		
		$this->assertGreaterThan(0, $fixture1->id);
		$this->assertGreaterThan(0, $fixture2->id);
		$this->assertNotEquals($fixture1->id, $fixture2->id);
	}
	
	
	
	public function test_delete__removes_object_from_database() {
		$fixture1 = $this->create_fixture();
		$this->pdo->delete($fixture1);
		
		$this->assertEmpty($this->pdo->select('select * from fixture1')->fetchAll());
	}
	
	
	
	public function test_prep__prepares_query() {
		$this->pdo->prep('select * from fixture1');
		$this->assertInternalType('object', $this->pdo->get_stmt());
	}
	
	
	
	public function test_select__finds_object() {
		$fixture1 = $this->create_fixture();
		$fixture2 = $this->pdo->select('select * from fixture1 where id = :id',
			array('id' => $fixture1->id))
			->fetchObject('\kinfixture\fixture1');

		$this->pdo->close();

		$this->assertEquals($fixture1->id, $fixture2->id);
		$this->assertEquals($fixture1->identifier, $fixture2->identifier);
	}
	
	public function _test_select__returns_null_on_invalid_query() {
		$fixture1 = $this->create_fixture();
		
		$invalid_fixture = $this->pdo->select('select * from invalid_fixture where id = :id',
			array('id' => $fixture1->id));
		
		$this->assertInternalType('null', $invalid_fixture);
	}
	
	
	
	public function test_select_one__returns_single_model() {
		$this->create_x_fixtures(5);
		
		$fixture1 = $this->pdo->select_one('select * from fixture1 where id = :id', '\kinfixture\fixture1', array('id' => 1));
		
		$this->assertGreaterThan(0, $fixture1->id);
	}
	
	public function test_select_one__returns_false_when_no_rows_found() {
		$this->create_x_fixtures(5);
		
		$fixture1 = $this->pdo->select_one('select * from fixture1 where id = :id', '\kinfixture\fixture1', array('id' => 'abc'));
		
		$this->assertFalse($fixture1);
	}
	
	
	
	public function test_select_exists__returns_true_when_row_exists() {
		$this->create_x_fixtures(5);
		
		$this->assertTrue($this->pdo->select_exists('select count(id) from fixture1 where id = :id', array('id' => 1)));
	}
	
	public function test_select_exists__returns_false_when_row_does_not_exist() {
		$this->create_x_fixtures(5);
		
		$this->assertFalse($this->pdo->select_exists('select count(id) from fixture1 where id = :id', array('id' => 'abc')));
	}
	
	
	
	public function test_modify__updates_data() {
		$new_identifier = uniqid();
		$fixture1 = $this->create_fixture();
		
		$modified = $this->pdo->modify('update fixture1 set identifier = :identifier', array('identifier' => $new_identifier));
		$this->assertTrue($modified);
		
		$found_fixture1 = $this->pdo->select_one('select * from fixture1 where id = :id', '\kinfixture\fixture1', array('id' => $fixture1->id));
		$this->assertEquals($new_identifier, $found_fixture1->identifier);
	}
	
	
	
	protected function create_fixture() {
		$name = uniqid();
		$identifier = mt_rand(1, 100000);
		
		$fixture1 = new fixture1;
		$fixture1->set_name($name)
			->set_identifier($identifier);

		return($this->pdo->save($fixture1));
	}
	
	protected function create_x_fixtures($x) {
		for ($i=0; $i<$x; $i++) {
			$this->create_fixture();
		}
		return(true);
	}
	
}