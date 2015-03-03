<?php

namespace vendor\SemanticParser\Nodes;

use vendor\Utility\Generator;
use vendor\Utility\Console;

class SymArrayAnonimType extends SymArrayType
{
	public function __construct($scanner, $_symTable)
	{
		$identifier = Generator::getUglified(Generator::ARRAY_ANONIM);
		parent::__construct($scanner, $_symTable, $identifier);
	}

	public function isAnonim()
	{
		return true;
	}

	public function printInfo($offset)
	{
		Console::write("{$offset}SymArrayAnonimType:\n");
		$offset .= '    ';
		Console::write("{$offset}{$this->identifier}\n");
		for ($i = 0; $i < count($this->dimensions); $i++) {
			$this->dimensions[$i]->printInfo($offset);
		}
		$this->type->printInfo($offset);
	}
}