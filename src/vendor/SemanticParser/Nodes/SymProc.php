<?php

namespace vendor\SemanticParser\Nodes;

use vendor\Utility\Console;

class SymProc extends Symbol
{
	public $symTable = null;
	public $node = null;

    public function isDefined()
    {
        return
            ($this->node->block != null) &&
            ($this->node->block->statements != null);
    }

	public function __construct($identifier, $_symTable, $node)
	{
		$this->identifier = $identifier;
		$this->symTable = $_symTable;
		$this->node = $node;
	}

	public function printInfo($offset)
	{
		Console::write("{$offset}SymProc:\n");
		$offset .= '    ';
		Console::write("{$offset}{$this->identifier}\n");
		$this->symTable->printInfo($offset);
	}

	static public function cmpSignature($a, $b)
    {
        $aArgs = [];
        $bArgs = [];
        foreach ($a->symTable->symbols as $symbol) {
            if (is_a($symbol, 'vendor\SemanticParser\Nodes\SymArg')) {
                $aArgs[$symbol->index] = $symbol->type;
            }
        }
        foreach ($b->symTable->symbols as $symbol) {
            if (is_a($symbol, 'vendor\SemanticParser\Nodes\SymArg')) {
                $bArgs[$symbol->index] = $symbol->type;
            }
        }
        if (count($aArgs) != count($bArgs)) {
            return false;
        }
        for ($i = 0; $i < count($aArgs); $i++) {
            $class = get_class($aArgs[$i]);
            if (!$class::equal($aArgs[$i], $bArgs[$i])) {
                return false;
            }
        }
        return true;
    }

    public function getArgs()
    {
        $args = [];
        foreach($this->symTable->symbols as $symbol) {
            if (is_a($symbol, 'vendor\SemanticParser\Nodes\SymArg')) {
                $args[$symbol->index] = $symbol;
            }
        }
        return $args;
    }

    public function mergeWith($src)
    {
        $this->symTable = $src->symTable;
    }
}