<?php

namespace vendor\SyntaxParser\Nodes;

use vendor\SyntaxParser\Nodes\Node;
use vendor\TokenParser\Scanner;
use vendor\Exception\SyntaxException;
use vendor\SemanticParser\Nodes\SymConst;

class Block extends Node
{
    public $defConst = null;
    public $defType = null;
    public $decVar = null;
    public $decFP = null; //functions and procedures declarations
    public $statements = null;


    public function __construct($scanner, $_symTable)
    {
        if (!$scanner->get()->isKeyword()) {
            parent::simpleException($scanner, ['<KEYWORD>']);
        }
        $cToken = $scanner->get();
        if ($cToken->isEq('const')) {
            $scanner->next();
            $this->defConst = new ConstDefPart($scanner, $_symTable);
            $cToken = $scanner->get();
        }
        // echo $cToken->getStr();
        if ($cToken->isEq('type')) {
            $scanner->next();
            $defType = new TypeDefPart($scanner, $_symTable);
            $cToken = $scanner->get();
        }
        if ($cToken->isEq('var')) {
            $scanner->next();
            $finalKeyWords = ['function', 'procedure', 'begin'];
            $this->decVar = new VarDecPart($scanner, $_symTable, $finalKeyWords);
            $cToken = $scanner->get();
        }
        if ($cToken->isEq('function') || $cToken->isEq('procedure')) {
            $decFP = new FPPart($scanner, $_symTable);
        }
        $this->statements = new CompoundStatement($scanner, $_symTable);
    }

    public function toIdArray(&$id)
    {
        $node = [
            "id"       => $id,
            "name"     => "Block",
            "children" => []
        ];
        if ($this->defConst) {
            array_push($node["children"], $this->defConst->toIdArray(++$id));
        }
        array_push($node["children"], $this->statements->toIdArray(++$id));
        return $node;
    }
}
