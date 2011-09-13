<?php namespace kintest\db;
declare(encoding='UTF-8');

use \kin\db\model as model,
	\kintest\testcase as testcase;

require_once(__DIR__.'/../../testcase.php');
require_once(__DIR__.'/../../../kin/lib/db/model.php');

class model_test extends testcase {
	
	public function test___construct__loads_array_of_model_data() {
		$id = mt_rand(1, 1000);
		$identifier = uniqid();
		
		$model = new model(array('id' => $id, 'identifier' => $identifier));
		
		$this->assertEquals($id, $model->id);
		$this->assertEquals($identifier, $model->identifier);
	}
	
	public function test___construct__loads_object_of_model_data() {
		$id = mt_rand(1, 1000);
		$identifier = uniqid();
		
		$load = new \stdClass;
		$load->id = $id;
		$load->identifier = $identifier;
		
		$model = new model($load);
		
		$this->assertEquals($id, $model->id);
		$this->assertEquals($identifier, $model->identifier);
	}
	
	
	
	public function test___call__method_without_argument_is_getter() {
		$id = mt_rand(1, 1000);
		$identifier = uniqid();
		
		$model = new model(array('id' => $id, 'identifier' => $identifier));
		
		$this->assertEquals($id, $model->get_id());
		$this->assertEquals($identifier, $model->get_identifier());
	}
	
	public function test___call__method_with_argument_is_setter() {
		$id = mt_rand(1, 1000);
		
		$model = new model;
		$model->set_id($id);
		
		$this->assertEquals($id, $model->get_id());
	}
	
	
	
	public function test_disable__toggles_status_flag() {
		$model = new model(array('status' => model::status_enabled));
		$model->disable();
		
		$this->assertTrue($model->is_disabled());
	}
	
	public function test_enable__toggles_status_flag() {
		$model = new model(array('status' => model::status_disabled));
		$model->enable();
		
		$this->assertTrue($model->is_enabled());
	}
	
}