<?php

namespace vendor\SyntaxParser\Nodes;

use vendor\SyntaxParser\Nodes\Node;

class SimpleExpression extends Node
{
    //simple-expression = [ sign ] term { adding-operator term }
    private $node = null;

    static public function parse($scanner)
    {
        $sign = null;
        if ($scanner->get()->isOperator('+') || $scanner->get()->isOperator('-')) {
            $sign = $scanner->get();
            $scanner->next();
        }
        $term = Term::parse($scanner);
        while ($scanner->get()->isAdditive()) {
            $operator = $scanner->get();
            $scanner->next();
            $term2 = Term::parse($scanner);
            $term = new BinOp($term, $term2, $operator);
        }
        if ($sign) {
            $term = new UnOp($term, $sign);
        }
        return new SimpleExpression($term);
    }

    public function __construct($tip)
    {
        $this->tip = $tip;
    }

    public function toIdArray(&$id)
    {
        return $this->tip->toIdArray(++$id);
    }
}
