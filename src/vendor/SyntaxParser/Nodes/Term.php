<?php

namespace vendor\SyntaxParser\Nodes;

use vendor\SyntaxParser\Nodes\Node;

class Term extends Node
{
	private $tip = null;


	//a * b * c / e
	//  *
	//a   b
	//-------------
	//     *
	//  *    c
	//a   b
	//-------------
	//         /
	//     *     e
	//  *    c
	//a   b
	static public function parse($scanner)
	{
		// echo "Factor::parse()\n";
		$factor = Factor::parse($scanner);
		$tip = null;
		while ($scanner->get()->isMultOperation()) {
			$operator = $scanner->get();
			parent::eofLessNext($scanner, ['<FACTOR>']);
			$factor2 = Factor::parse($scanner);
			if (!$tip) {
				$tip = new BinOp($factor, $factor2, $operator);
				continue;
			}
			$tip = new BinOp($tip, $factor2, $operator);
		}
		if (!$tip) {
			$tip = $factor;
		}
		return new Term($tip);
	}

	public function __construct($tip)
	{
		$this->tip = $tip;
	}

	static public function firstTokens()
	{
		return Factor::firstTokens();
	}

	public function toIdArray(&$id)
    {
        return $this->tip->toIdArray($id);
    }

}