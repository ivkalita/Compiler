<?php

namespace vendor\SyntaxParser\Nodes;

use vendor\SyntaxParser\Nodes\Node;
use vendor\TokenParser\Scanner;
use vendor\Exception\SyntaxException;

class TypeDef extends Node
{
    private $identifier = null;
    private $denoter = null;

    static public function parse($scanner)
    {
        if (!$scanner->get()->isIdentifier()) {
            parent::simpleException($scanner, ['<IDENTIFIER>']);
        }
        $identifier = $scanner->get();
        parent::eofLessNext($scanner, ["<OPERATOR '='>"]);
        if (!$scanner->get()->isOperator('=')) {
            parent::simpleException($scanner, ["<OPERATOR '='"]);
        }
        parent::eofLessNext($scanner, ["<TYPE-DENOTER>"]);
        $denoter = TypeDenoter::parse($scanner);
        $scanner->next();
        return new TypeDef($identifier, $denoter);
    }

    static public function firstTokens()
    {
        return ['<IDENTIFIER>'];
    }

    public function __construct($identifier, $denoter)
    {
        $this->identifier = $identifier;
        $this->denoter = $denoter;
    }
}
