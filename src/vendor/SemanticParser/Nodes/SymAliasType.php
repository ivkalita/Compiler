<?php

namespace vendor\SemanticParser\Nodes;

use vendor\TokenParser\Scanner;
use vendor\Exception\SemanticException;
use vendor\Utility\Console;

class SymAliasType extends SymType
{
	public $aliased = null;

	public function __construct($identifier, $aliased)
	{
		// Console::write("SymAliasType->construct\n");
		$this->identifier = $identifier;
		$this->aliased = $aliased;
	}

	public function printInfo($offset)
	{
		Console::write("{$offset}SymAliasType:\n");
		$offset .= '    ';
		Console::write("{$offset}{$this->identifier}\n");
		$this->aliased->printInfo($offset);
	}

	public function getBase()
	{
		$type = $this->aliased;
		while (true) {
			if (!is_a($type, 'vendor\SemanticParser\Nodes\SymAliasType')) {
				return $type;
			}
			$type = $type->aliased;
		}
	}

	static public function equal($a, $b)
	{
		if (is_a($a, 'vendor\SemanticParser\Nodes\SymAliasType')) {
			$a = $a->getBase();
		}
		if (is_a($b, 'vendor\SemanticParser\Nodes\SymAliasType')) {
			$b = $b->getBase();
		}
		$class = get_class($a);
		return $class::equal($a, $b);
	}

	public function isConvertableTo($type)
	{
		return $this->getBase()->isConvertableTo($type);
	}
}