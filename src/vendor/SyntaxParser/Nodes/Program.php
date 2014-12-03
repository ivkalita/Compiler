<?php

namespace vendor\SyntaxParser\Nodes;

use vendor\SyntaxParser\Nodes\Node;
use vendor\TokenParser\Scanner;
use vendor\Exception\SyntaxException;

class Program extends Node
{
    private $heading;
    private $block;

    static public function parse($scanner)
    {
        $scanner->next();
        $heading = ProgramHeading::parse($scanner);
        parent::semicolonPass($scanner);
        $block = Block::parse($scanner);
        if (!$scanner->get()->isOperator('.')) {
            parent::simpleException($scanner, ["<OPERATOR '.'>"]);
        }
        $scanner->next();
        if (!$scanner->get()->isEOF()) {
            parent::simpleException($scanner, ['<EOF>']);
        }
        return new Program($heading, $block);
    }

    static public function firstTokens()
    {
        return ['<PROGRAM HEADING>'];
    }

    public function __construct($heading, $block)
    {
        $this->heading = $heading;
        $this->block = $block;
    }

    public function toIdArray($id = 0)
    {
        $node = [
            "id"       => $id,
            "name"     => "Program",
            "children" => []
        ];
        $id++;
        $heading = $this->heading->toIdArray($id);
        $block = $this->block->toIdArray($id);
        array_push($node['children'], $heading);
        array_push($node['children'], $block);
        return $node;
    }
}