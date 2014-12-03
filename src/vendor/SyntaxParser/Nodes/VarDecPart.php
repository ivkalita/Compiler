<?php

namespace vendor\SyntaxParser\Nodes;

use vendor\SyntaxParser\Nodes\Node;
use vendor\TokenParser\Scanner;
use vendor\Exception\SyntaxException;

class VarDecPart extends Node
{
    private $varDecs = [];

    static public function parse($scanner)
    {
        $finalKeyWords = ['function', 'procedure', 'begin'];
        $decs = [];
        while ($scanner->get()->isIdentifier()) {
            array_push($decs, VarDec::parse($scanner));
            parent::semicolonPass($scanner);
            parent::eofLessNext(
                $scanner,
                ['<KEYWORD>']
            );
        }
        if (empty($decs)) {
            parent::simpleException($scanner, ['<IDENTIFIER>']);
        }
        return new VarDecPart($decs);
    }

    static public function firstTokens()
    {
        return ["<KEYWORD 'var'>"];
    }

    public function __construct($decs)
    {
        $this->constDecs = $decs;
    }
}
