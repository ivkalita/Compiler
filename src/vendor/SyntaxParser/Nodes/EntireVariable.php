<?php

namespace vendor\SyntaxParser\Nodes;

use vendor\SyntaxParser\Nodes\Node;
use vendor\TokenParser\Scanner;
use vendor\Exception\SyntaxException;
use vendor\Exception\SemanticException;

class EntireVariable extends Node
{
    public $identifier = null;
    public $symType = null;
    public $var = null;

    public function __construct($scanner, $_symTable, $identifier)
    {
        $symbol = $_symTable->findRecursive($identifier->getValue());
        if ($symbol == null || !is_a($symbol, 'vendor\SemanticParser\Nodes\SymVar')) {
            SemanticException::undeclared($scanner, $identifier->getValue());
        }
        $this->symbol = $symbol;
        $this->symType = $symbol->type;
        $this->identifier = $identifier;
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
                ],
                [
                    "id" => ++$id,
                    "name" => "type={$this->symType->identifier}"
                ]
            ]
        ];
        $id++;
        return $node;
    }
}
