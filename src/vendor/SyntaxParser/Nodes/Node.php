<?php
/**
 * Created by IntelliJ IDEA.
 * User: kaduev
 * Date: 08.10.14
 * Time: 9:55
 */

    namespace vendor\SyntaxParser\Nodes;

    use vendor\TokenParser\Scanner;
    use vendor\TokenParser\Token;
    use vendor\Exception\SyntaxException;

    class Node
    {
    	static public function eofLessNext($scanner, $expected)
    	{
            if (!$scanner->next()) {
            	throw new SyntaxException(
            	    $scanner->get()->point,
            	    $scanner->get()->text,
            	    $expected,
                    "<EOF>"
            	);
        	}
    	}

        static public function semicolonPass($scanner)
        {
            if (!$scanner->get()->isSemicolon()) {
                self::simpleException($scanner, ['<SEMICOLON>']);
            }
            $scanner->next();
        }

        static public function simpleException($scanner, $expected)
        {
            throw new SyntaxException(
                $scanner->get()->point,
                $scanner->get()->text,
                $expected,
                "<" . $scanner->get()->type . ">"
            );
        }
    }