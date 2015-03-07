<?php

namespace vendor\SyntaxParser\Nodes;

use vendor\SyntaxParser\Nodes\Node;
use vendor\TokenParser\Scanner;
use vendor\Exception\SyntaxException;
use vendor\SemanticParser\Nodes\SymType;

class TypeDef extends Node
{
    private $identifier = null;
    public $symbol = null;

    public function __construct($scanner, $_symTable)
    {
        if (!$scanner->get()->isIdentifier()) {
            parent::simpleException($scanner, ['<IDENTIFIER>']);
        }
        $identifier = $scanner->get();
        $scanner->next();
        parent::requireOperator($scanner, '=');
        $scanner->next();
        $this->symbol = SymType::parse($scanner, $_symTable, $identifier->getValue());
        $_symTable->append($this->symbol);
    }
}
