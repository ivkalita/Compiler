<?php

namespace vendor\SyntaxParser\Nodes;

use vendor\SyntaxParser\Nodes\Node;
use vendor\TokenParser\Token;
class Factor extends Node
{
	private $node = null;
	private $keyword = null;

	static public function parse($scanner)
	{
		if ($scanner->get()->isUnSignedConst()) {
			$const = $scanner->get();
			$scanner->next();
			return new Factor($const);
		}
		if ($scanner->get()->isLBracket()) {
			$scanner->next();
			$expr = Expression::parse($scanner);
			if (!$scanner->get()->isRBracket()) {
				parent::simpleException($scanner, ["<OPERATOR ')'>"]);
			}
			$scanner->next();
			return new Factor($expr);
		}
		if ($scanner->get()->isKeyword('not')) {
			$keyword = $scanner->get();
			$scanner->next();
			$factor = Factor::parse($scanner);
			$scanner->next();
			return new Factor($factor, $keyword);
		}
		if ($scanner->get()->isIdentifier()) {
			$identifier = $scanner->get();
			parent::eofLessNext($scanner, ['<ЧТО НИБУДЬ, НО НЕ ЕОФ>']);
			if ($scanner->get()->isLBracket()) {
				$actualParamList = ActualParamList::parse($scanner);
				return new Factor(new FunctionDesignator($identifier, $actualParamList));
			} else {
				return new Factor(VariableAccess::parse($scanner, $identifier));
			}
		}
		parent::simpleException($scanner, self::firstTokens());
	}

	static public function firstTokens()
	{
		return [
			'<IDENTIFIER>',
			'<UNSIGNED-CONST>',
			"<KEYWORD 'not'>",
			"<OPERATOR '('>"
		];
	}

	public function __construct($node, $keyword = null)
	{
		$this->node = $node;
		$this->keyword = $keyword;
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
					$this->node->toIdArray(++$id)
				]
			];
		} else {
			return (
				$this->node instanceof Token ?
					[
						"id" => $id++,
						"name" => $this->node->getValue()
					] :
					$this->node->toIdArray($id)
			);
		}
	}
}