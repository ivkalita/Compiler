<?php

namespace vendor\SyntaxParser\Nodes;

use vendor\SyntaxParser\Nodes\Node;
use vendor\TokenParser\Token;
use vendor\SemanticParser\Nodes\SymType;

class Factor extends Node
{
	private $node = null;
	private $keyword = null;
	public $symType = null;

	public function __construct($scanner, $_symTable)
	{
		if ($scanner->get()->isUnSignedConst()) {
			$this->node = $scanner->get();
			$this->symType = SymType::recognizeSimpleType($this->node, $_symTable);
			$scanner->next();
			return;
		}
		if ($scanner->get()->isLBracket()) {
			$scanner->next();
			$this->node = new Expression($scanner, $_symTable);
			if (!$scanner->get()->isRBracket()) {
				parent::simpleException($scanner, ["<OPERATOR ')'>"]);
			}
			$this->symType = $this->node->symType;
			$scanner->next();
			return;
		}
		if ($scanner->get()->isKeyword('not')) {
			$this->keyword = $scanner->get();
			$scanner->next();
			$this->node = new Factor($scanner, $_symTable);
			$this->symType = $_symTable->findRecursive('boolean');
			$this->node = new TypeCast($this->node, $this->symType);
			$scanner->next();
			return;
		}
		if ($scanner->get()->isIdentifier()) {
			$identifier = $scanner->get();
			$scanner->next();
			if ($scanner->get()->isLBracket()) {
				$actualParamList = new ActualParamList($scanner, $_symTable);
				$this->node = new FunctionDesignator($identifier, $actualParamList, $_symTable);
			} else {
				$this->node = VariableAccess::parse($scanner, $_symTable, $identifier);
			}
			$this->symType = $this->node->symType;
			return;
		}
		parent::simpleException($scanner, ['<FACTOR>']);
	}

	public function toIdArray(&$id)
	{
		if ($this->keyword) {
			return [
				"id" => $id,
				"name" => "Factor",
				"children" => [
					[
						"id" => ++$id,
						"name" => "not"
					],
					$this->node->toIdArray(++$id),
					[
						"id" => $id++,
						"name" => "type={$this->symType->identifier}"
					]
				]
			];
		} else {
			return (
				$this->node instanceof Token ?
					[
						"id" => $id++,
						"name" => $this->node->getValue() . ": {$this->symType->identifier}"
					] :
					$this->node->toIdArray($id)
			);
		}
	}
}