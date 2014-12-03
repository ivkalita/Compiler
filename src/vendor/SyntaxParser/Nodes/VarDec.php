<?php

namespace vendor\SyntaxParser\Nodes;

use vendor\SyntaxParser\Nodes\Node;
use vendor\TokenParser\Scanner;
use vendor\Exception\SyntaxException;

class VarDec extends Node
{
    private $identifier = null;
    private $denoter = null;

    static public function parse($scanner)
    {
        if (!$scanner->get()->isIdentifier()) {
            throw new SyntaxException(
                $scanner->get()->point,
                $scanner->get()->text,
                ['<IDENTIFIER>'],
                "<" . $scanner->get()->type . ">"
            );
        }
        $identifier = $scanner->get();
        parent::eofLessNext($scanner, ["<OPERATOR ':'>"]);
        if (!$scanner->get()->isOperator(':')) {
            throw new SyntaxException(
                $scanner->get()->point,
                $scanner->get()->text,
                ["<OPERATOR ':'"],
                "<" . $scanner->get()->type . ">"
            );
        }
        parent::eofLessNext($scanner, ["<TYPE-DENOTER>"]);
        $denoter = TypeDenoter::parse($scanner);
        if (!$scanner->get()->isTypeDenoter()) {
            throw new SyntaxException(
                $scanner->get()->point,
                $scanner->get()->text,
                ['<TYPE-DENOTER>'],
                "<" . $scanner->get()->type . ">"
            );
        }
        $denoter = $scanner->get();
        $scanner->next();
        return new VarDec($identifier, $denoter);
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
