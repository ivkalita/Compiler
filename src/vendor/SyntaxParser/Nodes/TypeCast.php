<?php

namespace vendor\SyntaxParser\Nodes;

use vendor\SyntaxParser\Nodes\Node;
use vendor\TokenParser\Token;
use vendor\SemanticParser\Nodes\SymType;
use vendor\Exception\SemanticException;

class TypeCast extends Node
{
	public $node = null;
	public $symType = null;

	public function __construct($node, $symType)
	{
		if (!$node->symType->isConvertableTo($symType)) {
			SemanticException::invalidTypeCast($node->symType, $symType);
		}
		$this->node = $node;
		$this->symType = $symType;
	}

	public function toIdArray(&$id)
	{
		return [
			"id" => $id++,
			"name" => "convertedTo={$this->symType->identifier}",
			"children" => [$this->node->toIdArray($id)]
		];
	}
}