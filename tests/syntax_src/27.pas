{Test27: assignment with function-designator2}
program t27;

var
	a: integer;

function f(arg1: integer): integer;
begin
	result := arg1;
end;

begin
	a := f(1);
end.