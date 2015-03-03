<?php

namespace vendor\SyntaxParser\Nodes;

use vendor\SyntaxParser\Nodes\Node;
use vendor\SemanticParser\Nodes\SymTable;
use vendor\SemanticParser\Nodes\SymProc;
use vendor\SemanticParser\Nodes\SymType;
use vendor\SemanticParser\Nodes\SymArg;
use vendor\TokenParser\Scanner;
use vendor\Exception\SyntaxException;
use vendor\Exception\SemanticException;

class Proc extends Node
{
    public $block = null;
    public $symbol = null;


    public function __construct($block, $symbol)
    {
        $this->block = $block;
        $this->symbol = $symbol;
    }

    static protected function parseSignature($scanner, $_symTable)
    {
        if (!$scanner->get()->isIdentifier()) {
            parent::simpleException($scanner, ['<IDENTIFIER>']);
        }
        $symTable = new SymTable($_symTable);
        $identifier = $scanner->get()->getValue();
        $scanner->next();
        if ($scanner->get()->isOperator('(')) {
            $scanner->next();
            $idx = 0;
            while (true) {
                $identifiers = [];
                while ($scanner->get()->isIdentifier()) {
                    $identifiers[] = $scanner->get()->getValue();
                    $scanner->next();
                    if (!$scanner->get()->isOperator(',')) {
                        break;
                    }
                    $scanner->next();
                }
                if (count($identifiers) > 0) {
                    if (!$scanner->get()->isOperator(':')) {
                        parent::simpleException($scanner, ['<OPERATOR \':\'>']);
                    }
                    $scanner->next();
                    $type = SymType::parseFixed($scanner, $symTable);
                    foreach($identifiers as $arg) {
                        $symTable->append(new SymArg($arg, $type, $idx));
                        $idx++;
                    }
                }
                if ($scanner->get()->isOperator(')')) {
                    $scanner->next();
                    break;
                }
                parent::semicolonPass($scanner);
            }
        }
        return [$identifier, $symTable];
    }

    static public function smartParse($scanner, $_symTable)
    {
        $scanner->next();
        list(
            $identifier,
            $symTable
        ) = self::parseSignature($scanner, $_symTable);
        parent::semicolonPass($scanner);

        $symbol = new SymProc($identifier, $symTable, null);
        $proc = new Proc(null, $symbol);
        $proc->symbol->node = $proc;

        if ($scanner->get()->isKeyword('forward')) {
            $_symTable->appendForwardable($proc->symbol);
            $scanner->next();
            return $proc;
        }
        $proc->block = new Block($scanner, $symTable);
        $_symTable->appendForwardable($proc->symbol);
    }
}
