<?php

namespace vendor\SyntaxParser\Nodes;

use vendor\SyntaxParser\Nodes\Node;
use vendor\SemanticParser\Nodes\SymSimpleType;
use vendor\Exception\SemanticException;
use vendor\Utility\Globals;

class WhileStatement extends Node
{
    public $condition = null;
    public $statements = null;

    public function __construct($scanner, $_symTable)
    {
        parent::requireKeyword($scanner, 'while');
        $scanner->next();
        $this->condition = new Expression($scanner, $_symTable);
        if (!SymSimpleType::equal($this->condition->symType, Globals::getSimpleType('boolean'))) {
            $this->condition = new TypeCast($this->condition, Globals::getSimpleType('boolean'));
        }
        parent::requireKeyword($scanner, 'do');
        $scanner->next();
        Globals::$loopDepth++;
        $this->statements = CompoundStatement::smartParse($scanner, $_symTable);
        Globals::$loopDepth--;
    }

    public function toIdArray(&$id)
    {
        $node = [
            "id" => $id++,
            "name" => "While-statement",
            "children" => []
        ];
        $condition = [
            "id" => $id++,
            "name" => "Condition",
            "children" => [$this->condition->toIdArray($id)]
        ];
        $statements = $this->statements->toIdArray($id);
        $node["children"] = [$condition, $statements];
        return $node;
    }
}
