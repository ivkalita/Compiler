{Test24: assignment to array, expression as index}
program t24;
var
	a: array [0..3] of integer;
begin
	a[1+(2-3)] := 1;
end.