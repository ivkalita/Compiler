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
        const BOOLEAN_CONST     = "BOOLEAN CONST";
        const EOF               = "END OF FILE";
        protected $value;
        public $point;
        public $text;
        public $type;

        public function __construct($type, $value, $text, $point)
        {
            $this->type = $type;
            $this->value = $value;
            $this->text = $text;
            $this->point = clone $point;
            $this->point->x = $this->point->x - strlen($text) + 1;
        }

        public function setValue($value)
        {
            $this->value = $value;
            return $this;
        }

        public function getMessage()
        {
            return $this->value;
        }

        public function getValue()
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

        public function __toString()
        {
            return $this->type;
        }

        public function isSigned()
        {
            return
                !is_null($this->type) &&
                in_array($this->type, [self::SIGNED_INTEGER,
                                       self::SIGNED_REAL,
                                       self::SIGNED_REAL_E]);
        }

        public function isAdditive()
        {
            return
                $this->isSigned() ||
                (
                    $this->type === Token::OPERATOR &&
                    in_array($this->value, ['-', '+'])
                ) ||
                (
                    $this->type === Token::KEYWORD &&
                    in_array($this->value, ['or', 'xor'])
                );
        }

        public function isRelational()
        {
            return
                (
                    $this->isOperator() &&
                    in_array($this->getValue(), ['=', '<>', '>', '<', '<=', '>='])
                ) ||
                (
                    $this->isKeyword('in')
                );
        }

        public function getOperation()
        {
            if ($this->isSigned()) {
                return $this->value[0];
            } else {
                throw new \Exception("Fatal error! Token::getOperation with value <{$this->value}>.");
            }
        }

        public function isFinal()
        {
            return $this->type === Token::DELIMITER || $this->type === Token::EOF;
        }

        public function isRBracket()
        {
            return $this->type === Token::OPERATOR && $this->value == ')';
        }

        public function isLBracket()
        {
            return $this->type === Token::OPERATOR && $this->value == '(';
        }

        public function isMultOperation()
        {
            $isGeneralMult = $this->type === Token::OPERATOR &&
                in_array($this->value, ['*', '/']);
            $isIntegerMult = $this->type === Token::KEYWORD &&
                in_array($this->value, ['div', 'mod', 'and']);
            return $isGeneralMult || $isIntegerMult;
        }

        public function isUnSignedConst()
        {
            return
                in_array(
                    $this->type,
                    [
                        Token::UNSIGNED_INTEGER,
                        Token::UNSIGNED_REAL,
                        Token::UNSIGNED_REAL_E,
                        Token::BOOLEAN_CONST
                    ]
                );

        }

        public function isInteger()
        {
            return
                in_array(
                    $this->type,
                    [
                        Token::UNSIGNED_INTEGER,
                        Token::SIGNED_INTEGER
                    ]
                );
        }

        public function isConst()
        {
            return
                in_array(
                    $this->type,
                    [
                        Token::SIGNED_INTEGER,
                        Token::SIGNED_REAL,
                        Token::SIGNED_REAL_E,
                        Token::UNSIGNED_INTEGER,
                        Token::UNSIGNED_REAL,
                        Token::UNSIGNED_REAL_E,
                        Token::CHARACTER_STRING
                    ]
                );
        }

        public function isKeyword($val = '')
        {
            return ($this->type == Token::KEYWORD) &&
                   ($val == '' ? true : $this->value == $val);
        }

        public function isIdentifier($val = '')
        {
            return ($this->type == Token::IDENTIFIER) &&
                   ($val == '' ? true : $this->value == $val); 
        }

        public function isOperator($val = '')
        {
            return ($this->type == Token::OPERATOR) &&
                   ($val == '' ? true : $this->value == $val); 
        }

        public function isSemicolon()
        {
            return ($this->type == Token::DELIMITER);
        }

        public function isEq($value)
        {
            return ($this->value == $value);
        }

        public function isEOF()
        {
            return ($this->type == Token::EOF);
        }


    }