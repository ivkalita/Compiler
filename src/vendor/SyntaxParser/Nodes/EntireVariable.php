<?php

namespace vendor\SyntaxParser\Nodes;

use vendor\SyntaxParser\Nodes\Node;
use vendor\TokenParser\Scanner;
use vendor\Exception\SyntaxException;

class EntireVariable extends Node
{
    private $identifier = null;

    static public function firstTokens()
    {
        return ['<IDENTIFIER>'];
    }

    public function __construct($identifier)
    {
        $this->identifier = $identifier;
    }

    public function toArray()
    {
        return [
            "EntireVariable" => [
                "identifier" => $this->identifier->getValue()
            ]
        ];
    }

    public function toIdArray(&$id)
    {
        $node = [
            "id" => $id,
            "name" => "EntireVariable",
            "children" => [
                [
                    "id" => ++$id,
                    "name" => "identifier=" . $this->identifier->getValue()
                ]
            ]
        ];
        $id++;
        return $node;
    }
}
