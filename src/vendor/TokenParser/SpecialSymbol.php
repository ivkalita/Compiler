<?php
/**
 * Created by IntelliJ IDEA.
 * User: kaduev
 * Date: 20.09.14
 * Time: 12:25
 */
    namespace vendor\TokenParser;

    class SpecialSymbol
    {
        const OPERATOR = 'OPERATOR';
        const DELIMITER = 'delimiter';
        const WORD_SYMBOL = 'word-symbol';
        static private $symbols = [
            Self::OPERATOR => [
                'single' => [
                    'plus'            => '+',
                    'minus'           => '-',
                    'star'            => '*',
                    'slash'           => '/',
                    'eq'              => '=',
                    'greater'         => '>',
                    'less'            => '<',
                    'l_s_bracket'     => '[',
                    'r_s_bracket'     => ']',
                    'dot'             => '.',
                    'comma'           => ',',
                    'colon'           => ':',
                    'up'              => '^',
                    'l_r_bracket'     => '(',
                    'r_r_bracket'     => ')'
                ],
                'multi' => [
                    'neq'            => '<>',
                    'le'             => '<=',
                    'ge'             => '>=',
                    'set'            => ':=',
                    'dots'           => '..'
                ],
            ],
            self::DELIMITER => [
                'single' => [
                    'semicolon' => ';'
                ]
            ],
            self::WORD_SYMBOL => [
                'multi' => [
                    'and'           => 'and',
                    'array'         => 'array',
                    'begin'         => 'begin',
                    'case'          => 'case',
                    'const'         => 'const',
                    'div'           => 'div',
                    'do'            => 'do',
                    'downto'        => 'downto',
                    'else'          => 'else',
                    'end'           => 'end',
                    'file'          => 'file',
                    'for'           => 'for',
                    'function'      => 'function',
                    'goto'          => 'goto',
                    'if'            => 'if',
                    'in'            => 'in',
                    'label'         => 'label',
                    'mod'           => 'mod',
                    'nil'           => 'nil',
                    'not'           => 'not',
                    'of'            => 'of',
                    'or'            => 'or',
                    'packed'        => 'packed',
                    'procedure'     => 'procedure',
                    'program'       => 'program',
                    'record'        => 'record',
                    'repeat'        => 'repeat',
                    'set'           => 'set',
                    'then'          => 'then',
                    'to'            => 'to',
                    'type'          => 'type',
                    'until'         => 'until',
                    'var'           => 'var',
                    'while'         => 'while',
                    'with'          => 'with'
                ]
            ]
        ];
        public static function isSeparator($char)
        {
            return !in_array($char, ['>', '=', '<', ':']) &&
                   (in_array($char, self::$symbols[self::OPERATOR]['alone']) ||
                    in_array($char, self::$symbols[self::DELIMITER]['alone']));
        }
    }
