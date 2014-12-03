<?php

namespace vendor\SyntaxParser\Nodes;

use vendor\SyntaxParser\Nodes\Node;
use vendor\TokenParser\Scanner;
use vendor\Exception\SyntaxException;

class IndexedVariable extends Node
{
    private $array = null;
    private $indexes = null;

    static public function parse($scanner, $array)
    {
        $indexes = [];
        // parent::eofLessNext($scanner, ['<INDEX-EXPRESSION>']);
        $continue = true;
        while (!$scanner->get()->isOperator(']')) {
            if (!$continue) {
                echo "here";
                parent::simpleException($scanner, ["<OPERATOR ']'>"]);
            }
            $indexes[] = Expression::parse($scanner);
            $continue = $scanner->get()->isOperator(',');
            $expected = $continue ? ['<INDEX-EXPRESSION>'] : ["<OPERATOR ']'>"];
            if ($scanner->get()->isOperator(']')) {
                break;
            }
            parent::eofLessNext($scanner, $expected);
        }
        if ($continue) {
            parent::simpleException($scanner, ['<INDEX-EXPRESSION>']);
        }
        $scanner->next();
        return new IndexedVariable($array, $indexes);
    }


    static public function firstTokens()
    {
        return ['<INDEX-EXPRESSION>'];
    }

    public function __construct($array, $indexes)
    {
        $this->array = $array;
        $this->indexes = $indexes;
    }

    public function toArray()
    {
        return [
            "IndexedVariable" => [
                "ArrayVariable" => $this->array->toArray(),
                "Indexes" => array_map(function($expr) {
                    return ["IndexExpression" => $expr->toArray()];
                }, $this->indexes)
            ]
        ];
    }

    public function toIdArray(&$id)
    {
        // echo json_encode($this->toArray());
        // exit;
        $arrayVariable = $this->array->toIdArray($id);
        $indexes = [];
        foreach($this->indexes as &$index) {
            array_push(
                $indexes,(
                    $index instanceof Token ?
                    [
                        "id" => $id,
                        "name" => $index->getValue()
                    ] :
                    $index->toIdArray($id)
                )
            );
        }
        // $indexes = $indexes[0];
        $node = [
            "id"       => $id,
            "name"     => "IndexedVariable",
            "children" => [
                $arrayVariable,
                [
                    "id" => ++$id,
                    "name" => "indexes",
                    "children" => $indexes
                ]
            ]
        ];
        $id++;
        return $node;
    }
}
