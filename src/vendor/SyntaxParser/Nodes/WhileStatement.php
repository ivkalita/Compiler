<?php

namespace vendor\SyntaxParser\Nodes;

use vendor\SyntaxParser\Nodes\Node;
use vendor\SemanticParser\Nodes\SymSimpleType;
use vendor\Exception\SemanticException;
use vendor\Utility\Flags;

class WhileStatement extends Node
{
    public $clause = null;
    public $statements = null;

    public function __construct($scanner, $_symTable)
    {
        $booleanType = $_symTable->findRecursive('boolean');
        if (!$scanner->get()->isKeyword('while')) {
            parent::simpleException($scanner, ['<KEYWORD \'while\'>']);
        }
        $scanner->next();
        $this->clause = new Expression($scanner, $_symTable);
        if (!SymSimpleType::equal($this->clause->symType, $booleanType)) {
            $this->clause = new TypeCast($this->clause, $booleanType);
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
            "name" => "While-statement",
            "children" => []
        ];
        $clause = [
            "id" => $id++,
            "name" => "Clause",
            "children" => [$this->clause->toIdArray($id)]
        ];
        $statements = $this->statements->toIdArray($id);
        $node["children"] = [$clause, $statements];
        return $node;
    }
}
