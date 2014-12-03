<?php
/**
 * Created by IntelliJ IDEA.
 * User: kaduev
 * Date: 08.10.14
 * Time: 10:10
 */

namespace vendor\SyntaxParser\Nodes;

class BinOp extends Node
{
    private $left;
    private $right;
    private $operator;

    public function __construct($left, $right, $operator)
    {
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
            "name" => $this->operator->getValue(),
            "children" => [
                $this->left->toIdArray(++$id),
                $this->right->toIdArray(++$id)
            ]
        ];
        return $node;
    }


}
