<?php

namespace vendor\SyntaxParser\Nodes;

use vendor\SyntaxParser\Nodes\Node;
use vendor\SemanticParser\Nodes\SymSimpleType;
use vendor\Exception\SemanticException;
use vendor\Utility\Globals;

class SwitchStatement extends Node
{
    public $condition = null;
    public $cases = [];
    public $default = null;


    public function __construct($scanner, $_symTable)
    {
        parent::requireKeyword($scanner, 'case');
        $scanner->next();
        $this->condition = new Expression($scanner, $_symTable);
        parent::requireKeyword($scanner, 'of');
        $scanner->next();
        Globals::$switchDepth++;
        while (true) {
            if ($scanner->get()->isKeyword('else')) {
                $scanner->next();
                $this->default = CompoundStatement::smartParse($scanner, $_symTable);
                parent::semicolonPass($scanner);
                break;
            }
            $caseCondition = new Expression($scanner, $_symTable);
            $caseCondition = TypeCast::tryTypeCast($caseCondition, $this->condition->symType, false);
            parent::requireOperator($scanner, ':');
            $scanner->next();
            $caseStatements = CompoundStatement::smartParse($scanner, $_symTable);
            $this->cases[] = [
                "condition" => $caseCondition,
                "statements" => $caseStatements
            ];
            parent::semicolonPass($scanner);
            if ($scanner->get()->isKeyword('end')) {
                break;
            }
        }
        Globals::$switchDepth--;
        parent::requireKeyword($scanner, 'end');
        $scanner->next();
    }

    public function toIdArray(&$id)
    {
        $node = [
            "id" => $id++,
            "name" => "Switch-statement"
        ];
        $condition = [
            "id" => $id++,
            "name" => "Condition"
        ];
        $condition["children"] = [$this->condition->toIdArray($id)];
        $node["children"] = [$condition];
        for ($i = 0; $i < count($this->cases); $i++) {
            $caseCondition = [
                "id" => $id++,
                "name" => "caseCondition"
            ];
            $caseCondition["children"] = [$this->cases[$i]["condition"]->toIdArray($id)];
            $caseStatements = [
                "id" => $id++,
                "name" => "caseStmts"
            ];
            $caseStatements["children"] = [$this->cases[$i]["statements"]->toIdArray($id)];
            $id++;
            $case = [
                "id" => $id++,
                "name" => "Case",
                "children" => [$caseCondition, $caseStatements]
            ];
            array_push($node["children"], $case);
        }
        if ($this->default != null) {
            $default = [
                "id" => $id++,
                "name" => "Default"
            ];
            $default["children"] = [$this->default->toIdArray($id)];
            array_push($node["children"], $default);
        }
        return $node;
    }
}
