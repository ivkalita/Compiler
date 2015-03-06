<?php

namespace vendor\SyntaxParser\Nodes;

use vendor\SyntaxParser\Nodes\Node;
use vendor\TokenParser\Scanner;
use vendor\Exception\SyntaxException;

class FPPart extends Node
{
    private $fps = [];

    public function __construct($scanner, $_symTable)
    {
        while ($scanner->get()->isKeyword('procedure') || $scanner->get()->isKeyword('function')) {
            array_push(
                $this->fps,
                (
                    $scanner->get()->isKeyword('procedure')
                        ? Proc::smartParse($scanner, $_symTable)
                        : Func::smartParse($scanner, $_symTable)
                )
            );
            parent::semicolonPass($scanner);
        }
        if (empty($this->fps)) {
            parent::simpleException($scanner, ['<IDENTIFIER>']);
        }
    }

    public function toIdArray(&$id)
    {
        $node = [
            "id"       => $id++,
            "name"     => "Functions And Procedures",
            "children" => []
        ];
        foreach($this->fps as $fp) {
            array_push($node["children"], $fp->toIdArray($id));
        }
        return $node;
    }
}
