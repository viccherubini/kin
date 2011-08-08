<?php namespace kintest;
declare(encoding='UTF-8');

use \kin\db\model as model,
	\kinfixture\fixture1 as fixture1;

require_once(__DIR__.'/../../testcase.php');
require_once(__DIR__.'/../../../kin/lib/db/model.php');

require_once(__DIR__.'/../../fixtures/fixture1.php');

class model_test extends testcase {
	
	public function test___construct__automatically_compiles_members() {
		$fixture1 = new fixture1;
		
		$this->assertTrue(isset($fixture1->id));
		$this->assertTrue($fixture1->is_compiled());
	}
	
	public function test___construct__loads_array_of_model_data() {
		$id = mt_rand(1, 1000);
		$identifier = uniqid();
		
		$fixture1 = new fixture1(array('id' => $id, 'identifier' => $identifier));
		
		$this->assertTrue($fixture1->is_compiled());
		$this->assertEquals($id, $fixture1->id);
		$this->assertEquals($identifier, $fixture1->identifier);
	}
	
	public function test___construct__loads_object_of_model_data() {
		$id = mt_rand(1, 1000);
		$identifier = uniqid();
		
		$model = new \stdClass;
		$model->id = $id;
		$model->identifier = $identifier;
		
		$fixture1 = new fixture1($model);
		
		$this->assertTrue($fixture1->is_compiled());
		$this->assertEquals($id, $fixture1->id);
		$this->assertEquals($identifier, $fixture1->identifier);
	}
	
	
	
	public function test___call__method_without_argument_is_getter() {
		$id = mt_rand(1, 1000);
		$identifier = uniqid();
		
		$fixture1 = new fixture1(array('id' => $id, 'identifier' => $identifier));
		
		$this->assertEquals($id, $fixture1->get_id());
		$this->assertEquals($identifier, $fixture1->get_identifier());
	}
	
	public function test___call__method_with_argument_is_setter() {
		$id = mt_rand(1, 1000);
		
		$fixture1 = new fixture1;
		$fixture1->set_id($id);
		
		$this->assertEquals($id, $fixture1->get_id());
	}
	
	
	
	public function test_copy__overwrites_all_data_from_second_model() {
		$id1 = mt_rand(1, 1000);
		$id2 = mt_rand(1, 1000);
		
		$identifier1 = uniqid();
		
		$fixture1 = new fixture1;
		$fixture1->set_id($id1);
		$fixture1->set_identifier($identifier1);
		
		$fixture2 = new fixture1;
		$fixture2->set_id($id2);
		
		$this->assertEquals($id1, $fixture1->get_id());
		$this->assertEquals($identifier1, $fixture1->get_identifier());
		
		$fixture1->copy($fixture2);
		
		$this->assertEquals($id2, $fixture1->get_id());
		$this->assertEquals('', $fixture1->get_identifier());
	}
	
	
	
	public function test_merge__only_copies_over_empty_properties() {
		$id1 = mt_rand(1, 1000);
		$id2 = mt_rand(1, 1000);
		
		$identifier = uniqid();
		
		$fixture1 = new fixture1;
		$fixture2 = new fixture1;
		
		$fixture1->set_id($id1);
		
		$fixture2->set_id($id2);
		$fixture2->set_identifier($identifier);
		
		$fixture1->merge($fixture2);
		
		$this->assertEquals($id1, $fixture1->get_id());
		$this->assertEquals($identifier, $fixture1->get_identifier());
	}
	
	
	
	public function test_disable__toggles_status_flag() {
		$fixture1 = new fixture1(array('status' => fixture1::status_enabled));
		
		$fixture1->disable();
		
		$this->assertTrue($fixture1->is_disabled());
	}
	
	public function test_enable__toggles_status_flag() {
		$fixture1 = new fixture1(array('status' => fixture1::status_disabled));
		
		$fixture1->enable();
		
		$this->assertTrue($fixture1->is_enabled());
	}
	
	
	
	public function test_extract__returns_arguments_values() {
		$id = mt_rand(1, 1000);
		$identifier = uniqid();
		
		$fixture1 = new fixture1(array('id' => $id, 'identifier' => $identifier, 'status' => fixture1::status_enabled));
		
		$members = $fixture1->extract('id', 'identifier');
		
		$this->assertEquals($id, $members['id']);
		$this->assertEquals($identifier, $members['identifier']);
	}
	
}