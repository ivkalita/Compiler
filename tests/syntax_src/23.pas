{Test23: assignment to array of records with array}
program t23;
type
myrec = record
	b: record
		c: array [0..1] of integer;
	end;
end;
var
	a: array [0..2] of myrec;
begin
	a[0].b.c[0] := 1;
end.