<?php

namespace vendor\SyntaxParser\Nodes;

use vendor\SyntaxParser\Nodes\Node;
use vendor\TokenParser\Scanner;
use vendor\Exception\SyntaxException;

class Statement extends Node
{
    private $statement = null;
    //null means empty-statement

    static public function parse($scanner)
    {
        $statement = null;
        while (!$scanner->get()->isSemicolon()) {
            $statement = AssignmentStatement::parse($scanner);
        }
        $scanner->next();
        return new Statement($statement);
    }

    public function __construct($statement)
    {
        $this->statement = $statement;
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
