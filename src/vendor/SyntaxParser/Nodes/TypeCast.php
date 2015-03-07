<?php

namespace vendor\SyntaxParser\Nodes;

use vendor\SyntaxParser\Nodes\Node;
use vendor\TokenParser\Token;
use vendor\SemanticParser\Nodes\SymType;
use vendor\Exception\SemanticException;
use vendor\Utility\Globals;

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

	static public function tryTypeCast($obj, $toType, $simpleType = true)
	{
		$comparer = $simpleType
			? "vendor\SemanticParser\Nodes\SymSimpleType"
			: get_class($obj->symType);
		if ($simpleType) {
			$toType = Globals::getSimpleType($toType);
		}
		if (!$comparer::equal($obj->symType, $toType)) {
			return new TypeCast($obj, $toType);
		}
		return $obj;

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