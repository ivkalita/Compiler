<?php

namespace vendor\SyntaxParser\Nodes;

use vendor\SyntaxParser\Nodes\Node;
use vendor\TokenParser\Scanner;
use vendor\Exception\SyntaxException;
use vendor\SemanticParser\Nodes\SymType;
use vendor\SemanticParser\Nodes\SymVar;


class VarDec extends Node
{
    private $symbol = null;
    private $identifier = null;

    public function __construct($scanner, $_symTable)
    {
        if (!$scanner->get()->isIdentifier()) {
            parent::simpleException($scanner, ['<IDENTIFIER>']);
        }
        $this->identifier = $scanner->get();
        $scanner->next();
        parent::requireOperator($scanner, ':');
        $scanner->next();
        $type = SymType::parse($scanner, $_symTable, null);
        $this->symbol = new SymVar($this->identifier->getValue(), $type);
        $_symTable->append($this->symbol);
    }
}
