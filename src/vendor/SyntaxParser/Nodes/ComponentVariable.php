<?php

namespace vendor\SyntaxParser\Nodes;

use vendor\SyntaxParser\Nodes\Node;
use vendor\TokenParser\Scanner;
use vendor\Exception\SyntaxException;

class ComponentVariable extends Node
{
    private $record = null;
    private $designator = null;

    static public function parse($scanner, $record)
    {
        if (!$scanner->get()->isIdentifier()) {
            parent::simpleException($scanner, ['<FIELD-DESIGNATOR>']);
        }
        $designator = $scanner->get();
        $scanner->next();
        return new ComponentVariable($record, $designator);
    }


    static public function firstTokens()
    {
        return ['<FIELD-DESIGNATOR>'];
    }

    public function __construct($record, $designator)
    {
        $this->record = $record;
        $this->designator = $designator;
    }

    public function toArray()
    {
        return [
            "ComponentVariable" => [
                "RecordVariable" => $this->record->toArray(),
                "FieldDesignator" => $this->designator->getValue()
            ]
        ];
    }

    public function toIdArray(&$id)
    {
        $record = $this->record->toIdArray($id);
        $des = [
            "id" => $id,
            "name" => "designator=" . $this->designator->getValue()
        ];
        $id++;
        $node = [
            "id"       => $id,
            "name"     => "ComponentVariable",
            "children" => [$record, $des]
        ];
        $id++;
        return $node;
    }
}
