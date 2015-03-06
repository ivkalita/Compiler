<?php

namespace vendor\SyntaxParser\Nodes;

use vendor\SyntaxParser\Nodes\Node;
use vendor\TokenParser\Scanner;
use vendor\Exception\SyntaxException;
// use vendor\Exception\SemanticException;
// use vendor\Utility\Flags;

class Statement extends Node
{
    private $statement = null;
    //null means empty-statement

    public function __construct($scanner, $_symTable)
    {
        $this->statement = null;
        //TODO: change stupid check on something more internal
        $identifier = $scanner->get();
        $symbol = $_symTable->findRecursive($identifier->getValue());
        if ($symbol != null && get_class($symbol) == 'vendor\SemanticParser\Nodes\SymProc') {
            $scanner->next();
            $paramList = new ActualParamList($scanner, $_symTable);
            $this->statement = new FunctionDesignator($identifier, $paramList, $_symTable, false);
            return;
        }
        if ($identifier->isKeyword()) {
            switch ($identifier->getValue()) {
                case 'if':
                    $this->statement = new IfStatement($scanner, $_symTable);
                    return;
                case 'for':
                    $this->statement = new ForStatement($scanner, $_symTable);
                    return;
                case 'while':
                    $this->statement = new WhileStatement($scanner, $_symTable);
                    return;
                case 'case':
                    $this->statement = new SwitchStatement($scanner, $_symTable);
                    return;
                case 'break':
                case 'continue':
                case 'exit':
                    $this->statement = new ControlStatement($scanner, $_symTable);
                    return;
            }
        }
        $this->statement = new Expression($scanner, $_symTable);
        if ($scanner->get()->isOperator(':=')) {
            $this->statement = new AssignmentStatement($scanner, $_symTable, $this->statement);
        }
    }

    public function toIdArray(&$id)
    {
        if (!$this->statement) {
            $node = [
                "id" => $id,
                "name" => 'empty-statement'
            ];
            $id++;
            return $node;
        }
        return $this->statement->toIdArray($id);
    }
}
