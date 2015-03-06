<?php

namespace vendor\SyntaxParser\Nodes;

use vendor\SyntaxParser\Nodes\Node;
use vendor\Exception\SemanticException;
use vendor\Utility\Flags;

class ControlStatement extends Node
{
    public $identifier = null;

    public function __construct($scanner, $_symTable)
    {
        if (!$scanner->get()->isKeyword()) {
            parent::simpleException($scanner, ['<KEYWORD>']);
        }
        $identifier = $scanner->get();
        $scanner->next();
        $valid = false;
        switch ($identifier->getValue()) {
            case 'break':
                $valid = Flags::$switchDepth > 0;
            case 'continue':
                $valid |= Flags::$loopDepth > 0;
                break;
            case 'exit':
                $valid |= Flags::$funcDepth > 0;
                break;
            default:
                throw new \Exception("Control keyword " . $identifier->getValue() . " not implemented yet");
        }
        if (!$valid) {
            SemanticException::illegalControlStatement($scanner, $identifier->getValue());
        }
        $this->identifier = $identifier;
    }

    public function toIdArray(&$id)
    {
        $node = [
            "id" => $id++,
            "name" => "ControlStatement = " . $this->identifier->getValue(),
        ];
        return $node;
    }
}
