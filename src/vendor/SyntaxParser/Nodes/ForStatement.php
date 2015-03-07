<?php

namespace vendor\SyntaxParser\Nodes;

use vendor\SyntaxParser\Nodes\Node;
use vendor\SemanticParser\Nodes\SymSimpleType;
use vendor\Exception\SemanticException;
use vendor\Utility\Globals;

class ForStatement extends Node
{
    public $counter = null;
    public $from = null;
    public $to = null;
    public $direction = null;
    public $statements = null;
    const DIRECTION_ASC = 0;
    const DIRECTION_DESC = 1;

    public function __construct($scanner, $_symTable)
    {
        parent::requireKeyword($scanner, 'for');
        $scanner->next();
        $this->counter = VariableAccess::parse($scanner, $_symTable);
        if (!SymSimpleType::equal($this->counter->symType, Globals::getSimpleType('integer'))) {
            SemanticException::raw($scanner, 'Counter must be integer');
        }
        parent::requireOperator($scanner, ':=');
        $scanner->next();
        $this->from = new Expression($scanner, $_symTable);
        if (!SymSimpleType::equal($this->from->symType, Globals::getSimpleType('integer'))) {
            $this->from = new TypeCast($this->from, Globals::getSimpleType('integer'));
        }
        switch($scanner->get()->getValue()) {
            case 'to':
                $this->direction = self::DIRECTION_ASC;
                break;
            case 'downto':
                $this->direction = self::DIRECTION_DESC;
                break;
            default:
                parent::simpleException($scanner, ['<KEYWORD \'to\'>', '<KEYWORD \'downto\'>']);
        }
        $scanner->next();
        $this->to = new Expression($scanner, $_symTable);
        if (!SymSimpleType::equal($this->to->symType, Globals::getSimpleType('integer'))) {
            $this->to = new TypeCast($this->to, Globals::getSimpleType('integer'));
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
            "name" => "For-statement",
            "children" => []
        ];
        $counter = [
            "id" => $id++,
            "name" => "Counter",
            "children" => [$this->counter->toIdArray($id)]
        ];
        $from = [
            "id" => $id++,
            "name" => "From",
            "children" => [$this->from->toIdArray($id)]
        ];
        $to = [
            "id" => $id++,
            "name" => "To",
            "children" => [$this->to->toIdArray($id)]
        ];
        $direction = [
            "id" => $id++,
            "name" => "Direction = " . ($this->direction == self::DIRECTION_ASC ? "ASCENDING" : "DESCENDING")
        ];
        $statements = $this->statements->toIdArray($id);
        $node["children"] = [$counter, $from, $to, $statements, $direction];
        return $node;
    }
}
