<?php

namespace vendor\SyntaxParser\Nodes;

use vendor\SyntaxParser\Nodes\Node;
use vendor\TokenParser\Scanner;
use vendor\Exception\SyntaxException;

class VarDecPart extends Node
{
    private $varDecs = [];

    public function __construct($scanner, $_symTable)
    {
        $decs = [];
        while ($scanner->get()->isIdentifier()) {
            array_push($this->varDecs, new VarDec($scanner, $_symTable));
            parent::semicolonPass($scanner);
        }
        if (empty($this->varDecs)) {
            parent::simpleException($scanner, ['<IDENTIFIER>']);
        }
    }
}
