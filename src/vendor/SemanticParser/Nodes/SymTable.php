<?php

namespace vendor\SemanticParser\Nodes;

use vendor\Exception\SemanticException;
use vendor\Utility\Console;

class SymTable
{
	public $parent = null;
	public $symbols = [];

	public function __construct($_parentTable)
	{
		$this->parent = $_parentTable;
		if ($_parentTable == null) {
			$this->append(new SymSimpleType('integer', SymSimpleType::$INTEGER));
			$this->append(new SymSimpleType('real', SymSimpleType::$REAL));
			$this->append(new SymSimpleType('boolean', SymSimpleType::$BOOLEAN));
			$this->append(new SymSimpleType('string', SymSimpleType::$STRING));
		}
	}

	public function append($symbol)
	{
		if (array_key_exists($symbol->identifier, $this->symbols)) {
			SemanticException::redeclared($symbol->identifier);
		}
		$this->symbols[$symbol->identifier] = $symbol;
	}

	public function findRecursive($identifier)
	{
		$symTable = $this;
		while (true) {
			if (array_key_exists($identifier, $symTable->symbols)) {
				return $symTable->symbols[$identifier];
			}
			if ($symTable->parent == null) {
				return null;
			}
			$symTable = $symTable->parent;
		}
	}

	public function find($identifier)
	{
		if (array_key_exists($identifier, $this->symbols)) {
			return $this->symbols[$identifier];
		}
		return null;
	}

	public function appendForwardable($forwardable)
	{
		$pretendent = $this->findRecursive($forwardable->identifier);
		if (!is_a($forwardable, 'vendor\SemanticParser\Nodes\SymProc')) {
			SemanticException::raw(null, '{$forwardable->identifier} is not forwardable!');
		}
		$pretendent = $this->find($forwardable->identifier);
		if ($pretendent == null) {
			$this->append($forwardable);
			return;
		}
		if (is_a($pretendent, 'vendor\SemanticParser\Nodes\SymProc') && $pretendent->isDefined()) {
			SemanticException::raw(null, "<{$forwardable->identifier}> has been already defined!");
		}
		if (get_class($forwardable) != get_class($pretendent)) {
			SemanticException::raw(null, "<{$forwardable->identifier}> definition and declaration are different!");
		}
		$class = get_class($forwardable);
		if (!$class::cmpSignature($forwardable, $pretendent)) {
			SemanticException::raw(null, "<{$forwardable->identifier}> definition and declaration are different!");
		}
		$pretendent->mergeWith($forwardable);
	}

	public function count()
	{
		return count($this->symbols);
	}

	public function printInfo($offset)
	{
		Console::write("{$offset}SymTable:\n");
		$offset .= '    ';
		foreach($this->symbols as $identifier => $symbol)
		{
			$symbol->printInfo($offset);
		}
	}

}