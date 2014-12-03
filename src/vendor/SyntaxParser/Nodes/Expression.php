<?php

namespace vendor\SyntaxParser\Nodes;

use vendor\SyntaxParser\Nodes\Node;

class Expression extends Node
{
    //expression = expr [ relational-operator expr2 ]
    private $node = null;

    static private function isRelationalOperator($token)
    {
        return
            ($token->isOperator() && in_array($token->getValue(), ['=', '<>', '>', '<', '<=', '>='])) ||
            ($token->isKeyword('in'));
    }

    static public function parse($scanner)
    {
        $expr = SimpleExpression::parse($scanner);
        $node = $expr;
        if (self::isRelationalOperator($scanner->get())) {
            $operator = $scanner->get();
            $scanner->next();
            $expr2 = SimpleExpression::parse($scanner);
            $node = new BinOp($expr, $expr2, $operator);
        }
        return new Expression($node);
    }

    public function __construct($node)
    {
        $this->node = $node;
    }

    public function toIdArray(&$id)
    {
        return $this->node->toIdArray($id);
    }
}
