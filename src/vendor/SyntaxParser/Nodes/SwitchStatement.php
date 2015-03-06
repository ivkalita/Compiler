<?php

namespace vendor\SyntaxParser\Nodes;

use vendor\SyntaxParser\Nodes\Node;
use vendor\SemanticParser\Nodes\SymSimpleType;
use vendor\Exception\SemanticException;
use vendor\Utility\Flags;

class SwitchStatement extends Node
{
    public $clause = null;
    public $cases = [];
    public $default = null;

    //case expr of
    //  caseA:
    //      statement/compound_statement
    //  caseA:
    //      statement/compound_statement

    public function __construct($scanner, $_symTable)
    {
        if (!$scanner->get()->isKeyword('case')) {
            parent::simpleException($scanner, ['<KEYWORD \'case\'>']);
        }
        $scanner->next();
        $this->clause = new Expression($scanner, $_symTable);
        if (!$scanner->get()->isKeyword('of')) {
            parent::simpleException($scanner, ['<KEYWORD \'of\'>']);
        }
        $scanner->next();
        Flags::$switchDepth++;
        while (true) {
            if ($scanner->get()->isKeyword('else')) {
                $scanner->next();
                $this->default = CompoundStatement::smartParse($scanner, $_symTable);
                parent::semicolonPass($scanner);
                break;
            }
            $caseClause = new Expression($scanner, $_symTable);
            $class = get_class($caseClause->symType);
            if (!$class::equal($caseClause->symType, $this->clause->symType)) {
                $caseClause = new TypeCast($caseClause, $this->clause->symType);
            }
            if (!$scanner->get()->isOperator(':')) {
                parent::simpleException($scanner, ['<OPERATOR \':\'>']);
            }
            $scanner->next();
            $caseStatements = CompoundStatement::smartParse($scanner, $_symTable);
            $this->cases[] = [
                "clause" => $caseClause,
                "statements" => $caseStatements
            ];
            parent::semicolonPass($scanner);
            if ($scanner->get()->isKeyword('end')) {
                break;
            }
        }
        Flags::$switchDepth--;
        if (!$scanner->get()->isKeyword('end')) {
            parent::simpleException($scanner, ['<KEYWORD \'end\'>']);
        }
        $scanner->next();
    }

    public function toIdArray(&$id)
    {
        $node = [
            "id" => $id++,
            "name" => "Switch-statement"
        ];
        $clause = [
            "id" => $id++,
            "name" => "Clause"
        ];
        $clause["children"] = [$this->clause->toIdArray($id)];
        $node["children"] = [$clause];
        for ($i = 0; $i < count($this->cases); $i++) {
            $caseClause = [
                "id" => $id++,
                "name" => "caseClause"
            ];
            $caseClause["children"] = [$this->cases[$i]["clause"]->toIdArray($id)];
            $caseStatements = [
                "id" => $id++,
                "name" => "caseStmts"
            ];
            $caseStatements["children"] = [$this->cases[$i]["statements"]->toIdArray($id)];
            $id++;
            $case = [
                "id" => $id++,
                "name" => "Case",
                "children" => [$caseClause, $caseStatements]
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
