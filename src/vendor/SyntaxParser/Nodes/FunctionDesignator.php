<?php

namespace vendor\SyntaxParser\Nodes;

use vendor\SyntaxParser\Nodes\Node;
use vendor\SemanticParser\Nodes;
use vendor\Exception\SemanticException;

class FunctionDesignator extends Node
{
	private $identifier = null;
	private $paramList = null;
	public $symType = null;
	public $symbol = null;

	public function __construct($identifier, $paramList, $_symTable, $isFunc = true)
	{
		$symFunc = $_symTable->findRecursive($identifier->getValue());
		if ($isFunc) {
			if ($symFunc == null || !is_a($symFunc, 'vendor\SemanticParser\Nodes\SymFunc')) {
				//TODO: Throw exceptions like identifier <$identifier> is not a function
				SemanticException::undeclared(null, $identifier->getValue());
			}
		}
		$symFuncArgs = $symFunc->getArgs();
		if (count($paramList->params) != count($symFuncArgs)) {
			SemanticException::invalidArgCount($identifier->getValue());
		}
		for ($i = 0; $i < count($paramList->params); $i++) {
			$class = get_class($symFuncArgs[$i]->type);
			if (!$class::equal($symFuncArgs[$i]->type, $paramList->params[$i]->symType)) {
				$paramList->params[$i] = new TypeCast($paramList->params[$i], $symFuncArgs[$i]->type);
			}
		}
		if ($isFunc) {
			$this->symType = $symFunc->returnType;
		}
		$this->symbol = $symFunc;
		$this->identifier = $identifier;
		$this->paramList = $paramList;
	}

	public function toIdArray(&$id)
	{
		$node = [
			"id" => $id,
			"name" => ($this->symType == null ? "ProcedureDesignator" : "FunctionDesignator"),
			"children" => [
				[
					"id" => ++$id,
					"name" => $this->identifier->getValue()
				]
			]
		];
		if ($this->paramList) {
			array_push($node["children"], $this->paramList->toIdArray(++$id));
		}
		if ($this->symType != null) {
			array_push(
				$node["children"],
				[
					"id" => $id++,
					"name" => "returnType={$this->symType->identifier}"
				]
			);
		}
		return $node;
	}
}