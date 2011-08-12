<?php namespace kintest\db;
declare(encoding='UTF-8');

use \kin\db\pdo\sqlite as sqlite,
	\kintest\testcase as testcase;;

require_once(__DIR__.'/../../testcase.php');
require_once(__DIR__.'/../../../kin/lib/db/pdo/sqlite.php');

class pdo_test extends testcase {
	
	public function test___construct__builds_active_connection_if_dsn_present() {
		$pdo = new sqlite('sqlite::memory:');
		
		$this->assertTrue($pdo->is_connected());
	}
	
	public function test_find_all__returns_empty_array_if_no_statement_was_prepare() {
		$pdo = new sqlite('');
		
		$this->assertEmpty($pdo->find_all());
	}
	
}