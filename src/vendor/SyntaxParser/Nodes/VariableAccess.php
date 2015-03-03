<?php

namespace vendor\SyntaxParser\Nodes;

use vendor\SyntaxParser\Nodes\Node;
use vendor\TokenParser\Scanner;
use vendor\Exception\SyntaxException;

class VariableAccess extends Node
{
    private $variable = null;

    //key operator - such an operator, that signals to go deeper parsing
    static private function isKeyOperator($token)
    {
        return $token->isOperator() && in_array($token->getValue(), ['.', '[', '^']);
    }

    static public function parse($scanner, $identifier = null)
    {
        if (!$identifier) {
            if (!$scanner->get()->isIdentifier()) {
                parent::simpleException($scanner, ['<IDENTIFIER>']);
            }
            $identifier = $scanner->get();
            parent::eofLessNext(
                $scanner,
                ["<OPERATOR '.'>", "<OPERATOR '['>", "<OPERATOR '^'>"]
            );
        }
        //entire-variable
        $cur = new EntireVariable($identifier);
        //variable-access
        $curVA = new VariableAccess($cur);
        while (self::isKeyOperator($scanner->get())) {
            $operVal = $scanner->get()->getValue();
            parent::eofLessNext(
                $scanner,
                ["<OPERATOR '.'>", "<OPERATOR '['>", "<OPERATOR '^'>"]
            );
            switch ($operVal) {
                case '.':
                    $curVA->variable = ComponentVariable::parse($scanner, clone $curVA);
                    break;
                case '[':
                    $curVA->variable = IndexedVariable::parse($scanner, clone $curVA);
                    break;
                case '^':
                    $curVA->variable = IdentifiedVariable::parse($scanner, clone $curVA);
                    break;
            }
        }
        return $curVA;
    }

    static public function firstTokens()
    {
        return ['<IDENTIFIER>'];
    }

    public function __construct($variable)
    {
        $this->variable = $variable;
    }

    public function toArray()
    {
        return [
            "VariableAccess" => $this->variable->toArray()
        ];
    }

    public function toIdArray(&$id)
    {
        return $this->variable->toIdArray($id);
    }
}
