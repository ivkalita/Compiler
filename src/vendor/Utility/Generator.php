<?php

namespace vendor\Utility;

use vendor\Exception\CompilerException;
use vendor\Utility\Uglificator;

class Generator
{
	const SUB_RANGE_ANONIM = "SUBRANGE";
	const ARRAY_ANONIM = "ARRAY";
	const POINTER_ANONIM = "POINTER";
	const RECORD_ANONIM = "RECORD";

	static public $generators = [];
	private $value = 0;

	public function __construct($initValue)
	{
		if ($initValue == null) {
			$this->value = 1;
		} else {
			$this->value = $initValue;
		}
	}

	public function next()
	{
		return $this->value++;
	}

	static public function getUglified($name)
	{
		if (!array_key_exists($name, self::$generators)) {
			self::initGenerator($name);
		}
		return Uglificator::twoVals($name, self::$generators[$name]->next());
	}

	static public function get($name)
	{
		if (!array_key_exists($name, self::$generators)) {
			self::initGenerator($name);
		}
		return self::$generators[$name]->next();
	}

	static public function initGenerator($name)
	{
		if (array_key_exists($name, self::$generators)) {
			throw new \Exception('Generator with name = "$name" already exists!');
		}
		self::$generators[$name] = new Generator(0);
	}
}