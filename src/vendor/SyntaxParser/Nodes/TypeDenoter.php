<?php

namespace vendor\SyntaxParser\Nodes;

use vendor\SyntaxParser\Nodes\Node;
use vendor\TokenParser\Scanner;
use vendor\Exception\SyntaxException;

class TypeDenoter extends Node
{
    private $identifier = null;
    private $type = null;
    //Type types, lol


    public function __construct($scanner, $_symTable)
    {
        $typeSwitch = [
            self::$NEW_STRUCT => 'RecordTypeDec',
            self::$NEW_POINTER => 'PointerTypeDec',
            self::$NEW_SUBRANGE => 'SubRangeTypeDec'
        ];
        $type = self::guessType($scanner);
        if ($type == self::$IDENTIFIER) {
            $identifier = $scanner->get();
            $scanner->next();
        } else {
            $identifier = new $typeSwitch[$type]($scanner, $_symTable);
        }
    }

    static private function guessType($scanner)
    {
        if ($scanner->get()->isIdentifier()) {
            return self::$IDENTIFIER;
        } else if ($scanner->get()->isKeyword('record')) {
            return self::$NEW_STRUCT;
        } else if ($scanner->get()->isConst()) {
            return self::$NEW_SUBRANGE;
        } else if ($scanner->get()->isOperator('^')) {
            return self::$NEW_POINTER;
        } else {
            parent::simpleException($scanner, self::firstTokens());
        }
    }
}
