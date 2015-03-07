<?php

namespace vendor\SemanticParser\Nodes;

use vendor\TokenParser\Token;
use vendor\Utility\Console;
use vendor\Utility\Globals;

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
				$this->type = Globals::getSimpleType('integer');
				$this->value = $value + 0;
				break;
			case Token::UNSIGNED_REAL:
			case Token::UNSIGNED_REAL_E:
			case Token::SIGNED_REAL:
			case Token::SIGNED_REAL_E:
				$this->type = Globals::getSimpleType('real');
				$this->value = $value + 0.0;
				break;
			case Token::CHARACTER_STRING:
				$this->type = Globals::getSimpleType('string');
				$this->value = $value;
				break;
			case Token::BOOLEAN_CONST:
				$this->type = Globals::getSimpleType('boolean');
				$this->value = $value == 'true';
				break;
			default:
				throw new \Exception('UNKNOWN TOKEN TYPE');
		}
		if ($this->type == null) {
			throw new \Exception('BUILT-IN TYPES ARE NOT INITIALIZED');
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