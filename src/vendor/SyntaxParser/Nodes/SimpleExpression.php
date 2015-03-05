<?php

namespace vendor\SyntaxParser\Nodes;

use vendor\SyntaxParser\Nodes\Node;

class SimpleExpression extends Node
{
    //simple-expression = [ sign ] term { adding-operator term }
    private $tip = null;
    public $symType = null;

    public function __construct($scanner, $_symTable)
    {
        $sign = null;
        if ($scanner->get()->isOperator('+') || $scanner->get()->isOperator('-')) {
            $sign = $scanner->get();
            $scanner->next();
        }
        $term = new Term($scanner, $_symTable);
        while ($scanner->get()->isAdditive()) {
            $operator = $scanner->get();
            $scanner->next();
            $term2 = new Term($scanner, $_symTable);
            $term = new BinOp($term, $term2, $operator, $_symTable);
        }
        if ($sign) {
            $term = new UnOp($term, $sign, $_symTable);
        }
        $this->tip = $term;
        $this->symType = $this->tip->symType;
    }

    public function toIdArray(&$id)
    {
        return $this->tip->toIdArray(++$id);
    }
}
