<?php

namespace vendor\SemanticParser\Nodes;

use vendor\TokenParser\Token;
use vendor\Utility\Console;

class SymConst extends Symbol
{
	public $value = null;
	public $type = null;

	public function __construct($identifier, $value, $type, $_symTable)
	{
		$this->identifier = $identifier;
		switch ($type) {
			case Token::UNSIGNED_INTEGER:
			case Token::SIGNED_INTEGER:
				$this->type = $_symTable->findRecursive('integer');
				if ($this->type == null) {
					throw new \Exception('BUILT-IN TYPES ARE NOT INITIALIZED');
				}
				$this->value = $value + 0;
				break;
			case Token::UNSIGNED_REAL:
			case Token::UNSIGNED_REAL_E:
			case Token::SIGNED_REAL:
			case Token::SIGNED_REAL_E:
				$this->type = $_symTable->findRecursive('real');
				if ($this->type == null) {
					throw new \Exception('BUILT-IN TYPES ARE NOT INITIALIZED');
				}
				$this->value = $value + 0.0;
				break;
			case Token::CHARACTER_STRING:
				$this->type = $_symTable->findRecursive('string');
				if ($this->type == null) {
					throw new \Exception('BUILT-IN TYPES ARE NOT INITIALIZED');
				}
				$this->value = $value;
				break;
			default:
				throw new \Exception('UNKNOWN TOKEN TYPE');
		}
	}

	public function printInfo($offset)
	{
		Console::write("{$offset}SymConst:\n");
		$offset .= '    ';
		Console::write("{$offset}{$this->identifier} = {$this->value}\n");
		$this->type->printInfo($offset);
	}
}