{Test22: assignment to array of records}
program t22;
type
	myrec = record
		b: integer;
	end;
var
	a: array [0..2] of myrec;
begin
	a[0].b := 1;
end.