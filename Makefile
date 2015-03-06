syntax:
	./tester.php tests/syntax/src/ tests/syntax/out/ tests/syntax/eta/ 2
symtable:
	./tester.php tests/symbol/src/ tests/symbol/out/ tests/symbol/eta/ 3
types:
	./tester.php tests/types/src/ tests/types/out/ tests/types/eta/ 2
fp:
	./tester.php tests/fp/src/ tests/fp/out/ tests/fp/eta/ 2
test:
	./tester.php tests/syntax/src/ tests/syntax/out/ tests/syntax/eta/ 2
	./tester.php tests/symbol/src/ tests/symbol/out/ tests/symbol/eta/ 3
	./tester.php tests/types/src/ tests/types/out/ tests/types/eta/ 2
	./tester.php tests/fp/src/ tests/fp/out/ tests/fp/eta/ 2