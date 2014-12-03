<?php

namespace vendor\SyntaxParser\Nodes;

use vendor\SyntaxParser\Nodes\Node;

class ActualParamList extends Node
{
	private $params = null;

	static public function parse($scanner)
	{
		// echo "ActualParamList::parse()\n";
		$params = [];
		if (!$scanner->get()->isLBracket()) {
			parent::simpleException($scanner, ["<OPERATOR '('>"]);
		}
		$scanner->next();
		if ($scanner->get()->isRBracket()) {
			$scanner->next();
			return new ActualParamList(null);
		}
		$params[] = SimpleExpression::parse($scanner);
		while (!$scanner->get()->isRBracket()) {
			if (!$scanner->get()->isOperator(',')) {
				parent::simpleException($scanner, ["<OPERATOR ','>"]);
			}
			$scanner->next();
			$params[] = SimpleExpression::parse($scanner);
		}
		$scanner->next();
		return new ActualParamList($params);
	}

	public function __construct($params)
	{
		$this->params = $params;
	}

	static public function firstTokens()
	{
		return ["<OPERATOR '('>"];
	}

	public function toArray()
	{
		$params = null;
		$i = 0;
		if ($this->params) {
			$params = [];
			foreach($this->params as &$param) {
				$params["param_$i"] = $param->toArray();
				$i++;
			}
		}
		return [
			'ActualParamList' => $params
		];
	}

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