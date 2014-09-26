<?php
    namespace vendor\TokenParser;

    class Token
    {
        const KEYWORD           = "WORD-SYMBOL";
        const IDENTIFIER        = "IDENTIFIER";
        const OPERATOR          = "OPERATOR";
        const DELIMITER         = "DELIMITER";
        const UNSIGNED_NUMBER   = "UNSIGNED NUMBER";
        const UNSIGNED_INTEGER  = "UNSIGNED INTEGER";
        const UNSIGNED_REAL     = "UNSIGNED REAL";
        const UNSIGNED_REAL_E   = "UNSIGNED REAL E";
        const SIGNED_NUMBER     = "SIGNED NUMBER";
        const SIGNED_INTEGER    = "SIGNED INTEGER";
        const SIGNED_REAL       = "SIGNED REAL";
        const SIGNED_REAL_E     = "SIGNED REAL E";
        const CHARACTER_STRING  = "CHARACTER STRING";
        protected $value;
        protected $point;
        protected $text;

        public function __construct($type, $value, $text, $point)
        {
            $this->type = $type;
            $this->value = $value;
            $this->text = $text;
            $this->point = clone $point;
            $this->point->x = $this->point->x - strlen($text) + 1;
        }

        public function getMessage()
        {
            return $this->value;
        }

        public function getStr()
        {
            $max_width = 18;
            $str = "|" . $this->text;
            for ($i = strlen($this->text); $i <= max($max_width, strlen($this->text)); $i++) {
                $str .= " ";
            }
            $str .= "|" . $this->type;
            for ($i = strlen($this->type); $i <= max($max_width, strlen($this->type)); $i++) {
                $str .= " ";
            }
            $str .= "|" . $this->value;
            for ($i = strlen($this->value); $i <= max($max_width, strlen($this->value)); $i++) {
                $str .= " ";
            }
            $pos = "{$this->point->y}:{$this->point->x}";
            $str .= "|" . $pos;
            for ($i = strlen($pos);  $i <= max($max_width, strlen($pos)); $i++) {
                $str .= " ";
            }
            $str .= "|\n";
            return $str;
        }
    }