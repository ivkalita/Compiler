var labelType, useGradients, nativeTextSupport, animate, editor, srcs, trees, cur, st;
        (function() {
          var ua = navigator.userAgent,
              iStuff = ua.match(/iPhone/i) || ua.match(/iPad/i),
              typeOfCanvas = typeof HTMLCanvasElement,
              nativeCanvasSupport = (typeOfCanvas == 'object' || typeOfCanvas == 'function'),
              textSupport = nativeCanvasSupport 
                && (typeof document.createElement('canvas').getContext('2d').fillText == 'function');
          labelType = (!nativeCanvasSupport || (textSupport && !iStuff))? 'Native' : 'HTML';
          nativeTextSupport = labelType == 'Native';
          useGradients = nativeCanvasSupport;
          animate = !(iStuff || !nativeCanvasSupport);
        })();

        var Log = {
          elem: false,
          write: function(text){
            if (!this.elem) 
              this.elem = document.getElementById('log');
            this.elem.innerHTML = text;
            this.elem.style.left = (500 - this.elem.offsetWidth / 2) + 'px';
          }
        };

        function init(){
            trees = [{"id":0,"name":"Program","children":[{"id":1,"name":"Heading","children":[{"id":2,"name":"t1"}]},{"id":3,"name":"Block","children":[{"id":4,"name":"CompoundStatement","children":[]}]}]},{"id":0,"name":"Program","children":[{"id":1,"name":"Heading","children":[{"id":2,"name":"t2"}]},{"id":3,"name":"Block","children":[{"id":4,"name":"CompoundStatement","children":[{"id":11,"name":"AssignmentStatement","children":[{"id":5,"name":"LValue","children":[{"id":6,"name":"EntireVariable","children":[{"id":7,"name":"identifier=a"}]}]},{"id":8,"name":"RValue","children":[{"id":10,"name":"1"}]}]}]}]}]},{"id":0,"name":"Program","children":[{"id":1,"name":"Heading","children":[{"id":2,"name":"t3"}]},{"id":3,"name":"Block","children":[{"id":4,"name":"CompoundStatement","children":[{"id":12,"name":"AssignmentStatement","children":[{"id":5,"name":"LValue","children":[{"id":6,"name":"EntireVariable","children":[{"id":7,"name":"identifier=a"}]}]},{"id":8,"name":"RValue","children":[{"id":10,"name":"-","children":[{"id":11,"name":"1"}]}]}]},{"id":20,"name":"AssignmentStatement","children":[{"id":13,"name":"LValue","children":[{"id":14,"name":"EntireVariable","children":[{"id":15,"name":"identifier=a"}]}]},{"id":16,"name":"RValue","children":[{"id":18,"name":"+","children":[{"id":19,"name":"1"}]}]}]}]}]}]},{"id":0,"name":"Program","children":[{"id":1,"name":"Heading","children":[{"id":2,"name":"t4"}]},{"id":3,"name":"Block","children":[{"id":4,"name":"CompoundStatement","children":[{"id":14,"name":"AssignmentStatement","children":[{"id":5,"name":"LValue","children":[{"id":6,"name":"EntireVariable","children":[{"id":7,"name":"identifier=a"}]}]},{"id":8,"name":"RValue","children":[{"id":10,"name":"+","children":[{"id":11,"name":"1"},{"id":13,"name":"2"}]}]}]},{"id":24,"name":"AssignmentStatement","children":[{"id":15,"name":"LValue","children":[{"id":16,"name":"EntireVariable","children":[{"id":17,"name":"identifier=a"}]}]},{"id":18,"name":"RValue","children":[{"id":20,"name":"-","children":[{"id":21,"name":"1"},{"id":23,"name":"2"}]}]}]}]}]}]},{"id":0,"name":"Program","children":[{"id":1,"name":"Heading","children":[{"id":2,"name":"t5"}]},{"id":3,"name":"Block","children":[{"id":4,"name":"CompoundStatement","children":[{"id":17,"name":"AssignmentStatement","children":[{"id":5,"name":"LValue","children":[{"id":6,"name":"EntireVariable","children":[{"id":7,"name":"identifier=a"}]}]},{"id":8,"name":"RValue","children":[{"id":10,"name":"-","children":[{"id":11,"name":"-","children":[{"id":12,"name":"1"},{"id":14,"name":"2"}]},{"id":16,"name":"3"}]}]}]}]}]}]},{"id":0,"name":"Program","children":[{"id":1,"name":"Heading","children":[{"id":2,"name":"t6"}]},{"id":3,"name":"Block","children":[{"id":4,"name":"CompoundStatement","children":[{"id":13,"name":"AssignmentStatement","children":[{"id":5,"name":"LValue","children":[{"id":6,"name":"EntireVariable","children":[{"id":7,"name":"identifier=a"}]}]},{"id":8,"name":"RValue","children":[{"id":10,"name":"-","children":[{"id":12,"name":"1"}]}]}]}]}]}]},{"id":0,"name":"Program","children":[{"id":1,"name":"Heading","children":[{"id":2,"name":"t7"}]},{"id":3,"name":"Block","children":[{"id":4,"name":"CompoundStatement","children":[{"id":16,"name":"AssignmentStatement","children":[{"id":5,"name":"LValue","children":[{"id":6,"name":"EntireVariable","children":[{"id":7,"name":"identifier=a"}]}]},{"id":8,"name":"RValue","children":[{"id":10,"name":"-","children":[{"id":12,"name":"+","children":[{"id":13,"name":"1"},{"id":15,"name":"2"}]}]}]}]}]}]}]},{"id":0,"name":"Program","children":[{"id":1,"name":"Heading","children":[{"id":2,"name":"t8"}]},{"id":3,"name":"Block","children":[{"id":4,"name":"CompoundStatement","children":[{"id":20,"name":"AssignmentStatement","children":[{"id":5,"name":"LValue","children":[{"id":6,"name":"EntireVariable","children":[{"id":7,"name":"identifier=a"}]}]},{"id":8,"name":"RValue","children":[{"id":10,"name":"-","children":[{"id":12,"name":"-","children":[{"id":13,"name":"1"},{"id":16,"name":"+","children":[{"id":17,"name":"2"},{"id":19,"name":"3"}]}]}]}]}]}]}]}]},{"id":0,"name":"Program","children":[{"id":1,"name":"Heading","children":[{"id":2,"name":"t9"}]},{"id":3,"name":"Block","children":[{"id":4,"name":"CompoundStatement","children":[{"id":14,"name":"AssignmentStatement","children":[{"id":5,"name":"LValue","children":[{"id":6,"name":"EntireVariable","children":[{"id":7,"name":"identifier=a"}]}]},{"id":8,"name":"RValue","children":[{"id":10,"name":"*","children":[{"id":11,"name":"1"},{"id":13,"name":"2"}]}]}]}]}]}]},{"id":0,"name":"Program","children":[{"id":1,"name":"Heading","children":[{"id":2,"name":"t10"}]},{"id":3,"name":"Block","children":[{"id":4,"name":"CompoundStatement","children":[{"id":17,"name":"AssignmentStatement","children":[{"id":5,"name":"LValue","children":[{"id":6,"name":"EntireVariable","children":[{"id":7,"name":"identifier=a"}]}]},{"id":8,"name":"RValue","children":[{"id":10,"name":"\/","children":[{"id":11,"name":"*","children":[{"id":12,"name":"1"},{"id":14,"name":"2"}]},{"id":16,"name":"3"}]}]}]}]}]}]},{"id":0,"name":"Program","children":[{"id":1,"name":"Heading","children":[{"id":2,"name":"t11"}]},{"id":3,"name":"Block","children":[{"id":4,"name":"CompoundStatement","children":[{"id":17,"name":"AssignmentStatement","children":[{"id":5,"name":"LValue","children":[{"id":6,"name":"EntireVariable","children":[{"id":7,"name":"identifier=a"}]}]},{"id":8,"name":"RValue","children":[{"id":10,"name":"+","children":[{"id":11,"name":"1"},{"id":13,"name":"\/","children":[{"id":14,"name":"2"},{"id":16,"name":"3"}]}]}]}]}]}]}]},{"id":0,"name":"Program","children":[{"id":1,"name":"Heading","children":[{"id":2,"name":"t12"}]},{"id":3,"name":"Block","children":[{"id":4,"name":"CompoundStatement","children":[{"id":18,"name":"AssignmentStatement","children":[{"id":5,"name":"LValue","children":[{"id":6,"name":"EntireVariable","children":[{"id":7,"name":"identifier=a"}]}]},{"id":8,"name":"RValue","children":[{"id":10,"name":"\/","children":[{"id":12,"name":"+","children":[{"id":13,"name":"1"},{"id":15,"name":"2"}]},{"id":17,"name":"3"}]}]}]}]}]}]},{"id":0,"name":"Program","children":[{"id":1,"name":"Heading","children":[{"id":2,"name":"t13"}]},{"id":3,"name":"Block","children":[{"id":4,"name":"ConstDefPart","children":[{"id":5,"name":"ConstDef","children":[{"id":6,"name":"identifier = a"},{"id":7,"name":"constant = 1"}]}]},{"id":9,"name":"CompoundStatement","children":[]}]}]},{"id":0,"name":"Program","children":[{"id":1,"name":"Heading","children":[{"id":2,"name":"t14"}]},{"id":3,"name":"Block","children":[{"id":4,"name":"ConstDefPart","children":[{"id":5,"name":"ConstDef","children":[{"id":6,"name":"identifier = a"},{"id":7,"name":"constant = 10"}]},{"id":9,"name":"ConstDef","children":[{"id":10,"name":"identifier = b"},{"id":11,"name":"constant = 3"}]}]},{"id":13,"name":"CompoundStatement","children":[]}]}]},{"id":0,"name":"Program","children":[{"id":1,"name":"Heading","children":[{"id":2,"name":"t15"}]},{"id":3,"name":"Block","children":[{"id":4,"name":"ConstDefPart","children":[{"id":5,"name":"ConstDef","children":[{"id":6,"name":"identifier = a"},{"id":7,"name":"constant = 1.5"}]}]},{"id":9,"name":"CompoundStatement","children":[]}]}]},{"id":0,"name":"Program","children":[{"id":1,"name":"Heading","children":[{"id":2,"name":"t16"}]},{"id":3,"name":"Block","children":[{"id":4,"name":"ConstDefPart","children":[{"id":5,"name":"ConstDef","children":[{"id":6,"name":"identifier = a"},{"id":7,"name":"constant = 1.5"}]},{"id":9,"name":"ConstDef","children":[{"id":10,"name":"identifier = b"},{"id":11,"name":"constant = 2e-3"}]}]},{"id":13,"name":"CompoundStatement","children":[]}]}]},{"id":0,"name":"Program","children":[{"id":1,"name":"Heading","children":[{"id":2,"name":"t17"}]},{"id":3,"name":"Block","children":[{"id":4,"name":"ConstDefPart","children":[{"id":5,"name":"ConstDef","children":[{"id":6,"name":"identifier = a"},{"id":7,"name":"constant = abc"}]}]},{"id":9,"name":"CompoundStatement","children":[]}]}]},{"id":0,"name":"Program","children":[{"id":1,"name":"Heading","children":[{"id":2,"name":"t18"}]},{"id":3,"name":"Block","children":[{"id":4,"name":"CompoundStatement","children":[{"id":15,"name":"AssignmentStatement","children":[{"id":5,"name":"LValue","children":[{"id":10,"name":"IndexedVariable","children":[{"id":6,"name":"EntireVariable","children":[{"id":7,"name":"identifier=a"}]},{"id":11,"name":"indexes","children":[{"id":9,"name":"0"}]}]}]},{"id":12,"name":"RValue","children":[{"id":14,"name":"1"}]}]}]}]}]},{"id":0,"name":"Program","children":[{"id":1,"name":"Heading","children":[{"id":2,"name":"t19"}]},{"id":3,"name":"Block","children":[{"id":4,"name":"CompoundStatement","children":[{"id":17,"name":"AssignmentStatement","children":[{"id":5,"name":"LValue","children":[{"id":12,"name":"IndexedVariable","children":[{"id":6,"name":"EntireVariable","children":[{"id":7,"name":"identifier=a"}]},{"id":13,"name":"indexes","children":[{"id":9,"name":"0"},{"id":11,"name":"2"}]}]}]},{"id":14,"name":"RValue","children":[{"id":16,"name":"1"}]}]}]}]}]},{"id":0,"name":"Program","children":[{"id":1,"name":"Heading","children":[{"id":2,"name":"t20"}]},{"id":3,"name":"Block","children":[{"id":4,"name":"CompoundStatement","children":[{"id":13,"name":"AssignmentStatement","children":[{"id":5,"name":"LValue","children":[{"id":9,"name":"ComponentVariable","children":[{"id":6,"name":"EntireVariable","children":[{"id":7,"name":"identifier=a"}]},{"id":8,"name":"designator=b"}]}]},{"id":10,"name":"RValue","children":[{"id":12,"name":"1"}]}]}]}]}]},{"id":0,"name":"Program","children":[{"id":1,"name":"Heading","children":[{"id":2,"name":"t21"}]},{"id":3,"name":"Block","children":[{"id":4,"name":"CompoundStatement","children":[{"id":15,"name":"AssignmentStatement","children":[{"id":5,"name":"LValue","children":[{"id":11,"name":"ComponentVariable","children":[{"id":9,"name":"ComponentVariable","children":[{"id":6,"name":"EntireVariable","children":[{"id":7,"name":"identifier=a"}]},{"id":8,"name":"designator=b"}]},{"id":10,"name":"designator=c"}]}]},{"id":12,"name":"RValue","children":[{"id":14,"name":"1"}]}]}]}]}]},{"id":0,"name":"Program","children":[{"id":1,"name":"Heading","children":[{"id":2,"name":"t22"}]},{"id":3,"name":"Block","children":[{"id":4,"name":"CompoundStatement","children":[{"id":17,"name":"AssignmentStatement","children":[{"id":5,"name":"LValue","children":[{"id":13,"name":"ComponentVariable","children":[{"id":10,"name":"IndexedVariable","children":[{"id":6,"name":"EntireVariable","children":[{"id":7,"name":"identifier=a"}]},{"id":11,"name":"indexes","children":[{"id":9,"name":"0"}]}]},{"id":12,"name":"designator=b"}]}]},{"id":14,"name":"RValue","children":[{"id":16,"name":"1"}]}]}]}]}]},{"id":0,"name":"Program","children":[{"id":1,"name":"Heading","children":[{"id":2,"name":"t23"}]},{"id":3,"name":"Block","children":[{"id":4,"name":"CompoundStatement","children":[{"id":23,"name":"AssignmentStatement","children":[{"id":5,"name":"LValue","children":[{"id":18,"name":"IndexedVariable","children":[{"id":15,"name":"ComponentVariable","children":[{"id":13,"name":"ComponentVariable","children":[{"id":10,"name":"IndexedVariable","children":[{"id":6,"name":"EntireVariable","children":[{"id":7,"name":"identifier=a"}]},{"id":11,"name":"indexes","children":[{"id":9,"name":"0"}]}]},{"id":12,"name":"designator=b"}]},{"id":14,"name":"designator=c"}]},{"id":19,"name":"indexes","children":[{"id":17,"name":"0"}]}]}]},{"id":20,"name":"RValue","children":[{"id":22,"name":"1"}]}]}]}]}]},{"id":0,"name":"Program","children":[{"id":1,"name":"Heading","children":[{"id":2,"name":"t24"}]},{"id":3,"name":"Block","children":[{"id":4,"name":"CompoundStatement","children":[{"id":22,"name":"AssignmentStatement","children":[{"id":5,"name":"LValue","children":[{"id":17,"name":"IndexedVariable","children":[{"id":6,"name":"EntireVariable","children":[{"id":7,"name":"identifier=a"}]},{"id":18,"name":"indexes","children":[{"id":9,"name":"+","children":[{"id":10,"name":"1"},{"id":13,"name":"-","children":[{"id":14,"name":"2"},{"id":16,"name":"3"}]}]}]}]}]},{"id":19,"name":"RValue","children":[{"id":21,"name":"1"}]}]}]}]}]},{"id":0,"name":"Program","children":[{"id":1,"name":"Heading","children":[{"id":2,"name":"t25"}]},{"id":3,"name":"Block","children":[{"id":4,"name":"CompoundStatement","children":[{"id":20,"name":"AssignmentStatement","children":[{"id":5,"name":"LValue","children":[{"id":15,"name":"IndexedVariable","children":[{"id":6,"name":"EntireVariable","children":[{"id":7,"name":"identifier=a"}]},{"id":16,"name":"indexes","children":[{"id":13,"name":"IndexedVariable","children":[{"id":9,"name":"EntireVariable","children":[{"id":10,"name":"identifier=b"}]},{"id":14,"name":"indexes","children":[{"id":12,"name":"0"}]}]}]}]}]},{"id":17,"name":"RValue","children":[{"id":19,"name":"1"}]}]}]}]}]},{"id":0,"name":"Program","children":[{"id":1,"name":"Heading","children":[{"id":2,"name":"t26"}]},{"id":3,"name":"Block","children":[{"id":4,"name":"CompoundStatement","children":[{"id":14,"name":"AssignmentStatement","children":[{"id":5,"name":"LValue","children":[{"id":6,"name":"EntireVariable","children":[{"id":7,"name":"identifier=a"}]}]},{"id":8,"name":"RValue","children":[{"id":10,"name":"FunctionDesignator","children":[{"id":11,"name":"f"},{"id":13,"name":"ActualParamList","children":{"id":12,"name":"no params"}}]}]}]}]}]}]},{"id":0,"name":"Program","children":[{"id":1,"name":"Heading","children":[{"id":2,"name":"t27"}]},{"id":3,"name":"Block","children":[{"id":4,"name":"CompoundStatement","children":[{"id":15,"name":"AssignmentStatement","children":[{"id":5,"name":"LValue","children":[{"id":6,"name":"EntireVariable","children":[{"id":7,"name":"identifier=a"}]}]},{"id":8,"name":"RValue","children":[{"id":10,"name":"FunctionDesignator","children":[{"id":11,"name":"f"},{"id":14,"name":"ActualParamList","children":[{"id":13,"name":"1"}]}]}]}]}]}]}]},{"id":0,"name":"Program","children":[{"id":1,"name":"Heading","children":[{"id":2,"name":"t28"}]},{"id":3,"name":"Block","children":[{"id":4,"name":"CompoundStatement","children":[{"id":18,"name":"AssignmentStatement","children":[{"id":5,"name":"LValue","children":[{"id":6,"name":"EntireVariable","children":[{"id":7,"name":"identifier=a"}]}]},{"id":8,"name":"RValue","children":[{"id":10,"name":"FunctionDesignator","children":[{"id":11,"name":"f"},{"id":17,"name":"ActualParamList","children":[{"id":13,"name":"1"},{"id":15,"name":"EntireVariable","children":[{"id":16,"name":"identifier=b"}]}]}]}]}]}]}]}]},{"id":0,"name":"Program","children":[{"id":1,"name":"Heading","children":[{"id":2,"name":"t29"}]},{"id":3,"name":"Block","children":[{"id":4,"name":"CompoundStatement","children":[{"id":12,"name":"AssignmentStatement","children":[{"id":5,"name":"LValue","children":[{"id":6,"name":"PointerVariable","children":[{"id":7,"name":"EntireVariable","children":[{"id":8,"name":"identifier=a"}]}]}]},{"id":9,"name":"RValue","children":[{"id":11,"name":"1"}]}]}]}]}]},{"id":0,"name":"Program","children":[{"id":1,"name":"Heading","children":[{"id":2,"name":"t30"}]},{"id":3,"name":"Block","children":[{"id":4,"name":"CompoundStatement","children":[{"id":13,"name":"AssignmentStatement","children":[{"id":5,"name":"LValue","children":[{"id":6,"name":"EntireVariable","children":[{"id":7,"name":"identifier=a"}]}]},{"id":8,"name":"RValue","children":[{"id":10,"name":"PointerVariable","children":[{"id":11,"name":"EntireVariable","children":[{"id":12,"name":"identifier=b"}]}]}]}]}]}]}]},{"id":0,"name":"Program","children":[{"id":1,"name":"Heading","children":[{"id":2,"name":"t31"}]},{"id":3,"name":"Block","children":[{"id":4,"name":"CompoundStatement","children":[{"id":17,"name":"AssignmentStatement","children":[{"id":5,"name":"LValue","children":[{"id":6,"name":"EntireVariable","children":[{"id":7,"name":"identifier=a"}]}]},{"id":8,"name":"RValue","children":[{"id":10,"name":"PointerVariable","children":[{"id":15,"name":"IndexedVariable","children":[{"id":11,"name":"EntireVariable","children":[{"id":12,"name":"identifier=b"}]},{"id":16,"name":"indexes","children":[{"id":14,"name":"0"}]}]}]}]}]}]}]}]},{"id":0,"name":"Program","children":[{"id":1,"name":"Heading","children":[{"id":2,"name":"t32"}]},{"id":3,"name":"Block","children":[{"id":4,"name":"CompoundStatement","children":[{"id":15,"name":"AssignmentStatement","children":[{"id":5,"name":"LValue","children":[{"id":6,"name":"EntireVariable","children":[{"id":7,"name":"identifier=a"}]}]},{"id":8,"name":"RValue","children":[{"id":10,"name":"PointerVariable","children":[{"id":14,"name":"ComponentVariable","children":[{"id":11,"name":"EntireVariable","children":[{"id":12,"name":"identifier=b"}]},{"id":13,"name":"designator=c"}]}]}]}]}]}]}]},{"id":0,"name":"Program","children":[{"id":1,"name":"Heading","children":[{"id":2,"name":"t33"}]},{"id":3,"name":"Block","children":[{"id":4,"name":"CompoundStatement","children":[{"id":26,"name":"AssignmentStatement","children":[{"id":5,"name":"LValue","children":[{"id":6,"name":"EntireVariable","children":[{"id":7,"name":"identifier=a"}]}]},{"id":8,"name":"RValue","children":[{"id":24,"name":"IndexedVariable","children":[{"id":10,"name":"PointerVariable","children":[{"id":21,"name":"ComponentVariable","children":[{"id":19,"name":"ComponentVariable","children":[{"id":11,"name":"PointerVariable","children":[{"id":16,"name":"IndexedVariable","children":[{"id":12,"name":"EntireVariable","children":[{"id":13,"name":"identifier=b"}]},{"id":17,"name":"indexes","children":[{"id":15,"name":"0"}]}]}]},{"id":18,"name":"designator=d"}]},{"id":20,"name":"designator=c"}]}]},{"id":25,"name":"indexes","children":[{"id":23,"name":"1"}]}]}]}]}]}]}]}];
            srcs = [["{Test1: empty program}","program t1;","begin","end."],["{Test2: assignment with unsignment integer}","program t2;","begin","\ta := 1;","end."],["{Test3: assignment with signed integer}","program t3;","begin","\ta := -1;","\ta := +1;","end."],["{Test4: assignment with simple expression}","program t4;","begin","\ta := 1 + 2;","\ta := 1 - 2;","end."],["{Test5: assignment with minus operation}","program t5;","begin","\ta := 1 - 2 - 3;","end."],["{Test6: assignment with priority}","program t6;","begin","\ta := -(1);","end."],["{Test7: assignment with priority2}","program t7;","begin","\ta := -(1 + 2);","end."],["{Test8: assignment with priority3}","program t8;","begin","\ta := -(1 - (2 + 3));","end."],["{Test9: assignment with mult}","program t9;","begin","\ta := 1 * 2;","end."],["{Test10: assignment with mult2}","program t10;","begin","\ta := 1 * 2 \/ 3;","end."],["{Test11: assignment with mult and add}","program t11;","begin","\ta := 1 + 2 \/ 3;","end."],["{Test12: assignment with mult, add and priority}","program t12;","begin","\ta := (1 + 2) \/ 3;","end."],["{Test13: consts unsigned int}","program t13;","const","\ta = 1;","begin","end."],["{Test14: consts 2 }","program t14;","const","\ta = 10;","\tb = 3;","begin","end."],["{Test15: consts unsigned float}","program t15;","const","\ta = 1.5;","begin","end."],["{Test16: consts 2 float}","program t16;","const","\ta = 1.5;","\tb =  2e-3;","begin","end."],["{Test17: consts string}","program t17;","const","\ta = 'abc';","begin","end."],["{Test18: assignment to 1-d array}","program t18;","begin","\ta[0] := 1;","end."],["{Test19: assignment to 2-d array}","program t19;","begin","\ta[0, 2] := 1;","end."],["{Test20: assignment to record}","program t20;","begin","\ta.b := 1;","end."],["{Test21: assignment to 2-level record}","program t21;","begin","\ta.b.c := 1;","end."],["{Test22: assignment to array of records}","program t22;","begin","\ta[0].b := 1;","end."],["{Test23: assignment to array of records with array}","program t23;","begin","\ta[0].b.c[0] := 1;","end."],["{Test24: assignment to array, expression as index}","program t24;","begin","\ta[1+(2-3)] := 1;","end."],["{Test25: assignment to array, array as index}","program t25;","begin","\ta[b[0]] := 1;","end."],["{Test26: assignment with function-designator}","program t26;","begin","\ta := f();","end."],["{Test27: assignment with function-designator2}","program t27;","begin","\ta := f(1);","end."],["{Test28: assignment with function-designator3}","program t28;","begin","\ta := f(1, b);","end."],["{Test29: assignment to pointer}","program t29;","begin","\ta^ := 1;","end."],["{Test30: assignment with pointer}","program t30;","begin","\ta := b^;","end."],["{Test31: pointer and array}","program t31;","begin","\ta := b[0]^;","end."],["{Test32: pointer and record}","program t32;","begin","\ta := b.c^;","end."],["{Test33: pointer and record and array}","program t33;","begin","\ta := b[0]^.d.c^[1];","end."]];
            st = new $jit.ST({
                levelsToShow: 200,
                orientation: "top",
                injectInto: 'infovis',
                duration: 0,
                transition: $jit.Trans.Quart.easeInOut,
                levelDistance: 30,
                Navigation: {
                  enable:true,
                  panning:true
                },
                Node: {
                    height: 30,
                    width: 120,
                    type: 'rectangle',
                    color: '#aaa',
                    overridable: true
                },
                
                Edge: {
                    type: 'bezier',
                    overridable: true
                },
                
                onBeforeCompute: function(node){
                    Log.write("loading " + node.name);
                },
                
                onAfterCompute: function(){
                    Log.write("done");
                },
                
                onCreateLabel: function(label, node){
                    label.id = node.id;            
                    label.innerHTML = node.name;
                    label.onclick = function(){
                        st.onClick(node.id);
                    };
                    var style = label.style;
                    style.width = 60 + 'px';
                    style.height = 17 + 'px';            
                    style.cursor = 'pointer';
                    style.color = '#333';
                    style.fontSize = '0.8em';
                    style.textAlign= 'center';
                    style.paddingTop = '3px';
                },
                
                onBeforePlotNode: function(node){
                    if (node.selected) {
                        node.data.$color = "#ff7";
                    }
                    else {
                        delete node.data.$color;
                        if(!node.anySubnode("exist")) {
                            var count = 0;
                            node.eachSubnode(function(n) { count++; });
                            node.data.$color = ['#aaa', '#baa', '#caa', '#daa', '#eaa', '#faa'][count];                    
                        }
                    }
                },
                
                onBeforePlotLine: function(adj){
                    if (adj.nodeFrom.selected && adj.nodeTo.selected) {
                        adj.data.$color = "#eed";
                        adj.data.$lineWidth = 3;
                    }
                    else {
                        delete adj.data.$color;
                        delete adj.data.$lineWidth;
                    }
                }
            });
            st.loadJSON(trees[0]);
            st.compute();
            st.onClick(st.root);
            editor = ace.edit("editor");
            editor.setTheme("ace/theme/monokai");
            editor.getSession().setMode("ace/mode/pascal");
            cur = 0;
            switchToTest();
            document.getElementById("btnNext").onclick = function(e) {
                cur++;
                switchToTest();    
            }
            document.getElementById("btnPrev").onclick = function(e) {
                cur--;
                switchToTest();
            }
        }

        function switchToTest() {
            editor.setValue('');
            if (cur >= srcs.length || cur < 0) {
                return;
            }
            st.loadJSON(trees[cur]);
            st.refresh();
            srcs[cur].forEach(function(elem) {
                editor.insert(elem + '\n');
            });
        }