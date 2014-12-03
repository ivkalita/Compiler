<?php

namespace vendor\SyntaxParser\Nodes;

use vendor\SyntaxParser\Nodes\Node;
use vendor\TokenParser\Scanner;
use vendor\Exception\SyntaxException;

class ConstDefPart extends Node
{
    private $constDefs = [];

    static public function parse($scanner)
    {
        $finalKeyWords = [
            // 'type',
            'var',
            // 'function',
            // 'procedure',
            'begin'
        ];
        $defs = [];
        while ($scanner->get()->isIdentifier()) {
            array_push($defs, ConstDef::parse($scanner));
            parent::semicolonPass($scanner);
        }
        if (empty($defs)) {
            parent::simpleException($scanner, ['<IDENTIFIER>']);
        }
        return new ConstDefPart($defs);
    }

    public function __construct($defs)
    {
        $this->constDefs = $defs;
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
