<?php

namespace vendor\SyntaxParser\Nodes;

use vendor\SyntaxParser\Nodes\Node;

class IfStatement extends Node
{
    public $clause = null;
    public $trueStatements = null;
    public $falseStatements = null;

    public function __construct($scanner, $_symTable)
    {
        if (!$scanner->get()->isKeyword('if')) {
            parent::SimpleException($scanner, ['<KEYWORD \'if\'>']);
        }
        $scanner->next();
        $this->clause = new Expression($scanner, $_symTable);
        $class = get_class($this->clause->symType);
        $booleanType = $_symTable->findRecursive('boolean');
        if (!$class::equal($booleanType, $this->clause->symType)) {
            $this->clause = new TypeCast($this->clause, $booleanType);
        }
        if (!$scanner->get()->isKeyword('then')) {
            parent::simpleException($scanner, ['<KEYWORD \'then\'>']);
        }
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
        $clause = [
            "id" => $id++,
            "name" => "Clause",
            "children" => [$this->clause->toIdArray($id)]
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
            $clause
        );
        return $node;
    }
}
