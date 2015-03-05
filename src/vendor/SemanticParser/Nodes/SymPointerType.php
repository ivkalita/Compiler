<?php

namespace vendor\SemanticParser\Nodes;

use vendor\TokenParser\Scanner;
use vendor\SyntaxParser\Nodes\Node;
use vendor\Exception\SemanticException;
use vendor\Utility\Console;

class SymPointerType extends SymType
{
	public $type = null;

	public function __construct($scanner, $_symTable, $identifier)
	{
		$this->identifier = $identifier;
		$type = $scanner->nget();
		if (!$type->isIdentifier()) {
			Node::simpleException($scanner, ['<TYPE IDENTIFIER>']);
		}
		$type = $_symTable->findRecursive($type->getValue());
		if ($type == null || !is_a($type, 'vendor\SemanticParser\Nodes\SymType')) {
			SemanticException::expected($scanner, ['<TYPE IDENTIFIER>']);
		}
		$this->type = $type;
		$scanner->next();
	}

	public function isAnonim()
	{
		return false;
	}

	public function printInfo($offset)
	{
		Console::write("{$offset}SymPointerType:\n");
		$offset .= '    ';
		Console::write("{$offset}{$this->identifier}\n");
		$this->type->printInfo($offset);
	}

	public function isConvertableTo($type)
	{
		if (is_a($type, 'vendor\SemanticParser\Nodes\SymAliasType')) {
			$type = $type->getBase();
		}
		return $this->identifier == $type->identifier;
	}
}