<?php

namespace vendor\SyntaxParser\Nodes;

use vendor\SemanticParser\Nodes\SymSimpleType;

class BinOp extends Node
{
    private $left;
    private $right;
    private $operator;
    public $symType = null;

    public function __construct($left, $right, $operator, $_symTable)
    {
        //TODO: Clear code
        $integerType = $_symTable->findRecursive('integer');
        $realType = $_symTable->findRecursive('real');
        $booleanType = $_symTable->findRecursive('boolean');
        $leftType = $left->symType;
        if (get_class($leftType) == 'vendor\SemanticParser\Nodes\SymAliasType') {
            $leftType = $leftType->getBase();
        }
        $rightType = $right->symType;
        if (get_class($leftType) == 'vendor\SemanticParser\Nodes\SymAliasType') {
            $rightType = $rightType->getBase();
        }
        switch ($operator->getValue()) {
            case 'div':
            case 'mod':
                if (!SymSimpleType::equal($rightType, $integerType)) {
                    $right = new TypeCast($right, $integerType);
                }
                if (!SymSimpleType::equal($leftType, $integerType)) {
                    $left = new TypeCast($left, $integerType);
                }
                $this->symType = $integerType;
                break;
            case '*':
            case '+':
            case '-':
                if (SymSimpleType::equal($leftType, $integerType) && SymSimpleType::equal($rightType, $integerType)) {
                    $this->symType = $integerType;
                    break;
                }
            case '/':
                $left = SymSimpleType::equal($leftType, $realType) ? $left : new TypeCast($left, $realType);
                $right = SymSimpleType::equal($rightType, $realType) ? $right: new TypeCast($right, $realType);
                $this->symType = $realType;
                break;
            case '>':
            case '<':
            case '>=':
            case '<=':
            case '<>':
            case '=':
                if (SymSimpleType::equal($leftType, $integerType) && SymSimpleType::equal($rightType, $integerType)) {
                    $this->symType = $booleanType;
                    break;
                }
                $left = SymSimpleType::equal($leftType, $realType) ? $left : new TypeCast($left, $realType);
                $right = SymSimpleType::equal($rightType, $realType) ? $right: new TypeCast($right, $realType);
                $this->symType = $booleanType;
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

    public function toArray()
    {
    	return [
    		$this->operator->getValue() => [
    			'left' => $this->left->toArray(),
    			'right' => $this->right->toArray()
    		]
    	];
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
