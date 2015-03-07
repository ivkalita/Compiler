<?php

namespace vendor\SyntaxParser\Nodes;

use vendor\SemanticParser\Nodes\SymSimpleType;
use vendor\Utility\Globals;

class BinOp extends Node
{
	private $left;
	private $right;
	private $operator;
	public $symType = null;

	public function __construct($left, $right, $operator, $_symTable)
	{
		switch ($operator->getValue()) {
			case 'div':
			case 'mod':
				$right = TypeCast::tryTypeCast($right, 'integer');
				$left = TypeCast::tryTypeCast($left, 'integer');
				$this->symType = Globals::getSimpleType('integer');
				break;
			case 'and':
			case 'or':
			case 'xor':
				$right = TypeCast::tryTypeCast($right, 'boolean');
				$left = TypeCast::tryTypeCast($left, 'boolean');
				$this->symType = Globals::getSimpleType('boolean');
				break;
			case '*':
			case '+':
			case '-':
				if (
					SymSimpleType::equal($left->symType, Globals::getSimpleType('integer')) &&
					SymSimpleType::equal($right->symType, Globals::getSimpleType('integer'))
				) {
					$this->symType = Globals::getSimpleType('integer');
					break;
				}
			case '/':
				$left = TypeCast::tryTypeCast($left, 'real');
				$right = TypeCast::tryTypeCast($right, 'real');
				$this->symType = Globals::getSimpleType('real');
				break;
			case '>':
			case '<':
			case '>=':
			case '<=':
			case '<>':
			case '=':
				if (
					SymSimpleType::equal($left->symType, Globals::getSimpleType('integer')) &&
					SymSimpleType::equal($right->symType, Globals::getSimpleType('integer'))
				) {
					$this->symType = Globals::getSimpleType('boolean');
					break;
				}
				$left = TypeCast::tryTypeCast($left, 'real');
				$right = TypeCast::tryTypeCast($right, 'real');
				$this->symType = Globals::getSimpleType('boolean');
				break;
			default:
				throw new \Exception('OPERATOR NOT IMPLEMENTED YET');

		}
		$this->left = $left;
		$this->right = $right;
		$this->operator = $operator;
	}

	public function getRight()
	{
		return $this->right;
	}

	public function getLeft()
	{
		return $this->left;
	}

	public function setRight($value)
	{
		$this->right = $value;
	}

	public function setLeft($value)
	{
		$this->left = $value;
	}

	public function getOperator()
	{
		return $this->operator;
	}

	public function setOperator($value)
	{
		$this->operator = $value;
	}

	public function toIdArray(&$id)
	{
		$node = [
			"id" => $id,
			"name" => $this->operator->getValue() . " : {$this->symType->identifier}",
			"children" => [
				$this->left->toIdArray(++$id),
				$this->right->toIdArray(++$id)
			]
		];
		return $node;
	}


}
