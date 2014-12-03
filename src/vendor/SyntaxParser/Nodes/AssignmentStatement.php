<?php

namespace vendor\SyntaxParser\Nodes;

use vendor\SyntaxParser\Nodes\Node;
use vendor\TokenParser\Scanner;
use vendor\Exception\SyntaxException;

class AssignmentStatement extends Node
{
    private $leftPart = null;
    private $rightPart = null;

    static public function parse($scanner)
    {
        $leftPart = Expression::parse($scanner);        
        if (!$scanner->get()->isOperator(':=')) {
            parent::simpleException($scanner, ["<OPERATOR ':='>"]);
        }
        $scanner->next();
        $rightPart = Expression::parse($scanner);
        return new AssignmentStatement($leftPart, $rightPart);
    }

    public function __construct($leftPart, $rightPart)
    {
        $this->leftPart = $leftPart;
        $this->rightPart = $rightPart;
    }

    public function toIdArray(&$id)
    {
        $lvalue = [
            "id"       => $id,
            "name"     => "LValue",
        ];
        $id++;
        $lvalue["children"] = [$this->leftPart->toIdArray($id)];
        $rvalue = [
            "id" => $id,
            "name" => "RValue"
        ];
        $id++;
        $rvalue["children"] = [$this->rightPart->toIdArray($id)];
        $node = [
            "id" => $id,
            "name" => "AssignmentStatement",
            "children" => [$lvalue, $rvalue]
        ];
        return $node;
    }
}