<?php

namespace vendor\SemanticParser\Nodes;
use vendor\TokenParser\Scanner;
use vendor\Exception\SemanticException;
use vendor\Utility\Console;

class SymSimpleType extends SymType
{
	public $type = null;
	static public $INTEGER = 'SYS??_INTEGER';
	static public $BOOLEAN = 'SYS??_BOOLEAN';
	static public $REAL = 'SYS??_REAL';
	static public $STRING = 'SYS??_STRING';

	public function __construct($identifier, $type)
	{
		parent::__construct();
		$this->identifier = $identifier;
		$this->type = $type;
	}

	public function isAnonim()
	{
		return false;
	}

	public function printInfo($offset)
	{
		Console::write("{$offset}SymSimpleType:\n");
		$offset .= '    ';
		Console::write("{$offset}{$this->identifier}\n");
		Console::write("{$offset}{$this->type}\n");
	}

	static public function equal($a, $b)
	{
		$bothAreSimple = get_class($a) == 'vendor\SemanticException\SymSimpleType' && get_class($b) == 'vendor\SemanticException\SymSimpleType';
		return
			$bothAreSimple &&
			($a->type == $b->type);
	}
}