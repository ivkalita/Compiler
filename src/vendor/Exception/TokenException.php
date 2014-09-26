<?php
/**
 * Created by IntelliJ IDEA.
 * User: kaduev
 * Date: 26.09.14
 * Time: 11:19
 */
    namespace vendor\Exception;

    class TokenException extends CompilerException
    {
        private $expected;
        private $found;

        public function __construct($point, $text, $expected, $found)
        {
            parent::__construct($point, $text);
            $this->module = parent::TOKEN_EXCEPTION;
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
