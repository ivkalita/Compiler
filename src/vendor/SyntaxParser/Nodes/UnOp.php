<?php

namespace vendor\SyntaxParser\Nodes;

use vendor\SemanticParser\Nodes\SymSimpleType;
use vendor\Exception\SemanticException;

class UnOp extends Node
{
    private $operand;
    private $operator;
    public $symType = null;

    public function __construct($operand, $operator, $_symTable)
    {
        $integerType = $_symTable->findRecursive('integer');
        $realType = $_symTable->findRecursive('real');
        $operandType = $operand->symType;
        if (get_class($operandType) == 'vendor\SemanticParser\Nodes\SymAliasType') {
            $operandType = $operandType->getBase();
        }
        if (!SymSimpleType::equal($operandType, $integerType) && !SymSimpleType::equal($operandType, $realType)) {
            SemanticException::invalidTypeCast($operandType, $realType);
        }
        $this->symType = $operandType;
    	$this->operand = $operand;
    	$this->operator = $operator;
    }

    public function toArray()
    {
    	return [
    		$this->operator->getValue() => [
                'operand' => $this->operator->toArray()
            ]
    	];
    }

    public function toIdArray(&$id)
    {
        $node = [
            "id" => $id,
            "name" => $this->operator->getValue() . " : {$this->symType->identifier}",
            "children" => [$this->operand->toIdArray(++$id)]
        ];
        return $node;
    }


}
