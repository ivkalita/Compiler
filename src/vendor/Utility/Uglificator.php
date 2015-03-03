<?php

namespace vendor\Utility;

use vendor\Exception\CompilerException;

class Uglificator
{
	const DELIMITER = '??';
	static public function twoVals($usual, $unique)
	{
		return $usual . self::DELIMITER . "_$unique";
	}
}