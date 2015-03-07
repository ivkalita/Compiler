<?php

namespace vendor\SyntaxParser\Nodes;

use vendor\SyntaxParser\Nodes\Node;
use vendor\SemanticParser\Nodes\SymFunc;
use vendor\SemanticParser\Nodes\SymType;
use vendor\TokenParser\Scanner;
use vendor\Exception\SyntaxException;
use vendor\Exception\SemanticException;
use vendor\SemanticParser\Nodes\SymVar;
use vendor\Utility\Globals;

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
        parent::requireOperator($scanner, ':');
        $scanner->next();
        $type = SymType::parseFixed($scanner, $symTable);
        if ($type->isAnonim()) {
            $symTable->append($type);
        }
        $_symTable->append(new SymVar('result', $type));
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
        return $func;
    }

    protected function getInfo()
    {
        $info = parent::getInfo();
        $info .= ": {$this->symbol->returnType->identifier}";
        return $info;
    }

    public function toIdArray(&$id)
    {
        $node = parent::toIdArray($id);
        $node["name"] = "Function";
        return $node;
    }
}
