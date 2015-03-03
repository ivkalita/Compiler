<?php

namespace vendor\SyntaxParser\Nodes;

use vendor\SyntaxParser\Nodes\Node;
use vendor\TokenParser\Scanner;
use vendor\Exception\SyntaxException;

class CompoundStatement extends Node
{
    private $statements = null;

    static public function parse($scanner)
    {
        $statements = [];
        if (!$scanner->get()->isKeyword('begin')) {
            parent::simpleException($scanner, ["<KEYWORD 'begin'>"]);
        }
        $scanner->next();
        while (!$scanner->get()->isKeyword('end')) {
            $statements[] = Statement::parse($scanner);
        }
        $scanner->next();
        return new CompoundStatement($statements);
    }

    public function __construct($statements)
    {
        $this->statements = $statements;
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
