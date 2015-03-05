{Test26: assignment with function-designator}
program t26;

var
	a: integer;

function f(): integer;
begin
	result := 2;
end;

begin
	a := f();
end.