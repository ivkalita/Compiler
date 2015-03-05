<?php

namespace vendor\SemanticParser\Nodes;

use vendor\Utility\Generator;
use vendor\Utility\Console;

class SymPointerAnonimType extends SymPointerType
{
	public function __construct($scanner, $_symTable)
	{
		$identifier = Generator::getUglified(Generator::POINTER_ANONIM);
		parent::__construct($scanner, $_symTable, $identifier);
	}

	public function isAnonim()
	{
		return true;
	}

	public function printInfo($offset)
	{
		Console::write("{$offset}SymPointerAnonimType:\n");
		$offset .= '    ';
		Console::write("{$offset}{$this->identifier}\n");
		$this->type->printInfo($offset);
	}

	public function isConvertableTo($type)
	{
		return parent::isConvertableTo($type);
	}
}