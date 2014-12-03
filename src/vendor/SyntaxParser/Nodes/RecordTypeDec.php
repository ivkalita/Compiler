<?php

namespace vendor\SyntaxParser\Nodes;

use vendor\SyntaxParser\Nodes\Node;
use vendor\TokenParser\Scanner;
use vendor\Exception\SyntaxException;

class RecordTypeDec extends Node
{
    private $identifier = null;
    private $type = null;
    //Type types, lol
    static private $IDENTIFIER = 0;
    static private $NEW_STRUCT = 1;
    static private $NEW_POINTER = 2;
    static private $NEW_SUBRANGE = 3;

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
    static public function parse($scanner)
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
            $identifier = $typeSwitch[$type]::parse($scanner);
        }
        return new TypeDenoter($identifier, $type);
    }

    static public function firstTokens()
    {
        return array_merge(
            RecordTypeDec::firstTokens(),
            PointerTypeDec::firstTokens(),
            SubRangeTypeDec::firstTokens()
            ['<IDENTIFIER>']
        );
    }

    public function __construct($identifier, $denoter)
    {
        $this->identifier = $identifier;
        $this->denoter = $denoter;
    }
}
