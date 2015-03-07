<?php

namespace vendor\SyntaxParser\Nodes;

class Const extends Node
{
	public $value = null;
	public $symType = null;

	public function __construct($value, $type)
	{
		$this->symType = $type;
		$this->value = $this->symType->convertValue($value);
	}
}