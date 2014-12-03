<?php

namespace vendor\SyntaxParser\Nodes;

use vendor\SyntaxParser\Nodes\Node;
use vendor\TokenParser\Scanner;
use vendor\Exception\SyntaxException;

class IdentifiedVariable extends Node
{
    private $pointer = null;

    static public function parse($scanner, $pointer)
    {
        return new IdentifiedVariable($pointer);
    }

    static public function firstTokens()
    {
        return ["<OPERATOR '^'>"];
    }

    public function __construct($pointer)
    {
        $this->pointer = $pointer;
    }

    public function toArray()
    {
        return [
            "IdentifiedVariable" => [
                "PointerVariable" => $this->pointer->toArray()
            ]
        ];
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
