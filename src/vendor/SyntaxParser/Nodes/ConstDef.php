<?php

namespace vendor\SyntaxParser\Nodes;

use vendor\SyntaxParser\Nodes\Node;
use vendor\TokenParser\Scanner;
use vendor\Exception\SyntaxException;
use vendor\SemanticParser\Nodes\SymConst;

class ConstDef extends Node
{
    private $identifier = null;
    private $constant = null;
    private $symbol = null;

    public function __construct($scanner, $_symTable)
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
        $this->symbol = new SymConst($identifier->getValue(), $constant->getValue(), $constant->type, $_symTable);
        $_symTable->append($this->symbol);
        $scanner->next();
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
