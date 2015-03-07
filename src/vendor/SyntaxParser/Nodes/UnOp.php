<?php

namespace vendor\SyntaxParser\Nodes;

use vendor\SemanticParser\Nodes\SymSimpleType;
use vendor\Exception\SemanticException;
use vendor\Utility\Globals;

class UnOp extends Node
{
	private $operand;
	private $operator;
	public $symType = null;

	public function __construct($operand, $operator, $_symTable)
	{
		if (
			!SymSimpleType::equal($operand->symType, Globals::getSimpleType('integer')) &&
			!SymSimpleType::equal($operand->symType, Globals::getSimpleType('real'))
		) {
			SemanticException::invalidTypeCast($operand->symType, Globals::getSimpleType('real'));
		}
		$this->symType = $operand->symType;
		$this->operand = $operand;
		$this->operator = $operator;
	}

	public function toIdArray(&$id)
	{
		$node = [
			"id" => $id,
			"name" => $this->operator->getValue() . " : {$this->symType->identifier}",
			"children" => [$this->operand->toIdArray(++$id)]
		];
		return $node;
	}


}
