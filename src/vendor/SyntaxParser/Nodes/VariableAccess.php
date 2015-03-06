<?php

namespace vendor\SyntaxParser\Nodes;

use vendor\SyntaxParser\Nodes\Node;
use vendor\TokenParser\Scanner;
use vendor\Exception\SyntaxException;

class VariableAccess extends Node
{
    public $variable = null;
    public $symType = null;

    //key operator - such an operator, that signals to go deeper parsing
    static private function isKeyOperator($token)
    {
        return $token->isOperator() && in_array($token->getValue(), ['.', '[', '^']);
    }

    static public function parse($scanner, $_symTable, $identifier = null)
    {
        if (!$identifier) {
            if (!$scanner->get()->isIdentifier()) {
                parent::simpleException($scanner, ['<IDENTIFIER>']);
            }
            $identifier = $scanner->get();
            $scanner->next();
        }
        //entire-variable
        $cur = new EntireVariable($scanner, $_symTable, $identifier);
        //variable-access
        $curVA = new VariableAccess($cur);
        while (self::isKeyOperator($scanner->get())) {
            $operVal = $scanner->get()->getValue();
            $scanner->next();
            switch ($operVal) {
                case '.':
                    $curVA->variable = new ComponentVariable($scanner, $_symTable, clone $curVA);
                    break;
                case '[':
                    $curVA->variable = new IndexedVariable($scanner, $_symTable, clone $curVA);
                    break;
                case '^':
                    $curVA->variable = new IdentifiedVariable($scanner, $_symTable, clone $curVA);
                    break;
            }
        }
        $curVA->symType = $curVA->variable->symType;
        return $curVA;
    }

    public function __construct($variable)
    {
        $this->variable = $variable;
        $this->symType = $this->variable->symType;
    }

    public function toIdArray(&$id)
    {
        return $this->variable->toIdArray($id);
    }
}
