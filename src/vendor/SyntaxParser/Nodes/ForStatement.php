<?php

namespace vendor\SyntaxParser\Nodes;

use vendor\SyntaxParser\Nodes\Node;
use vendor\SemanticParser\Nodes\SymSimpleType;
use vendor\Exception\SemanticException;
use vendor\Utility\Flags;

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
        $integerType = $_symTable->findRecursive('integer');
        if (!$scanner->get()->isKeyword('for')) {
            parent::simpleException($scanner, ['<KEYWORD \'for\'>']);
        }
        $scanner->next();
        $this->counter = VariableAccess::parse($scanner, $_symTable);
        if (!SymSimpleType::equal($this->counter->symType, $integerType)) {
            SemanticException::raw($scanner, 'Counter must be integer');
        }
        if (!$scanner->get()->isOperator(':=')) {
            parent::simpleException($scanner, ['<OPERATOR \':=\'>']);
        }
        $scanner->next();
        $this->from = new Expression($scanner, $_symTable);
        if (!SymSimpleType::equal($this->from->symType, $integerType)) {
            $this->from = new TypeCast($this->from, $integerType);
        }
        if (!$scanner->get()->isKeyword('to') && !$scanner->get()->isKeyword('downto')) {
            parent::simpleException($scanner, ['<KEYWORD \'to\'>', '<KEYWORD \'downto\'>']);
        }
        switch($scanner->get()->getValue()) {
            case 'to':
                $this->direction = self::DIRECTION_ASC;
                break;
            case 'downto':
                $this->direction = self::DIRECTION_DESC;
                break;
        }
        $scanner->next();
        $this->to = new Expression($scanner, $_symTable);
        if (!SymSimpleType::equal($this->to->symType, $integerType)) {
            $this->to = new TypeCast($this->to, $integerType);
        }
        if (!$scanner->get()->isKeyword('do')) {
            parent::simpleException($scanner, ['<KEYWORD \'do\'>']);
        }
        $scanner->next();
        Flags::$loopDepth++;
        $this->statements = CompoundStatement::smartParse($scanner, $_symTable);
        Flags::$loopDepth--;
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
            "name" => "Direction = " . ($this->direction == self::DIRECTION_ASC ? "STRAIGHT" : "REVERSE")
        ];
        $statements = $this->statements->toIdArray($id);
        $node["children"] = [$counter, $from, $to, $statements, $direction];
        return $node;
    }
}
