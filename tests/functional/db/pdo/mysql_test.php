<?php namespace kintest\db\pdo;
declare(encoding='UTF-8');

use \kin\db\pdo\sqlite as mysql,
	\kintest\db\pdo_test as pdo_test,
	\kinfixture\fixture1 as fixture1;

require_once(__DIR__.'/../pdo_test.php');
require_once(__DIR__.'/../../../../kin/lib/db/pdo/sqlite.php');

class mysql_test extends pdo_test {

	public function setUp() {
		parent::setUp();
		$s = $this->settings['mysql'];
		
		$sql_setup = file_get_contents(__DIR__.'/../../../fixtures/scripts/mysql_setup.sql');
		
		$this->pdo = new mysql('mysql:host='.$s['host'].';dbname='.$s['dbname'], $s['user'], $s['password']);
		$this->pdo->exec($sql_setup);
	}
	
	public function tearDown() {
		$sql_teardown = file_get_contents(__DIR__.'/../../../fixtures/scripts/mysql_teardown.sql');
		$this->pdo->exec($sql_teardown);
	}
	
}