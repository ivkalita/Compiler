<?php

namespace vendor\Exception;

use vendor\Utility\TextPoint;

class CompilerException extends \Exception
{
    const TOKEN_EXCEPTION = "Token parser exception";
    const BASE_EXCEPTION = "Unknown exception";
    const SYNTAX_EXCEPTION = "Syntax parser exception";
    const SEMANTIC_EXCEPTION = "Semantic parser exception";
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
        $str = "{$this->module}:\n" . ($this->point->idx > -1 ? "Position:{$this->point}\n" : "") . "Text:\"{$this->text}\"\n";
        return $str;
    }
}