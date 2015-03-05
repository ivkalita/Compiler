<?php

namespace vendor\SyntaxParser\Nodes;

use vendor\SyntaxParser\Nodes\Node;
use vendor\TokenParser\Scanner;
use vendor\Exception\SyntaxException;
use vendor\Exception\SemanticException;

class ComponentVariable extends Node
{
    private $record = null;
    private $designator = null;
    public $symType = null;

    public function __construct($scanner, $_symTable, $record)
    {
        $recType = $record->variable->symType;
        if (!is_a($recType, 'vendor\SemanticParser\Nodes\SymRecordType')) {
            SemanticException::varAccessTypeMismatch($scanner, $recType, 'record');
        }
        if (!$scanner->get()->isIdentifier()) {
            parent::simpleException($scanner, ['<FIELD-DESIGNATOR>']);
        }
        $identifier = $scanner->get()->getValue();
        $designatorType = $recType->getFieldType($identifier);
        if ($designatorType == null) {
            SemanticException::undeclared($scanner, $identifier->getValue());
        }
        $this->symType = $designatorType;
        $this->record = $record;
        $this->designator = $scanner->get();
        $scanner->next();
    }

    public function toIdArray(&$id)
    {
        $record = $this->record->toIdArray($id);
        $des = [
            "id" => $id++,
            "name" => "designator=" . $this->designator->getValue()
        ];
        $type = [
            "id" => $id++,
            "name" => "type=" . $this->symType->identifier
        ];
        $node = [
            "id"       => $id++,
            "name"     => "ComponentVariable",
            "children" => [$record, $des, $type]
        ];
        return $node;
    }
}
