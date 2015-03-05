<?php

namespace vendor\SyntaxParser\Nodes;

use vendor\SyntaxParser\Nodes\Node;
use vendor\TokenParser\Scanner;
use vendor\Exception\SyntaxException;

class CompoundStatement extends Node
{
    private $statements = null;

    public function __construct($scanner, $_symTable)
    {
        $this->statements = [];
        if (!$scanner->get()->isKeyword('begin')) {
            parent::simpleException($scanner, ["<KEYWORD 'begin'>"]);
        }
        $scanner->next();
        while (!$scanner->get()->isKeyword('end')) {
            $this->statements[] = new Statement($scanner, $_symTable);
        }
        $scanner->next();
    }

    public function toIdArray(&$id)
    {
        $node = [
            "id"       => $id,
            "name"     => "CompoundStatement",
            "children" =>
                array_map(function(&$statement) use (&$id) {
                    $id++;
                    return $statement->toIdArray($id);
                }, $this->statements)
        ];
        return $node;
    }
}
