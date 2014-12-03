<?php

namespace vendor\SyntaxParser\Nodes;

use vendor\SyntaxParser\Nodes\Node;
use vendor\TokenParser\Scanner;
use vendor\Exception\SyntaxException;

class Block extends Node
{
    private $defConst;
    private $defType;
    private $decVar;
    private $decFP; //functions and procedures declarations
    private $statements;

    static public function parse($scanner)
    {
        $defConst = null;
        $defType = null;
        $decVar = null;
        $decFP = null;
        $statements = null;
        if (!$scanner->get()->isKeyword()) {
            parent::simpleException($scanner, ['<KEYWORD>']);
        }
        $cToken = $scanner->get();
        if ($cToken->isEq('const')) {
            $scanner->next();
            $defConst = ConstDefPart::parse($scanner);
            $cToken = $scanner->get();
        }
        // if ($cToken->isEq('type')) {
            // $defType = TypeDefPart::parse($scanner);
            // $cToken = $scanner->get();
        // }
        if ($cToken->isEq('var')) {
            $scanner->next();
            $decVar = VarDecPart::parse($scanner);
            $cToken = $scanner->get();
        }
        // if ($cToken->isEq('function') || $cToken->isEq('procedure')) {
            // $decFP = FPDecPart::parse($scanner);
        // }
        $statements = CompoundStatement::parse($scanner);
        return new Block($defConst, $defType, $decVar, $decFP, $statements);
    }

    public function __construct($defConst, $defType, $decVar, $decFP, $statements)
    {
        $this->defConst = $defConst;
        $this->defType = $defType;
        $this->decVar = $decVar;
        $this->decFP = $decFP;
        $this->statements = $statements;
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
