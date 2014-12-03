<?php

namespace vendor\SyntaxParser\Nodes;

use vendor\SyntaxParser\Nodes\Node;
use vendor\TokenParser\Scanner;
use vendor\Exception\SyntaxException;

class ConstDef extends Node
{
    private $identifier = null;
    private $constant = null;

    static public function parse($scanner)
    {
        $identifier = $scanner->get();
        if (!$identifier->isIdentifier()) {
            parent::simpleException($scanner, ['<IDENTIFIER>']);
        }
        $scanner->next();
        if (!$scanner->get()->isOperator('=')) {
            parent::simpleException($scanner, ["<OPERATOR '='>"]);
        }
        $constant = $scanner->nget();
        if (!$constant->isConst()) {
            parent::simpleException($scanner, ['<CONSTANT']);
        }
        $scanner->next();
        return new ConstDef($identifier, $constant);
    }

    public function __construct($identifier, $constant)
    {
        $this->identifier = $identifier;
        $this->constant = $constant;
    }

    public function toIdArray(&$id)
    {
        $node = [
            "id"       => $id,
            "name"     => "ConstDef",
            "children" => [
                [
                    "id"   => ++$id,
                    "name" => "identifier = " . $this->identifier->getValue()
                ],
                [
                    "id"   => ++$id,
                    "name" => "constant = " . $this->constant->getValue()
                ]
            ]
        ];
        $id++;
        return $node;
    }
}
