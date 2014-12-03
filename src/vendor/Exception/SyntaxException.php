<?php
/**
 * Created by IntelliJ IDEA.
 * User: kaduev
 * Date: 11.10.14
 * Time: 7:43
 */
    namespace vendor\Exception;

    use vendor\TokenParser\Token;
    class SyntaxException extends CompilerException
    {
        private $expected;
        private $found;

        public function __construct($point, $text, $expected, $found)
        {
            parent::__construct($point, $text);
            $this->module = parent::SYNTAX_EXCEPTION;
            $this->expected = $expected;
            $this->found = $found;
        }

        public function __toString()
        {
            $base = parent::__toString();
            $expected = implode(" or ", $this->expected);
            $base .= "Expected: $expected\nFound:{$this->found}";
            return $base;
        }
    }
