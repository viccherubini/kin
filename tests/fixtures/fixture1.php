<?php namespace kinfixture;
declare(encoding='utf-8');

class fixture1 extends \kin\db\model {
	
	protected $members = array(
		'id' => self::type_int,
		'created' => self::type_string,
		'updated' => self::type_string,
		'name' => self::type_string,
		'identifier' => self::type_string,
		'status' => self::type_int
	);

}