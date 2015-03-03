<?php

namespace vendor\SyntaxParser\Nodes;

use vendor\SyntaxParser\Nodes\Node;
use vendor\TokenParser\Scanner;
use vendor\Exception\SyntaxException;
use vendor\SemanticParser\Nodes\SymTable;

class Program extends Node
{
    private $heading;
    private $block;
    public $symTable;

    public function __construct($scanner)
    {
        $scanner->next();
        $this->symTable = new SymTable(null);
        $this->heading = new ProgramHeading($scanner);
        parent::semicolonPass($scanner);
        $this->block = new Block($scanner, $this->symTable);
        if (!$scanner->get()->isOperator('.')) {
            parent::simpleException($scanner, ["<OPERATOR '.'>"]);
        }
        $scanner->next();
        if (!$scanner->get()->isEOF()) {
            parent::simpleException($scanner, ['<EOF>']);
        }
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