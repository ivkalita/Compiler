<?php

namespace vendor\SyntaxParser\Nodes;

use vendor\SyntaxParser\Nodes\Node;
use vendor\TokenParser\Scanner;
use vendor\Exception\SyntaxException;

class ProgramHeading extends Node
{
    private $name;

    public function __construct($scanner)
    {
        if (!$scanner->get()->isKeyword('program')) {
            parent::simpleException($scanner, ["<KEYWORD 'program'"]);
        }
        $this->name = $scanner->nget();
        if (!$this->name->isIdentifier()) {
            parent::simpleException($scanner, ['<IDENTIFIER>']); 
        }                
        $scanner->next();
    }

    public function toIdArray(&$id)
    {
        $node = [
            "id"       => $id,
            "name"     => "Heading",
            "children" => [
                [
                    "id"   => ++$id,
                    "name" => $this->name->getValue()
                ]
            ]
        ];
        $id++;
        return $node;
    }


}
