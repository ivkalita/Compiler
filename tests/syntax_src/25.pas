{Test25: assignment to array, array as index}
program t25;
var
	b: array [0..2] of integer;
	a: array [0..2] of integer;
begin
	a[b[0]] := 1;
end.