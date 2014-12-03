<?php

namespace vendor\SyntaxParser\Nodes;

use vendor\SyntaxParser\Nodes\Node;
use vendor\TokenParser\Scanner;
use vendor\Exception\SyntaxException;

class ProgramHeading extends Node
{
    private $name; //identifier

    static public function parse($scanner)
    {
        if (!$scanner->get()->isKeyword('program')) {
            parent::simpleException($scanner, ["<KEYWORD 'program'"]);
        }
        $name = $scanner->nget();
        if (!$name->isIdentifier()) {
            parent::simpleException($scanner, ['<IDENTIFIER>']); 
        }                
        $scanner->next();
        return new ProgramHeading($name);
    }

    public function __construct($name)
    {
        $this->name = $name;
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
