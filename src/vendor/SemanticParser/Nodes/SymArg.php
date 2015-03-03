<?php

namespace vendor\SemanticParser\Nodes;

use vendor\TokenParser\Scanner;
use vendor\Exception\SemanticException;
use vendor\Utility\Console;

class SymArg extends SymVar
{
	public $index;

	public function __construct($identifier, $type, $idx)
	{
		parent::__construct($identifier, $type);
		$this->index = $idx;
	}

	public function printInfo($offset)
	{
		Console::write("{$offset}SymArg:\n");
		$offset .= '    ';
		Console::write("{$offset}{$this->identifier}\n");
		Console::write("{$offset}index = {$this->index}\n");
		$this->type->printInfo($offset);
	}
}