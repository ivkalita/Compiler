<?php

namespace vendor\SyntaxParser\Nodes;

use vendor\SyntaxParser\Nodes\Node;

class ActualParamList extends Node
{
	public $params = null;

	public function __construct($scanner, $_symTable)
	{
		$this->params = [];
		if (!$scanner->get()->isLBracket()) {
			parent::simpleException($scanner, ["<OPERATOR '('>"]);
		}
		$scanner->next();
		if ($scanner->get()->isRBracket()) {
			$scanner->next();
			$this->params = [];
			return;
		}
		$this->params[] = new SimpleExpression($scanner, $_symTable);
		while (!$scanner->get()->isRBracket()) {
			if (!$scanner->get()->isOperator(',')) {
				parent::simpleException($scanner, ["<OPERATOR ','>"]);
			}
			$scanner->next();
			$this->params[] = new SimpleExpression($scanner, $_symTable);
		}
		$scanner->next();
	}

	//TODO: DC
	// public function toArray()
	// {
	// 	$params = null;
	// 	$i = 0;
	// 	if ($this->params) {
	// 		$params = [];
	// 		foreach($this->params as &$param) {
	// 			$params["param_$i"] = $param->toArray();
	// 			$i++;
	// 		}
	// 	}
	// 	return [
	// 		'ActualParamList' => $params
	// 	];
	// }

	public function toIdArray(&$id)
	{
		$params = null;
		if ($this->params) {
			$params = [];
			foreach($this->params as &$param) {
				$params[] = $param->toIdArray($id);
			}
		} else {
			$params = [
				"id" => $id,
				"name" => "no params"
			];
			$id++;
		}
		return [
			"id" => $id++,
			"name" => "ActualParamList",
			"children" => $params
		];
	}
}