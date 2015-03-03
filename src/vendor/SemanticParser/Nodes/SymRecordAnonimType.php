<?php

namespace vendor\SemanticParser\Nodes;

use vendor\Utility\Generator;
use vendor\Utility\Console;

class SymRecordAnonimType extends SymRecordType
{
	public function __construct($scanner, $_symTable)
	{
		$identifier = Generator::getUglified(Generator::RECORD_ANONIM);
		parent::__construct($scanner, $_symTable, $identifier);
	}

	public function isAnonim()
	{
		return true;
	}

	public function printInfo($offset)
	{
		Console::write("{$offset}SymRecordAnonimType:\n");
		$offset .= '    ';
		Console::write("{$offset}{$this->identifier}\n");
		$this->symTable->printInfo($offset);
	}
}