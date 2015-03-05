{Test32: pointer and record}
program t32;
var
	b: record
		c: ^integer;
	end;
	a: integer;
begin
	a := b.c^;
end.