<?php
/**
 * Created by IntelliJ IDEA.
 * User: kaduev
 * Date: 08.10.14
 * Time: 10:10
 */

namespace vendor\SyntaxParser\Nodes;

class UnOp extends Node
{
    private $operand;
    private $operator;

    public function __construct($operand, $operator)
    {
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
            "name" => $this->operator->getValue(),
            "children" => [$this->operand->toIdArray(++$id)]
        ];
        return $node;
    }


}
