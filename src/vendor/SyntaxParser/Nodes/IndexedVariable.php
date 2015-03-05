<?php

namespace vendor\SyntaxParser\Nodes;

use vendor\SyntaxParser\Nodes\Node;
use vendor\TokenParser\Scanner;
use vendor\Exception\SyntaxException;
use vendor\Exception\SemanticException;

class IndexedVariable extends Node
{
    private $array = null;
    private $indexes = null;
    public $symType = null;

    public function __construct($scanner, $_symTable, $array)
    {
        $arrayType = $array->variable->symType;
        if (!is_a($arrayType, 'vendor\SemanticParser\Nodes\SymArrayType')) {
            SemanticException::varAccessTypeMismatch($scanner, $arrayType, 'array');
        }
        $this->array = $array;
        $this->indexes = [];
        $continue = true;
        $idx = 0;
        while (!$scanner->get()->isOperator(']')) {
            if (!$continue) {
                parent::simpleException($scanner, ["<OPERATOR ']'>"]);
            }
            $this->indexes[$idx] = new Expression($scanner, $_symTable);
            if (!$arrayType->checkIndex($this->indexes[$idx], $idx, $_symTable)) {
                SemanticException::raw($scanner, "Array index type mismatch");
            }
            $idx++;
            $continue = $scanner->get()->isOperator(',');
            if ($scanner->get()->isOperator(']')) {
                break;
            }
            $scanner->next();
        }
        if ($idx != count($arrayType->dimensions)) {
            SemanticException::raw($scanner, "Array should be dereferenced completely");
        }
        $this->symType = $arrayType->type;
        if ($continue) {
            parent::simpleException($scanner, ['<INDEX-EXPRESSION>']);
        }
        $scanner->next();
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
                ],
                [
                    "id" => ++$id,
                    "name" => "type={$this->symType->identifier}",
                ]
            ]
        ];
        $id++;
        return $node;
    }
}
