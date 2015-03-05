<?php

namespace vendor\SyntaxParser\Nodes;

use vendor\SyntaxParser\Nodes\Node;
use vendor\TokenParser\Scanner;
use vendor\Exception\SyntaxException;

class Statement extends Node
{
    private $statement = null;
    //null means empty-statement

    public function __construct($scanner, $_symTable)
    {
        $this->statement = null;
        while (!$scanner->get()->isSemicolon()) {
            $this->statement = new AssignmentStatement($scanner, $_symTable, null);
        }
        $scanner->next();
    }

    public function toIdArray(&$id)
    {
        if (!$this->statement) {
            $node = [
                "id" => $id,
                "name" => 'empty-statement'
            ];
            $id++;
            return $node;
        }
        return $this->statement->toIdArray($id);
    }
}
