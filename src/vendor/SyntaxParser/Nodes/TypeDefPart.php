<?php

namespace vendor\SyntaxParser\Nodes;

use vendor\SyntaxParser\Nodes\Node;
use vendor\TokenParser\Scanner;
use vendor\Exception\SyntaxException;

class TypeDefPart extends Node
{
    private $typeDefs = [];

    public function __construct($scanner, $_symTable)
    {
        $finalKeyWords = ['var', 'function', 'procedure', 'begin'];
        $defs = [];
        while ($scanner->get()->isIdentifier()) {
            array_push($defs, new TypeDef($scanner, $_symTable));
            parent::semicolonPass($scanner);
        }
        if (empty($defs)) {
            parent::simpleException($scanner, ['<IDENTIFIER>']);
        }
    }
}
