<?php namespace kintest;
declare(encoding='UTF-8');

use \kin\db\pdo as pdo;

require_once(__DIR__.'/../../testcase.php');
require_once(__DIR__.'/../../../kin/lib/db/pdo.php');

//require_once(__DIR__.'/../../fixtures/fixture1.php');

class pdo_test extends testcase {
	
	public function test___construct__builds_active_connection_if_dsn_present() {
		$pdo = new pdo('sqlite::memory:');
		
		$this->assertTrue($pdo->is_connected());
	}
	
	public function _test_now__returns_iso_long_date_format() {
		$pdo = new pdo('');
		
	}
	
	
	public function test_find_all__returns_empty_array_if_no_statement_was_prepare() {
		$pdo = new pdo('');
		
		$this->assertEmpty($pdo->find_all());
	}
	
	
}