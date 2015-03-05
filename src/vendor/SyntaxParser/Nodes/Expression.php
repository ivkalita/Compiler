<?php

namespace vendor\SyntaxParser\Nodes;

use vendor\SyntaxParser\Nodes\Node;

class Expression extends Node
{
    //expression = expr [ relational-operator expr2 ]
    private $node = null;
    public $symType = null;

    static private function isRelationalOperator($token)
    {
        return
            ($token->isOperator() && in_array($token->getValue(), ['=', '<>', '>', '<', '<=', '>='])) ||
            ($token->isKeyword('in'));
    }

    public function __construct($scanner, $_symTable)
    {
        $expr = new SimpleExpression($scanner, $_symTable);
        $this->node = $expr;
        if (self::isRelationalOperator($scanner->get())) {
            $operator = $scanner->get();
            $scanner->next();
            $expr2 = new SimpleExpression($scanner, $_symTable);
            $this->node = new BinOp($expr, $expr2, $operator);
        }
        $this->symType = $this->node->symType;
    }

    public function toIdArray(&$id)
    {
        return $this->node->toIdArray($id);
    }
}
