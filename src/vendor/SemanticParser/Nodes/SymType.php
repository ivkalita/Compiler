<?php

namespace vendor\SemanticParser\Nodes;

use vendor\TokenParser\Scanner;
use vendor\Utility\Console;
use vendor\Exception\SemanticException;
use vendor\SemanticParser\Nodes\SymType;
use vendor\TokenParser\Token;

class SymType extends Symbol
{

	public function __construct()
	{
		parent::__construct();
	}

	public function isAnonim()
	{
		return false;
	}

	static public function parse($scanner, $_symTable, $identifier)
	{
		$anonim = $identifier == null;
		if ($scanner->get()->isIdentifier()) {
			//Type alias or subrange started with constant identifier
			$aliased = $_symTable->findRecursive($scanner->get()->getValue());
			if (is_a($aliased, 'vendor\SemanticParser\Nodes\SymType')) {
				$scanner->next();
				return $anonim
					? $aliased
					: new SymAliasType($identifier, $aliased);
			}
			if (is_a($aliased, 'vendor\SemanticParser\Nodes\SymConst')) {
				return $anonim
					? new SymSubrangeAnonimType($scanner, $_symTable)
					: new SymSubrangeType($scanner, $_symTable, $identifier);
			}
			SemanticException::expected($scanner, ['<TYPE DECLARATION>']);
		} else if ($scanner->get()->isKeyword('record')) {
			$scanner->next();
			return $anonim
				? new SymRecordAnonimType($scanner, $_symTable)
				: new SymRecordType($scanner, $_symTable, $identifier);
		} else if ($scanner->get()->isInteger()) {
			return $anonim
				? new SymSubrangeAnonimType($scanner, $_symTable)
				: new SymSubrangeType($scanner, $_symTable, $identifier);
		} else if ($scanner->get()->isOperator('^')) {
			return $anonim
				? new SymPointerAnonimType($scanner, $_symTable)
				: new SymPointerType($scanner, $_symTable, $identifier);
		} else if ($scanner->get()->isKeyword('array')) {
			return $anonim
				? new SymArrayAnonimType($scanner, $_symTable)
				: new SymArrayType($scanner, $_symTable, $identifier);
		} else {
			SemanticException::expected($scanner, ['<TYPE>']);
		}
	}

	static public function parseFixed($scanner, $_symTable) {
		if ($scanner->get()->isIdentifier()) {
			$aliased = $_symTable->findRecursive($scanner->get()->getValue());
			if (is_a($aliased, 'vendor\SemanticParser\Nodes\SymType')) {
				$scanner->next();
				return $aliased;
			}
			SemanticException::expected($scanner, ['<TYPE IDENTIFIER>']);
		} else if ($scanner->get()->isOperator('^')) {
			return new SymPointerAnonimType($scanner, $_symTable);
		} else if ($scanner->get()->isKeyword('array')) {
			$type = new SymArrayAnonimType($scanner, $_symTable);
			if (!$type->isDynamic()) {
				SemanticException::expected($scanner, ['<FIXED TYPE>']);
			}
			return $type;
		} else {
			SemanticException::expected($scanner, ['<TYPE>']);
		}
	}

	static public function recognizeSimpleType($const, $_symTable)
	{
		switch ($const->type) {
			case Token::UNSIGNED_NUMBER:
			case Token::UNSIGNED_INTEGER:
			case Token::SIGNED_NUMBER:
			case Token::SIGNED_INTEGER:
				return $_symTable->findRecursive('integer');
			case Token::UNSIGNED_REAL:
			case Token::UNSIGNED_REAL_E:
			case Token::SIGNED_REAL:
			case Token::SIGNED_REAL_E:
				return $_symTable->findRecursive('real');
			case Token::CHARACTER_STRING:
				return $_symTable->findRecursive('string');
			default:
				throw new \Exception('CONST TYPE NOT IMPLEMENTED YET!');
		}
	}

	static public function equal($a, $b)
	{
		return get_class($a) == get_class($b);
	}

	public function isConvertableTo($type)
	{
		return get_class($this) == get_class($type);
	}
}
