<?php

namespace vendor\SemanticParser\Nodes;

use vendor\TokenParser\Scanner;
use vendor\SyntaxParser\Nodes\Node;
use vendor\Utility\Generator;
use vendor\Utility\Console;


class SymSubrangeAnonimType extends SymSubrangeType
{
	public $from = 0;
	public $to = 0;

	public function __construct($scanner, $_symTable)
	{
		$identifier = Generator::getUglified(Generator::SUB_RANGE_ANONIM);
		parent::__construct($scanner, $_symTable, $identifier);
	}

	public function isAnonim()
	{
		return true;
	}

	public function printInfo($offset)
	{
		Console::write("{$offset}SymSubrangeAnonimType:\n");
		$offset .= '    ';
		Console::write("{$offset}{$this->identifier}\n");
		Console::write("{$offset}{$this->from}..{$this->to}\n");
	}
}