{Test28: assignment with function-designator3}
program t28;

var
	a: real;
	b: integer;

function f(arg1: real; arg2: integer): real;
begin
	result := arg1 + arg2;
end;

begin
	a := f(1, b);
end.