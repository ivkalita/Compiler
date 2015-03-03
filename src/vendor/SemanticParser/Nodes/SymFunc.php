<?php

namespace vendor\SemanticParser\Nodes;

use vendor\Utility\Console;

class SymFunc extends SymProc
{
	public $returnType = null;

	public function __construct($identifier, $_symTable, $node, $returnType)
	{
        parent::__construct($identifier, $_symTable, $node);
        $this->returnType = $returnType;
	}

	public function printInfo($offset)
	{
		Console::write("{$offset}SymFunc:\n");
		$offset .= '    ';
		Console::write("{$offset}{$this->identifier}\n");
		$this->symTable->printInfo($offset);
        Console::write("{$offset}return value:\n");
        $this->returnType->printInfo($offset);
	}

	static public function cmpSignature($a, $b)
    {
        return
            parent::cmpSignature($a, $b) &&
            (get_class($a->returnType) == get_class($b->returnType));
    }
}