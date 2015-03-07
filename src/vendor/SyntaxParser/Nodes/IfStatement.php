<?php

namespace vendor\SyntaxParser\Nodes;

use vendor\SyntaxParser\Nodes\Node;
use vendor\Utility\Globals;

class IfStatement extends Node
{
    public $condition = null;
    public $trueStatements = null;
    public $falseStatements = null;

    public function __construct($scanner, $_symTable)
    {
        parent::requireKeyword($scanner, 'if');
        $scanner->next();
        $this->condition = new Expression($scanner, $_symTable);
        $class = get_class($this->condition->symType);
        if (!$class::equal($this->condition->symType, Globals::getSimpleType('boolean'))) {
            $this->condition = new TypeCast($this->condition, $booleanType);
        }
        parent::requireKeyword($scanner, 'then');
        $scanner->next();
        $this->trueStatements = CompoundStatement::smartParse($scanner, $_symTable);
        if ($scanner->get()->isSemicolon()) {
            return;
        }
        if ($scanner->get()->isKeyword('else')) {
            $scanner->next();
            $this->falseStatements = CompoundStatement::smartParse($scanner, $_symTable);
        } else {
            parent::simpleException($scanner, ['<OPERATOR \';\'>', '<KEYWORD \'else\'>']);
        }
    }

    public function toIdArray(&$id)
    {
        $node = [
            "id" => $id++,
            "name" => "If-statement",
            "children" => []
        ];
        $condition = [
            "id" => $id++,
            "name" => "Condition",
            "children" => [$this->condition->toIdArray($id)]
        ];
        $id++;
        if ($this->trueStatements) {
            array_push(
                $node["children"],
                [
                    "id" => $id++,
                    "name" => "TrueStatements",
                    "children" => [$this->trueStatements->toIdArray($id)]
                ]
            );
            $id++;
        }
        if ($this->falseStatements) {
            array_push(
                $node["children"],
                [
                    "id" => $id++,
                    "name" => "FalseStatements",
                    "children" => [$this->falseStatements->toIdArray($id)]
                ]
            );
            $id++;
        }
        array_push(
            $node["children"],
            $condition
        );
        return $node;
    }
}
