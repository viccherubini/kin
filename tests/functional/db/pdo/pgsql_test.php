<?php namespace kintest\db\pdo;
declare(encoding='UTF-8');

use \kin\db\pdo\pgsql as pgsql,
	\kin\db\model as model,
	\kintest\db\pdo_test as pdo_test,
	\kinfixture\fixture1 as fixture1;

require_once(__DIR__.'/../pdo_test.php');
require_once(__DIR__.'/../../../../kin/lib/db/pdo/pgsql.php');

class pgsql_test extends pdo_test {

	public function setUp() {
		$sql_setup = file_get_contents(__DIR__.'/../../../fixtures/scripts/pgsql_setup.sql');
		
		$this->pdo = new pgsql('pgsql:host=localhost;dbname=kintest;user=postgres;password=dba89da');
		$this->pdo->exec($sql_setup);
	}
	
	public function tearDown() {
		$sql_teardown = file_get_contents(__DIR__.'/../../../fixtures/scripts/pgsql_teardown.sql');
		$this->pdo->exec($sql_teardown);
	}
	
}