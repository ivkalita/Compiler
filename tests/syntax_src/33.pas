{Test33: pointer and record and array}
program t33;
type
	arr = array [0..2] of integer;
	rec1 = record
		d: record
			c: ^arr;
		end;
	end;
var
	a: integer;
	b: array [0..2] of ^rec1;
begin
	a := b[0]^.d.c^[1];
end.