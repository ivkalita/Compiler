<?php

namespace vendor\SemanticParser\Nodes;
use vendor\TokenParser\Scanner;
use vendor\Exception\SemanticException;
use vendor\Utility\Console;

class SymSimpleType extends SymType
{
	public $type = null;
	static public $INTEGER = 'SYS??_INTEGER';
	static public $BOOLEAN = 'SYS??_BOOLEAN';
	static public $REAL = 'SYS??_REAL';
	static public $STRING = 'SYS??_STRING';
	private $CMP_TABLE;

	public function __construct($identifier, $type)
	{
		parent::__construct();
		$this->identifier = $identifier;
		$this->type = $type;

		$this->CMP_TABLE = [
			self::$INTEGER => [
				self::$INTEGER => true,
				self::$BOOLEAN => true,
				self::$REAL    => true,
				self::$STRING  => true
			],
			self::$REAL => [
				self::$INTEGER => false,
				self::$REAL => true,
				self::$BOOLEAN => true,
				self::$STRING => true
			],
			self::$BOOLEAN => [
				self::$INTEGER => true,
				self::$REAL => true,
				self::$BOOLEAN => true,
				self::$STRING => true
			],
			self::$STRING => [
				self::$INTEGER => false,
				self::$REAL => false,
				self::$BOOLEAN => false,
				self::$STRING => true
			]
		];
	}

	public function isAnonim()
	{
		return false;
	}

	public function printInfo($offset)
	{
		Console::write("{$offset}SymSimpleType:\n");
		$offset .= '    ';
		Console::write("{$offset}{$this->identifier}\n");
		Console::write("{$offset}{$this->type}\n");
	}

	static public function equal($a, $b)
	{
		if (is_a($a, 'vendor\SemanticParser\Nodes\SymAliasType')) {
			$a = $a->getBase();
		}
		if (is_a($b, 'vendor\SemanticParser\Nodes\SymAliasType')) {
			$b = $b->getBase();
		}
		$bothAreSimple = get_class($a) == 'vendor\SemanticParser\Nodes\SymSimpleType' && get_class($b) == 'vendor\SemanticParser\Nodes\SymSimpleType';
		return
			$bothAreSimple &&
			($a->type == $b->type);
	}

	public function isConvertableTo($type)
	{
		if (is_a($type, 'vendor\SemanticParser\Nodes\SymAliasType')) {
			$type = $type->getBase();
		}
		// echo "{$this->type}\n{$type->type}\n";
		// var_dump($this->CMP_TABLE[$this->type][$type->type]);
		switch (get_class($type)) {
			case 'vendor\SemanticParser\Nodes\SymSimpleType':
				return $this->CMP_TABLE[$this->type][$type->type];
			case 'vendor\SemanticParser\Nodes\SymSubrangeType':
			case 'vendor\SemanticParser\Nodes\SymSubrangeAnonimType':
				return $this->CMP_TABLE[$this->type][self::$INTEGER];
			default:
				return false;
		}
	}
}