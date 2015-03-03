<?php

namespace vendor\SemanticParser\Nodes;

use vendor\TokenParser\Scanner;
use vendor\SyntaxParser\Nodes\Node;
use vendor\Exception\SemanticException;
use vendor\Utility\Console;

class SymRecordType extends SymType
{
	public $symTable = null;

	public function isAnonim()
	{
		return false;
	}

	public function __construct($scanner, $_symTable, $identifier)
	{
		$this->symTable = new SymTable($_symTable);
		$this->identifier = $identifier;
		while ($scanner->get()->isIdentifier()) {
			//variable declaration parse
			$identifier = $scanner->get();
			if (!$scanner->nget()->isOperator(':')) {
				Node::simpleException($scanner, ['<OPERATOR \':\'>']);
			}
			$scanner->next();
			$type = SymType::parse($scanner, $_symTable, null);
			if ($type->isAnonim()) {
				$this->symTable->append($type);
			}
			$this->symTable->append(new SymVar($identifier->getValue(), $type));
			// $scanner->next();
			Node::semicolonPass($scanner);
		}
		if (!$scanner->get()->isKeyword('end')) {
			Node::simpleException($scanner, ['<KEYWORD \'end\'']);
		}
		$scanner->next();
		if ($this->symTable->count() == 0) {
			SemanticException::expected($scanner, ['<VAR DECLARATIONS>']);
		}
	}

	public function printInfo($offset)
	{
		Console::write("{$offset}SymRecordType:\n");
		$offset .= '    ';
		Console::write("{$offset}{$this->identifier}\n");
		$this->symTable->printInfo($offset);
	}
}