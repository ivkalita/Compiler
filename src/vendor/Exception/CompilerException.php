<?php
/**
 * Created by IntelliJ IDEA.
 * User: kaduev
 * Date: 26.09.14
 * Time: 11:11
 */
    namespace vendor\Exception;

    use vendor\Utility\TextPoint;

    class CompilerException extends \Exception
    {
        const TOKEN_EXCEPTION = "Token parser exception";
        const BASE_EXCEPTION = "Unknown exception";
        const SYNTAX_EXCEPTION = "Syntax parser exception";
        protected $module;
        protected $point;
        protected $text;

        public function __construct(TextPoint $point, $text)
        {
            parent::__construct('', 0, null);
            $this->module = self::BASE_EXCEPTION;
            $this->point = clone $point;
            $this->text = $text;
        }

        public function __toString()
        {
            return "{$this->module}:\nPosition:{$this->point}\nText:\"{$this->text}\"\n";
        }
    }