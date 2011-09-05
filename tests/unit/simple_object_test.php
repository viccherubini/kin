<?php namespace kintest;
declare(encoding='UTF-8');

use \kin\simple_object as simple_object;

require_once(__DIR__.'/../testcase.php');
require_once(__DIR__.'/../../kin/lib/simple_object.php');

class simple_object_test extends testcase {
	
	public function test___construct__adds_array() {
		$id = mt_rand(1, 1000);
		
		$so = new simple_object(array('id' => $id));
		
		$this->assertEquals($id, $so->id);
	}
	
	public function test___get__returns_null_for_missing_keys() {
		$so = new simple_object(array());
	
		$this->assertNull($so->id);
	}
	
	public function test___set__adds_key_to_hash() {
		$id = mt_rand(1, 1000);
		
		$so = new simple_object(array());
		$so->id = $id;
		
		$this->assertEquals($id, $so->id);
	}
	
}