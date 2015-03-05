{Test31: pointer and array}
program t31;
var
	a: integer;
	b: array [0..2] of ^integer;

begin
	a := b[0]^;
end.