{Test21: assignment to 2-level record}
program t21;
var
	a: record
		b: record
			c: integer;
		end;
	end;
begin
	a.b.c := 1;
end.