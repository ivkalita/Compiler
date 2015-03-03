<?php

namespace vendor\SyntaxParser\Nodes;

use vendor\SyntaxParser\Nodes\Node;
use vendor\TokenParser\Scanner;
use vendor\Exception\SyntaxException;
use vendor\SemanticParser\Nodes\SymTable;

class ProcDeclaration extends Node
{
    public $symbol;
    public $identifier;

    public function __construct($scanner, $_symTable)
    {
        $args = new SymTable($_symTable);
        if (!$scanner->get()->isIdentifier()) {
            parent::simpleException($scanner, ['<IDENTIFIER>']);
        }
        $this->identifier = $scanner->get()->getValue();
        $scanner->next();
        if (!$scanner->get()->isOperator('(')) {
            parent::simpleException($scanner, ['<OPERATOR \'(\'>']);
        }
        while (!$scanner->get()->isOperator(')')) {
            $identifiers = [];
            while ($scanner->get()->isIdentifier()) {
                $identifiers[] = $scanner->get()->getValue();
                $scanner->next();
                if (!$scanner->get()->isOperator(',')) {
                    break;
                }
                $scanner->next();
            }
            if (!$scanner->get()->isOperator(':')) {
                parent::simpleException($scanner, ['<OPERATOR \':\'>']);
            }
            $scanner->next();
            $type = SymType::parseExisting($scanner, $args);
            foreach($identifiers as $identifier) {
                $args->append(new SymVar($identifier, $type));
            }
            parent::semicolonPass($scanner);
        }
        $this->symbol = new SymProc($this->identifier->getValue(), $args);
        $_symTable->append($this->symbol);
        $scanner->next();
    }
}
