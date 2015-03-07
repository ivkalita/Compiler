<?php

namespace vendor\SyntaxParser\Nodes;

use vendor\SyntaxParser\Nodes\Node;
use vendor\TokenParser\Scanner;
use vendor\Exception\SyntaxException;
use vendor\SemanticParser\Nodes\SymConst;
use vendor\Utility\Globals;

class Block extends Node
{
    public $defConst = null;
    public $defType = null;
    public $decVar = null;
    public $decFP = null; //functions and procedures declarations
    public $statements = null;


    public function __construct($scanner, $_symTable)
    {
        $cToken = $scanner->get();
        if ($cToken->isEq('const')) {
            $scanner->next();
            $this->defConst = new ConstDefPart($scanner, $_symTable);
            $cToken = $scanner->get();
        }
        if ($cToken->isEq('type')) {
            $scanner->next();
            $this->defType = new TypeDefPart($scanner, $_symTable);
            $cToken = $scanner->get();
        }
        if ($cToken->isEq('var')) {
            $scanner->next();
            $this->decVar = new VarDecPart($scanner, $_symTable);
            $cToken = $scanner->get();
        }
        if ($cToken->isEq('function') || $cToken->isEq('procedure')) {
            $this->decFP = new FPPart($scanner, $_symTable);
        }
        Globals::$funcDepth++;
        $this->statements = new CompoundStatement($scanner, $_symTable);
        Globals::$funcDepth--;
    }

    public function toIdArray(&$id)
    {
        $node = [
            "id"       => $id++,
            "name"     => "Block",
            "children" => []
        ];
        if ($this->defConst) {
            array_push($node["children"], $this->defConst->toIdArray(++$id));
        }
        if ($this->decFP) {
            array_push($node["children"], $this->decFP->toIdArray(++$id));
        }
        array_push($node["children"], $this->statements->toIdArray(++$id));
        return $node;
    }
}
