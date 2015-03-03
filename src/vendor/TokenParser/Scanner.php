<?php
    namespace vendor\TokenParser;

    use vendor\Utility\TextPoint;
    use vendor\Exception\TokenException;
    use vendor\Exception\CompilerException;

    class Scanner
    {
        const INIT              = -1;
        const ERROR              = -2;
        const TERMINAL           = -3;
        const KEYWORD_IDENT      = 0;
        const SPECIAL_1          = 1;
        const ALMOST_REAL        = 2;
        const REAL               = 3;
        const ALMOST_REAL_E      = 4;
        const ALMOST_REAL_E_SIGN = 5;
        const REAL_E             = 6;
        const SIGN               = 7;
        const IDENT_NOT_FINAL    = 8;
        const DIGIT_SEQUENCE     = 9;
        const STR_NOT_FINAL      = 10;
        const APOS_STR_STR_FINAL = 11;
        const MULTI_COMMENT      = 12;
        const ONE_COMMENT        = 13;
        static public $EOF = "EndOfFile";
        static public $SEPARATORS = [
            'plus'        => '+',
            'minus'       => '-',
            'star'        => '*',
            'slash'       => '/',
            'eq'          => '=',
            'greater'     => '>',
            'less'        => '<',
            'l_s_bracket' => '[',
            'r_s_bracket' => ']',
            'dot'         => '.',
            'comma'       => ',',
            'colon'       => ':',
            'up'          => '^',
            'l_r_bracket' => '(',
            'r_r_bracket' => ')',
            'semicolon'   => ';',
            'at'          => '@'
        ];
        static public $KEYWORDS = [
            'and',
            'array',
            'begin',
            'case',
            'const',
            'div',
            'do',
            'downto',
            'else',
            'end',
            'file',
            'for',
            'forward',
            'function',
            'goto',
            'if',
            'in',
            'label',
            'mod',
            'nil',
            'not',
            'of',
            'or',
            'packed',
            'procedure',
            'program',
            'record',
            'repeat',
            'set',
            'then',
            'to',
            'type',
            'until',
            'var',
            'while',
            'with'
        ];

        static public $MULTI_SEPARATORS = [
            '<' => ['>', '='],
            '>' => ['='],
            ':' => ['='],
            '.' => ['.', ')'],
            '(' => ['*'],
            '*' => [')'],
            '/' => ['/'],
            '(' => ['.']
        ];

        private $text;
        private $currentToken;
        private $point;

        private function isTerminal($char)
        {
            return self::isSpace($char) || in_array($char, self::$SEPARATORS) || $this->EOF();
        }

        private function isSpecialOne($char)
        {
            return in_array($char, self::$SEPARATORS) && !in_array($char, array_keys(self::$MULTI_SEPARATORS));
        }

        private function isSpecialMulti($char)
        {
            return in_array($char, array_keys(self::$MULTI_SEPARATORS));
        }

        private function isSpecialMultiEnd($start, $end)
        {
            return in_array($end, self::$MULTI_SEPARATORS[$start]);
        }

        private function isSpace($char)
        {
            return in_array($char, [" ", "\n", "\t"]);
        }

        private function nextChar($dontMove = false, $native = false)
        {
            if ($this->EOF()) {
                return self::$EOF;
            }
            $char = $this->text[$this->point->idx + 1];
            if (!$native) {
                $char = mb_strtolower($char);
            }
            if ($dontMove) {
                return $char;
            }
            switch ($char) {
                case "\n":
                    $this->point->y++;
                    $this->point->x = 0;
                    break;
                case "\t":
                    $this->point->x += 4;
//                    $this->point->idx++;
                    break;
                default:
                    $this->point->x++;
                    break;
            }
            $this->point->idx++;
            return $char;
        }

        private function lookup($delta)
        {
            $id = $this->point->idx + $delta;
            if ($id >= strlen($this->text)) {
                return self::$EOF;
            } else {
                return $this->text[$id];
            }
        }

        private function EOF()
        {
            return (strlen($this->text)) <= $this->point->idx + 1;
        }


        public function __construct($text)
        {
            $this->point = new TextPoint();
            $this->text = $text;
            $this->currentToken = NULL;
        }

        public function get()
        {
            return $this->currentToken;
        }

        public function nget()
        {
            self::next();
            return self::get();
        }
        public function next()
        {
            $this->currentToken = NULL;
//            $this->chop();
            $value = '';
            $text = '';
            $flags = [
                'signed' => false
            ];
            $state = self::INIT;
            while ($state != self::TERMINAL) {
                $c = $this->nextChar(true);
                switch ($state) {
                    case self::INIT:
                        if ($this->EOF()) {
                            $this->currentToken = new Token(Token::EOF, '', '', $this->point);
                            return false;
                        }
                        if (ctype_alpha($c)) {
                            $value .= $this->nextChar();
                            $text .= $c;
                            $state = self::IDENT_NOT_FINAL;
                            break;
                        }
                        switch ($c) {
                            case ';':
                                $this->nextChar();
                                $text .= $c;
                                $this->currentToken = new Token(Token::DELIMITER, $c, $text, $this->point);
                                return true;
                            case '+':
                            case '-':
                                $value .= $this->nextChar();
                                $text .= $c;
                                $state = self::SIGN;
                                $this->currentToken = new Token(Token::OPERATOR, $c, $text, $this->point);
                                return true;
                            case "'":
                                $text .= $this->nextChar();
                                $state = self::STR_NOT_FINAL;
                                break;
                            case '{':
                                $this->nextChar();
                                $state = self::MULTI_COMMENT;
                                break;
                            default:
                                if (self::isSpace($c)) {
                                    $this->nextChar();
                                    break;
                                }
                                if (self::isSpecialOne($c)) {
                                    $text .= $this->nextChar();
                                    $this->currentToken = new Token(Token::OPERATOR, $c, $text, $this->point);
                                    return true;
                                }
                                if (self::isSpecialMulti($c)) {
                                    $text .= $c;
                                    $value .= $this->nextChar();
                                    $state = self::SPECIAL_1;
                                    break;
                                }
                                if (ctype_digit($c)) {
                                    $text .= $c;
                                    $value .= $this->nextChar();
                                    $state = self::DIGIT_SEQUENCE;
                                    break;
                                }
                                throw new CompilerException($this->point, $text);
                        }
                        break;
                    case self::MULTI_COMMENT:
                        if ($this->EOF()) {
                            throw new TokenException($this->point, $text, ["<EOC>"], self::$EOF);
                        }
                        $text .= $this->nextChar();
                        if ($value == '*' && $c == ')' || $c == '}') {
                            $text = '';
                            $value = '';
                            $state = self::INIT;
                            break;
                        }
                        $value = $c == '*' ?: '';
                        break;
                    case self::STR_NOT_FINAL:
                        $c = $this->nextChar(false, true);
                        if ($this->EOF()) {
                            throw new TokenException($this->point, $text, ["<EOS>"], self::$EOF);
                        }
                        $text .= $c;
                        if ($c == '\'') {
                            $state = self::APOS_STR_STR_FINAL;
                            break;
                        }
                        $value .= $c;
                        $state = self::STR_NOT_FINAL;
                        break;
                    case self::APOS_STR_STR_FINAL:
                        if (self::isTerminal($c)) {
                            $this->currentToken = new Token(Token::CHARACTER_STRING, $value, $text, $this->point);
                            return true;
                        }
                        $text .= $c;
                        if ($c == '\'') {
                            $value .= $this->nextChar(false, true);
                            $state = self::STR_NOT_FINAL;
                            break;
                        }
                        throw new TokenException($this->point, $text, ["<'>", "<TERMINAL>"], $c);
                        break;
                    case self::SIGN:
                        if ($this->EOF()) {
                            $this->currentToken = new Token(Token::OPERATOR, $value, $text, $this->point);
                            return true;
                        }
                        $text .= $c;
                        if (ctype_digit($c)) {
                            $flags['signed'] = true;
                            $value .= $this->nextChar();
                            $state = self::DIGIT_SEQUENCE;
                            break;
                        }
                        $this->currentToken = new Token(Token::OPERATOR, $value, $text, $this->point);
                        return true;
                    case self::DIGIT_SEQUENCE:
                        // if ($this->lookup(2) == '..') {
                        //     $this->currentToken = new Token(
                        //         $flags['signed']
                        //             ? Token::SIGNED_INTEGER
                        //             : Token::UNSIGNED_INTEGER,
                        //         $value,
                        //         $text,
                        //         $this->point
                        //     );
                        //     return true;
                        // }
                        if ($c == '.') {
                            if ($this->lookup(2) == '.') {
                                $this->currentToken = new Token(
                                    $flags['signed']
                                        ? Token::SIGNED_INTEGER
                                        : Token::UNSIGNED_INTEGER,
                                    $value,
                                    $text,
                                    $this->point
                                );
                                return true;
                            }
                            $text .= $c;
                            $value .= $this->nextChar();
                            $state = self::ALMOST_REAL;
                            break;
                        }
                        if (self::isTerminal($c)) {
                            $this->currentToken = new Token(
                                $flags['signed']
                                    ? Token::SIGNED_INTEGER
                                    : Token::UNSIGNED_INTEGER,
                                $value,
                                $text,
                                $this->point
                            );
                            return true;
                        }
                        $text .= $c;
                        if (ctype_digit($c)) {
                            $value .= $this->nextChar();
                            $state = self::DIGIT_SEQUENCE;
                            break;
                        }
                        if ($c == 'e') {
                            $value .= $this->nextChar();
                            $state = self::ALMOST_REAL_E;
                            break;
                        }
                        throw new TokenException($this->point, $text, ["<DIGIT>", "<TERMINAL>", "<.>", "<e>"], $c);
                    case self::ALMOST_REAL:
                        $text .= $c;
                        if (ctype_digit($c)) {
                            $value .= $this->nextChar();
                            $state = self::REAL;
                            break;
                        }
                        // if ($c == '.') {
                        //     $value = '..';
                        //     $this->currentToken = new Token(
                        //         Token::OPERATOR,
                        //         '..',
                        //     );
                        // }
                        throw new TokenException($this->point, $text, ["<DIGIT>"], $c);
                    case self::REAL:
                        if (self::isTerminal($c)) {
                            $this->currentToken = new Token(
                                $flags['signed']
                                    ? Token::SIGNED_REAL
                                    : Token::UNSIGNED_REAL,
                                $value,
                                $text,
                                $this->point
                            );
                            return true;
                        }
                        $text .= $c;
                        if (ctype_digit($c)) {
                            $value .= $this->nextChar();
                            $state = self::REAL;
                            break;
                        }
                        if ($c == 'e') {
                            $value .= $this->nextChar();
                            $state = self::ALMOST_REAL_E;
                            break;
                        }
                        throw new TokenException($this->point, $text, ["<DIGIT>", "<TERMINAL>", "<e>"], $c);
                    case self::ALMOST_REAL_E:
                        $text .= $c;
                        if ($c == '+' || $c == '-') {
                            $value .= $this->nextChar();
                            $state = self::ALMOST_REAL_E_SIGN;
                            break;
                        }
                        if (ctype_digit($c)) {
                            $value .= $this->nextChar();
                            $state = self::REAL_E;
                            break;
                        }
                        throw new TokenException($this->point, $text, ["<DIGIT>", "<SIGN>"], $c);
                    case self::REAL_E:
                        if (self::isTerminal($c)) {
                            $this->currentToken = new Token(
                                $flags['signed']
                                    ? Token::SIGNED_REAL_E
                                    : Token::UNSIGNED_REAL_E,
                                $value,
                                $text,
                                $this->point
                            );
                            return true;
                        }
                        $text .= $c;
                        if (ctype_digit($c)) {
                            $value .= $this->nextChar();
                            $state = self::REAL_E;
                            break;
                        }
                        throw new TokenException($this->point, $text, ["<DIGIT>", "<TERMINAL>"], $c);
                    case self::ALMOST_REAL_E_SIGN:
                        $text .= $c;
                        if (ctype_digit($c)) {
                            $value .= $this->nextChar();
                            $state = self::REAL_E;
                            break;
                        }
                        throw new TokenException($this->point, $text, ["<DIGIT>"], $c);
                    case self::SPECIAL_1:
                        if (self::isSpecialMultiEnd($value, $c)) {
                            $text .= $c;
                            $value .= $this->nextChar();
                            if ($value == '(*') {
                                $state = self::MULTI_COMMENT;
                                break;
                            }
                            if ($value == '//') {
                                $state = self::ONE_COMMENT;
                                break;
                            }
                            $this->currentToken = new Token(Token::OPERATOR, $value, $text, $this->point);
                            return true;
                        }
                        $this->currentToken = new Token(Token::OPERATOR, $value, $text, $this->point);
                        return true;
                    case self::IDENT_NOT_FINAL:
                        if (self::isTerminal($c)) {
                            $this->currentToken = new Token(
                                in_array($value, self::$KEYWORDS)
                                    ? Token::KEYWORD
                                    : Token::IDENTIFIER,
                                $value, $text, $this->point
                            );
                            return true;
                        }
                        $text .= $c;
                        if (ctype_alnum($c)) {
                            $value .= $this->nextChar();
                            $state = self::IDENT_NOT_FINAL;
                            break;
                        }
                        throw new TokenException($this->point, $text, ["<ALNUM>", "<TERMINAL>"], $c);
                    case self::ONE_COMMENT:
                        if ($this->EOF()) {
                            return false;
                        }
                        $this->nextChar();
                        if ($c == "\n") {
                            $state = self::INIT;
                            $text = $value = '';
                            break;
                        }
                        break;
                    default:
                        throw new CompilerException($this->point, $text);
                }
            }
            return false;
        }
    }
