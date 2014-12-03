<?php

namespace vendor\SyntaxParser\Nodes;

use vendor\SyntaxParser\Nodes\Node;
use vendor\TokenParser\Scanner;
use vendor\Exception\SyntaxException;

class TypeDefPart extends Node
{
    private $typeDefs = [];

    static public function parse($scanner)
    {
        $finalKeyWords = ['var', 'function', 'procedure', 'begin'];
        $defs = [];
        while ($scanner->get()->isIdentifier()) {
            array_push($defs, TypeDef::parse($scanner));
            parent::semicolonPass($scanner);
            parent::eofLessNext(
                $scanner,
                array_merge(
                    ['<IDENTIFIER>'],
                    array_map(function($word) {
                        return "<KEYWORD '$word'>";
                    }, $finalKeyWords)
                )
            );
        }
        if (empty($defs)) {
            parent::simpleException($scanner, ['<IDENTIFIER>']);
        }
        return new TypeDefPart($defs);
    }

    static public function firstTokens()
    {
        return ["<KEYWORD 'type'>"];
    }

    public function __construct($defs)
    {
        $this->typeDefs = $defs;
    }
}
