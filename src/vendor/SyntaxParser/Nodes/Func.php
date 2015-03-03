<?php

namespace vendor\SyntaxParser\Nodes;

use vendor\SyntaxParser\Nodes\Node;
use vendor\SemanticParser\Nodes\SymFunc;
use vendor\SemanticParser\Nodes\SymType;
use vendor\TokenParser\Scanner;
use vendor\Exception\SyntaxException;
use vendor\Exception\SemanticException;

class Func extends Proc
{
    public function __construct($block, $symbol)
    {
        parent::__construct($block, $symbol);
    }

    static protected function parseSignature($scanner, $_symTable)
    {
        list(
            $identifier,
            $symTable
        ) = parent::parseSignature($scanner, $_symTable);
        if (!$scanner->get()->isOperator(':')) {
            Node::simpleException($scanner, ['<OPERATOR \':\'>']);
        }
        $scanner->next();
        $type = SymType::parseFixed($scanner, $symTable);
        if ($type->isAnonim()) {
            $symTable->append($type);
        }
        return [$identifier, $symTable, $type];
    }

    static public function smartParse($scanner, $_symTable)
    {
        $scanner->next();
        list(
            $identifier,
            $symTable,
            $returnType
        ) = self::parseSignature($scanner, $_symTable);
        parent::semicolonPass($scanner);

        $symbol = new SymFunc($identifier, $symTable, null, $returnType);
        $func = new Func(null, $symbol);
        $func->symbol->node = $func;

        if ($scanner->get()->isKeyword('forward')) {
            $_symTable->appendForwardable($func->symbol);
            $scanner->next();
            return $proc;
        }
        $func->block = new Block($scanner, $symTable);
        $_symTable->appendForwardable($func->symbol);
    }
}
