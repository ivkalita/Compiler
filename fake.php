#!/usr/bin/php
<?php
	$testCnt = 44;
	for ($i = 1; $i <= $testCnt; $i++) {
		$fname = ($i < 10 ? "0$i" : $i . "");
		exec("./compiler.php -f tests/symbol_src/$fname.pas -o tests/symbol_eta/$fname.out --table-only");
	}
