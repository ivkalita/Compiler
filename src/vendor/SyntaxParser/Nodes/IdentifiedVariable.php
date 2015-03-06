<?php

namespace vendor\SyntaxParser\Nodes;

use vendor\SyntaxParser\Nodes\Node;
use vendor\TokenParser\Scanner;
use vendor\Exception\SyntaxException;

class IdentifiedVariable extends Node
{
    private $pointer = null;
    public $symType = null;

    public function __construct($scanner, $_symTable, $pointer)
    {
        $pointerType = $pointer->variable->symType;
        if (!is_a($pointerType, 'vendor\SemanticParser\Nodes\SymPointerType')) {
            SemanticParser::varAccessTypeMismatch($scanner, $pointerType, 'pointer');
        }
        $this->symType = $pointerType->type;
        $this->pointer = $pointer;
    }

    public function toIdArray(&$id)
    {
        return [
            "id" => $id,
            "name" => "PointerVariable",
            "children" => [$this->pointer->toIdArray(++$id)]
        ];
    }
}
