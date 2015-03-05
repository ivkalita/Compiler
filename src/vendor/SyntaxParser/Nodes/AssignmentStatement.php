<?php

namespace vendor\SyntaxParser\Nodes;

use vendor\SyntaxParser\Nodes\Node;
use vendor\TokenParser\Scanner;
use vendor\Exception\SyntaxException;

class AssignmentStatement extends Node
{
    private $leftPart = null;
    private $rightPart = null;

    public function __construct($scanner, $_symTable, $expression)
    {
        if ($expression != null) {
            $this->leftPart = $expression;
        } else {
            $this->leftPart = new Expression($scanner, $_symTable);
        }
        if (!$scanner->get()->isOperator(':=')) {
            parent::simpleException($scanner, ["<OPERATOR ':='>"]);
        }
        $scanner->next();
        $this->rightPart = new Expression($scanner, $_symTable);
        $class = get_class($this->rightPart->symType);
        if (!$class::equal($this->rightPart->symType, $this->leftPart->symType)) {
            $this->leftPart = new TypeCast($this->leftPart, $this->rightPart->symType);
        }
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