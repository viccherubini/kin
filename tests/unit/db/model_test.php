<?php namespace kintest\db;

use \kin\db\model as model,
	\kintest\testcase as testcase,
	\kinfixture\user as user;

require_once(__DIR__.'/../../testcase.php');
require_once(__DIR__.'/../../../kin/lib/db/model.php');
require_once(__DIR__.'/../../fixtures/user.php');

class model_test extends testcase {
	
	public function test___construct__loads_array_of_model_data() {
		$id = mt_rand(1, 1000);
		$identifier = uniqid();
		
		$model = new user(array('id' => $id, 'identifier' => $identifier));
		
		$this->assertEquals($id, $model->id);
		$this->assertEquals($identifier, $model->identifier);
	}
	
	public function test___construct__loads_object_of_model_data() {
		$id = mt_rand(1, 1000);
		$identifier = uniqid();
		
		$load = new \stdClass;
		$load->id = $id;
		$load->identifier = $identifier;
		
		$model = new user($load);
		
		$this->assertEquals($id, $model->id);
		$this->assertEquals($identifier, $model->identifier);
	}
	
	
	
	public function test___call__method_without_argument_is_getter() {
		$id = mt_rand(1, 1000);
		$identifier = uniqid();
		
		$model = new user(array('id' => $id, 'identifier' => $identifier));
		
		$this->assertEquals($id, $model->get_id());
		$this->assertEquals($identifier, $model->get_identifier());
	}
	
	public function test___call__method_with_argument_is_setter() {
		$id = mt_rand(1, 1000);
		
		$model = new user;
		$model->set_id($id);
		
		$this->assertEquals($id, $model->get_id());
	}
	


	public function test___isset__is_set_if_property_exists() {
		$model = new user;
		$this->assertTrue(isset($model->id));
	}

	public function test___isset__is_not_set_if_property_exists() {
		$model = new user;
		$this->assertFalse(isset($model->_id_3));
	}



	public function test___set__can_set_for_properties_that_exist() {
		$id = mt_rand(1, 1000);

		$model = new user;
		$model->id = $id;

		$this->assertEquals($id, $model->id);
	}

	public function test___set__can_not_set_for_properties_that_dont_exist() {
		$_id_3 = mt_rand(1, 1000);

		$model = new user;
		$model->_id_3 = $_id_3;

		$this->assertInternalType('null', $model->_id_3);
	}
	


	public function test_load__builds_fields_list() {
		$model = new user;
		
		$this->assertObjectHasAttribute('id', $model);
	}

	public function test_load__loads_into_fields_that_exist() {
		$id = mt_rand(1, 1000);

		$model = new user;
		$model->load(array('id' => $id));
		
		$this->assertEquals($id, $model->id);
	}

	public function test_load__calls_load_method_for_model_arrays() {
		$id = mt_rand(1, 1000);

		$model = new user(array('id' => $id));
		
		$this->assertArrayHasKey(0, $model->friends);
		$this->assertGreaterThan(0, $model->friends[0]->id);
	}

	
	public function test_disable__toggles_status_flag() {
		$model = new user(array('status' => model::status_enabled));
		$model->disable();
		
		$this->assertTrue($model->is_disabled());
	}
	
	public function test_enable__toggles_status_flag() {
		$model = new user(array('status' => model::status_disabled));
		$model->enable();
		
		$this->assertTrue($model->is_enabled());
	}
	
}
