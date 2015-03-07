<?php

namespace vendor\Utility;

class Globals
{
	static public $loopDepth = 0;
	static public $funcDepth = 0;
	static public $switchDepth = 0;

	static private $simpleTypesCache = [
		'integer' => null,
		'boolean' => null,
		'real'    => null,
		'string'  => null
	];

	static private $globalSymTable = null;

	static public function init($symTable)
	{
		self::$globalSymTable = $symTable;
	}

	static public function getSimpleType($type)
	{
		if (!array_key_exists($type, self::$simpleTypesCache)) {
			throw new \Exception("INTERNAL TYPE $type DOES NOT EXIST");
		}
		if (self::$simpleTypesCache[$type] == null) {
			self::$simpleTypesCache[$type] = self::$globalSymTable->findRecursive($type);
		}
		return self::$simpleTypesCache[$type];
	}
}