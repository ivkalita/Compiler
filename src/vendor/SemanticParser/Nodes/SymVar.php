<?php

namespace vendor\SemanticParser\Nodes;
use vendor\TokenParser\Scanner;
use vendor\Exception\SemanticException;
use vendor\Utility\Console;

class SymVar extends Symbol
{
	public $type;

	public function __construct($identifier, $type)
	{
		$this->identifier = $identifier;
		$this->type = $type;
	}

	public function printInfo($offset)
	{
		Console::write("{$offset}SymVar:\n");
		$offset .= '    ';
		Console::write("{$offset}{$this->identifier}\n");
		$this->type->printInfo($offset);
	}
}