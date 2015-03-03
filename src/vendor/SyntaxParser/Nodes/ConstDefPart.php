<?php

namespace vendor\SyntaxParser\Nodes;

use vendor\SyntaxParser\Nodes\Node;
use vendor\TokenParser\Scanner;
use vendor\Exception\SyntaxException;

class ConstDefPart extends Node
{
    private $constDefs = [];

    public function __construct($scanner, $_symTable)
    {
        $finalKeyWords = [
            'type',
            'var',
            'function',
            'procedure',
            'begin'
        ];
        $defs = [];
        while ($scanner->get()->isIdentifier()) {
            array_push($defs, new ConstDef($scanner, $_symTable));
            parent::semicolonPass($scanner);
        }
        if (empty($defs)) {
            parent::simpleException($scanner, ['<IDENTIFIER>']);
        }
    }

    public function toIdArray(&$id)
    {
        $node = [
            "id"       => $id,
            "name"     => "ConstDefPart",
            "children" =>
                array_map(function(&$constDef) use (&$id) {
                    $id++;
                    return $constDef->toIdArray($id);
                }, $this->constDefs)
        ];
        return $node;
    }
}
