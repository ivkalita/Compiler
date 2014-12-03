<?php

namespace vendor\SyntaxParser\Nodes;

use vendor\SyntaxParser\Nodes\Node;

class FunctionDesignator extends Node
{
	private $identifier = null;
	private $params = null;

	public function __construct($identifier, $params)
	{
		$this->identifier = $identifier;
		$this->params = $params;
	}

	public function toArray()
	{
		return [
		'FunctionDesignator' => [
				'identifier' => $this->identifier->getValue(),
				'params'     => $this->params->toArray()
			]
		];
	}

	public function toIdArray(&$id)
	{
		$node = [
			"id" => $id,
			"name" => "FunctionDesignator",
			"children" => [
				[
					"id" => ++$id,
					"name" => $this->identifier->getValue()
				]
			]
		];
		if ($this->params) {
			array_push($node["children"], $this->params->toIdArray(++$id));
		}
		return $node;
	}
}