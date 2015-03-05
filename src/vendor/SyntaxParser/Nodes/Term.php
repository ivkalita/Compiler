<?php

namespace vendor\SyntaxParser\Nodes;

use vendor\SyntaxParser\Nodes\Node;

class Term extends Node
{
	private $tip = null;
	public $symType = null;


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
	public function __construct($scanner, $_symTable)
	{
		$factor = new Factor($scanner, $_symTable);
		$this->tip = null;
		while ($scanner->get()->isMultOperation()) {
			$operator = $scanner->get();
			$scanner->next();
			$factor2 = new Factor($scanner, $_symTable);
			if (!$this->tip) {
				$this->tip = new BinOp($factor, $factor2, $operator, $_symTable);
				continue;
			}
			$this->tip = new BinOp($this->tip, $factor2, $operator, $_symTable);
		}
		if (!$this->tip) {
			$this->tip = $factor;
		}
		$this->symType = $this->tip->symType;
	}

	public function toIdArray(&$id)
    {
        return $this->tip->toIdArray($id);
    }

}