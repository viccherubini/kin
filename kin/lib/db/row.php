<?php namespace kin\db;

// This class is necessary because \StdClass can not be populated by a PDO fetchObject()
// because it does not have a constructor.
class row extends \StdClass {
	
	public function __construct() {
	}
	
}