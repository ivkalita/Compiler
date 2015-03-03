<?php

namespace vendor\SemanticParser\Nodes;

use vendor\TokenParser\Scanner;
use vendor\SyntaxParser\Nodes\Node;
use vendor\Exception\SemanticException;
use vendor\Utility\Console;

class SymSubrangeType extends SymType
{
	public $from = 0;
	public $to = 0;

	static private function getRangeValue($scanner, $_symTable)
	{
		$token = $scanner->get();
		if (!$token->isInteger() && !$token->isIdentifier()) {
			Node::simpleException($scanner, ['<UNSIGNED NUMBER CONST>']);
		}
		if ($token->isIdentifier()) {
			$symbol = $_symTable->findRecursive($token->getValue());
			if (!is_a($symbol, 'vendor\SemanticParser\Nodes\SymConst')) {
				SemanticException::expected($scanner, ['<CONST IDENTIFIER>']);
			}
			$value = $symbol->value;
			if (!is_int($value)) {
				SemanticException::expected($scanner, ['<INT CONST IDENTIFIER>']);
			}
			return $value;
		} else {
			return $token->getValue();
		}
	}

	public function __construct($scanner, $_symTable, $identifier)
	{
		$this->identifier = $identifier;
		$this->from = self::getRangeValue($scanner, $_symTable);
		$dots = $scanner->nget();
		if (!$dots->isOperator('..')) {
			Node::simpleException($scanner, ['<OPERATOR \'..\'>']);
		}
		$scanner->next();
		$this->to = self::getRangeValue($scanner, $_symTable);
		if ($this->from > $this->to) {
			SemanticException::raw($scanner, 'Found Subrange type, but FROM > TO');
		}
		$scanner->next();
	}

	public function isAnonim()
	{
		return false;
	}

	public function printInfo($offset)
	{
		Console::write("{$offset}SymSubrangeType:\n");
		$offset .= '    ';
		Console::write("{$offset}{$this->identifier}\n");
		Console::write("{$offset}{$this->from}..{$this->to}\n");
	}
}