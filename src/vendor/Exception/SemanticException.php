<?php

    namespace vendor\Exception;

    use vendor\TokenParser\Token;
    use vendor\TokenParser\Scanner;
    use vendor\Utility\TextPoint;

    class SemanticException extends CompilerException
    {
        private $expected;
        private $found;

        public function __construct($point, $text, $expected, $found)
        {
            parent::__construct($point, $text);
            $this->module = parent::SEMANTIC_EXCEPTION;
            $this->expected = $expected;
            $this->found = $found;
        }

        public function __toString()
        {
            $base = parent::__toString();
            $expected = implode(" or ", $this->expected);
            if (!trim($expected) == '') {
                $base .= "Expected: $expected\n";
            }
            if (!trim($this->found) == '') {
                $base .= "Found:{$this->found}\n";
            }
            return $base;
        }

        static public function expected($scanner, $expected)
        {
            $point = new TextPoint();
            throw new SemanticException(
                $scanner->get()->point,
                $scanner->get()->text,
                $expected,
                $scanner->get()->getValue()
            );
        }

        static public function undeclared($scanner, $identifier)
        {
            self::raw($scanner, "Undeclared identifier <$identifier>");
        }

        static public function varAccessTypeMismatch($scanner, $symbol, $expectedType)
        {
            self::raw($scanner, "Expected instance of <$expectedType>, found instance of <{$symbol->identifier}>");
        }

        static public function invalidTypeCast($src, $dst)
        {
            self::raw(null, "Invalid typecast <{$src->identifier}> to <{$dst->identifier}>");

        }

        static public function raw($scanner, $msg)
        {
            throw new SemanticException(
                $scanner ? $scanner->get()->point : new TextPoint(),
                $msg,
                [],
                ''
            );
        }

        static public function illegalControlStatement($scanner, $identifier)
        {
            self::raw($scanner, "Illegal usage of control statement {$identifier}");
        }

        static public function invalidArgCount($identifier)
        {
            self::raw(null, "Invalid argument count for calling <$identifier>");
        }

        static public function redeclared($identifier)
        {
            throw new SemanticException(
                new TextPoint(),
                "Identifier redeclared: <$identifier>",
                [],
                ''
            );
        }
    }
