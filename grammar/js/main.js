/**
 * Created by kaduev on 02.10.14.
 */
String.prototype.supplant = function (o) {
    return this.replace(/{([^{}]*)}/g,
        function (a, b) {
            var r = o[b];
            return typeof r === 'string' || typeof r === 'number' ? r : a;
        }
    );
};

function asgn(dst, src, def) { //assign
    return '{a} {c} {b}'.supplant({a: dst, b:src, c:def});
}

//function al(expr) { //alternative
//    return '[{a}]'.supplant({a: expr});
//}
function g(expr) {
    return '( {a} )'.supplant({a: expr});
}
function q(expr) { //quote
    return '`{a}`'.supplant({a: expr});
}

function qArray(arr) {
    for (var i = 0; i < arr.length; i++) {
        arr[i] = q(arr[i]);
    }
    return arr;
}

function r(arr) { //or
    var res = arr[0];
    for (var i = 1; i < arr.length; i++) {
        res += ' | {elem}'.supplant({elem:arr[i]});
    }
    return res;
}

function hTo(name, what) { //make a href
//    if (elems.indexOf(name) > -1) {
      return '<a href="#{n}">{w}</a>'.supplant({n: name, w: what});
//    } else
//        return what;
}

function hTo2(name) { //make a href
//    if (elems.indexOf(name) > -1) {
      return '<a href="#{n}">{n}</a>'.supplant({n: name});
//    } else
//        return name;
}

function hFrom(name, what) {
    return '<a id="{n}">{w}</a>'.supplant({n: name, w: what});
}

function p(expr) {
    return '<p>{a}</p>'.supplant({a: expr});
}

function zRm(expr) { //zero or one instance of expr
    return '{ ' + expr + ' }';
}

function zRo(expr) { //zero or many instance of expr
    return '[ {a} ]'.supplant({a: expr});
}

function def(expr) { //definition
    return '{a} .'.supplant({a: expr});
}

function s(arr) { //spacify
    var res = arr[0];
    for (var i = 1; i < arr.length; i++) {
        res += ' {a}'.supplant({a: arr[i]});
    }
    return res;
}

var refs = '';
var main = '';

function addElem(name, value, sgn) {
    refName = 'ref_' + name;
    refs += hTo(name, '<li id="{id}">{a}</li>'.supplant({id: refName, a: name}));
    main += p(def(asgn(hFrom(name, name), value, sgn)));
}

function addSection(name) {
    var secName = 'section_' + name;
    refs += '<li>' + hTo(secName, name) + '<ul>';
//    var s = '<li>' + hTo(secName, name) + '<ul>';
//    refs.innerHTML += '<ul>';
    main += hFrom(secName, '<h2>{a}</h2>'.supplant({a: name}));
}

function closeSection() {
    refs += '</li></ul>';
}

function initGrammar() {
    addSection('General');
    addElem(
        'digit',
        r(qArray(['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'])),
        '='
    );
    addElem(
        'letter',
        r(qArray(['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'q', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't','u', 'v', 'w', 'x', 'y', 'z'])),
        '='
    );
    closeSection();
    addSection('Special symbols');
    addElem(
        'special-symbol',
        r([r(qArray(['+', '-', '*', '/', '=', '<', '>', '[', ']', '.', ',', ':', ';', '^', '(', ')', '<>', '<=', '>=', ':=', '..'])), hTo2('word-symbol')]),
        '='
    );
    addElem(
        'word-symbol',
        r(qArray(['and', 'array', 'begin', 'case', 'const', 'div', 'do', 'downto', 'else', 'end', 'file', 'for', 'function', 'goto', 'if', 'in', 'label', 'mod', 'nil', 'not', 'of', 'or', 'packed', 'procedure', 'program', 'record', 'repeat', 'set', 'then', 'to', 'type', 'until', 'var', 'while', 'with'])),
        '='
    );
    closeSection();
    addSection('Identifiers');
    addElem(
        'identifier',
        s([hTo2('letter'), zRm(r([hTo2('letter'), hTo2('digit')]))]),
        '='
    );

    closeSection();
    addSection('Directives');
    addElem(
        'directive',
            s([hTo2('letter'), zRm(r([hTo2('letter'), hTo2('digit')]))]),
        '='
    );
    closeSection();
    addSection('Numbers');
    addElem(
        'signed-number',
        r([hTo2('signed-integer'), hTo2('signed-real')]),
        '='
    );
    addElem(
        'signed-real',
        s([zRo(hTo2('sign')), hTo2('unsigned-real')]),
        '='
    );
    addElem(
        'signed-integer',
        s([zRo(hTo2('sign')), hTo2('unsigned-integer')]),
        '='
    );
    addElem(
        'unsigned-number',
        r([hTo2('unsigned-integer'), hTo2('unsigned-real')]),
        '='
    );
    addElem(
        'sign',
        r(qArray(['+', '-'])),
        '='
    );
    addElem(
        'unsigned-real',
        r([
            s([
                hTo2('digit-sequence'),
                q('.'),
                hTo2('fractional-part'),
                zRo(s([
                    q('e'),
                    hTo2('scale-factor')
                ]))
            ]),
            s([
                hTo2('digit-sequence'),
                q('e'),
                hTo2('scale-factor')
            ])
        ]),
        '='
    );
    addElem(
        'unsigned-integer',
        hTo2('digit-sequence'),
        '='
    );
    addElem(
        'fractional-part',
        hTo2('digit-sequence'),
        '='
    );
    addElem(
        'scale-factor',
        s([
            zRo(hTo2('sign')),
            hTo2('digit-sequence')
        ]),
        '='
    );
    addElem(
        'digit-sequence',
        s([
            hTo2('digit'),
            zRm(hTo2('digit'))
        ]),
        '='
    );
    closeSection();
    addSection('Character-strings');
    addElem(
        'character-string',
        s([
            q("'"),
            hTo2('string-element'),
            zRm(hTo2('string-element')),
            q("'")
        ]),
        '='
    );
    addElem(
        'string-element',
        r([
            hTo2('apostrophe-image'),
            hTo2('string-character')
        ]),
        '='
    );
    addElem(
        'apostrophe-image',
        q("''"),
        '='
    );
    addElem(
        'string-character',
        'one-of-a-set-of-implementation-defined-characters',
        '='
    );
    closeSection();
    addSection('Blocks');
    addElem(
        'block',
        s([
            hTo2('constant-definition-part'),
            hTo2('type-definition-part'),
            hTo2('variable-declaration-part'),
            hTo2('procedure-and-function-declaration-part'),
            hTo2('statement-part')
        ]),
        '='
    );
    addElem(
        'constant-definition-part',
        zRo(s([
            q('const'),
            hTo2('constant-definition'),
            q(';'),
            zRm(s([
                hTo2('constant-definition'),
                q(';')
            ]))
        ])),
        '='
    );
    addElem(
        'type-definition-part',
        zRo(s([
            q('type'),
            hTo2('type-definition'),
            q(';'),
            zRm(s([
                hTo2('type-definition'),
                q(';')
            ]))
        ])),
        '='
    );
    addElem(
        'variable-declaration-part',
        zRo(s([
            q('var'),
            hTo2('variable-declaration'),
            q(';'),
            zRm(s([
                hTo2('variable-declaration'),
                q(';')
            ]))
        ])),
        '='
    );
    addElem(
        'procedure-and-function-declaration-part',
        zRm(s([
            g(r([
                hTo2('procedure-declaration'),
                hTo2('function-declaration')
            ])),
            q(';')
        ])),
        '='
    );
    addElem(
        'statement-part',
        hTo2('compound-statement'),
        '='
    );
    closeSection();
    addSection('Constant-definitions');
    addElem(
        'constant-definition',
        s([
            hTo2('identifier'),
            q('='),
            hTo2('constant')
        ]),
        '='
    );
    addElem(
        'constant',
        r([
            s([
                zRo(hTo2('sign')),
                g(r([
                    hTo2('unsigned-number'),
                    hTo2('constant-identifier')
                ]))
            ]),
            hTo2('character-string')
        ]),
        '='
    );
    addElem(
        'constant-identifier',
        hTo2('identifier'),
        '='
    );
    closeSection();
    addSection('Type-definitions');
    addElem(
        'type-definition',
        s([
            hTo2('identifier'),
            q('='),
            hTo2('type-denoter')
        ]),
        '='
    );
    addElem(
        'type-denoter',
        r([
            hTo2('type-identifier'),
            hTo2('new-type')
        ]),
        '='
    );
    addElem(
        'new-type',
        r([
            hTo2('new-ordinal-type'),
            hTo2('new-structured-type'),
            hTo2('new-pointer-type')
        ]),
        '='
    );
    addElem(
        'simple-type-identifier',
        hTo2('type-identifier'),
        '='
    );
    addElem(
        'structured-type-identifier',
        hTo2('type-identifier'),
        '='
    );
    addElem(
        'pointer-type-identifier',
        hTo2('type-identifier'),
        '='
    );
    addElem(
        'type-identifier',
        hTo2('identifier'),
        '='
    );
    closeSection();
    addSection('Simple-types');
    addElem(
        'simple-type',
        r([
            hTo2('ordinal-type'),
            hTo2('real-type-identifier')
        ]),
        '='
    );
    addElem(
        'ordinal-type',
        r([
            hTo2('new-ordinal-type'),
            hTo2('ordinal-type-identifier')
        ]),
        '='
    );
    addElem(
        'ordinal-type-identifier',
        hTo2('type-identifier'),
        '='
    );
    addElem(
        'real-type-identifier',
        hTo2('type-identifier'),
        '='
    );
    closeSection();
    addSection('Subrange-types');
    addElem(
        'subrange-type',
        s([
            hTo2('constant'),
            '..',
            hTo2('constant')
        ]),
        '='
    );
    closeSection();
    addSection('Structured-types');
    addElem(
        'structured-type',
        r([
            'new-structured-type',
            'structured-type-identifier'
        ]),
        '='
    );
    addElem(
        'new-structured-type',
        s([
            zRo(q('packed')),
            hTo2('unpacked-structured-type')
        ]),
        '='
    );
    addElem(
        'unpacked-structured-type',
        r([
            hTo2('array-type'),
            hTo2('record-type'),
            hTo2('set-type')
//            hTo2('file-type')
        ]),
        '='
    );
    addElem(
        'array-type',
        s([
            q('array'),
            q('['),
            hTo2('index-type'),
            zRm(s([
                q(','),
                hTo2('index-type')
            ])),
            q(']'),
            q('of'),
            hTo2('component-type')
        ]),
        '='
    );
    addElem(
        'index-type',
        hTo2('ordinal-type'),
        '='
    );
    addElem(
        'component-type',
        hTo2('type-denoter'),
        '='
    );
    closeSection();
    addSection('Record-types');
    addElem(
        'record-type',
        s([
            q('record'),
            hTo2('field-list'),
            q('end')
        ]),
        '='
    );
    addElem(
        'field-list',
        zRo(
            s([
                g(r([
                    s([
                        hTo2('fixed-part'),
                        zRo(s([
                            q(';'),
                            hTo2('variant-part')
                        ]))
                    ]),
                hTo2('variant-part')
                ])),
                zRo(q(';'))
        ])),
        '='
    );
//    closeSection();
    addElem(
        'fixed-part',
        s([
            hTo2('record-section'),
            zRm(
                s([
                    q(';'),
                    hTo2('record-section')
                ])
            )
        ]),
        '='
    );
    addElem(
        'record-section',
        s([
            hTo2('identifier-list'),
            q(':'),
            hTo2('type-denoter')
        ]),
        '='
    );
    addElem(
        'field-identifier',
        hTo2('identifier'),
        '='
    );
    addElem(
        'variant-part',
        s([
            q('case'),
            hTo2('variant-selector')
        ]),
        '='
    );
    addElem(
        'variant-selector',
        s([
            zRo(s([
                hTo2('tag-field'),
                q(':')
            ])),
            hTo2('tag-type')
        ]),
        '='
    );
    addElem(
        'tag-field',
        hTo2('identifier'),
        '='
    );
    addElem(
        'variant',
        s([
            hTo2('case-constant-list'),
            q(':'),
            q('('),
            hTo2('field-list'),
            q(')')
        ]),
        '='
    );
    addElem(
        'tag-type',
        hTo2('ordinal-type-identifier'),
        '='
    );
    addElem(
        'case-constant-list',
        s([
            hTo2('case-constant'),
            zRm(s([
                q(','),
                hTo2('case-constant')
            ]))
        ]),
        '='
    );
    addElem(
        'case-constant',
        hTo2('constant'),
        '='
    );
    closeSection();
    addSection('Set-types');
    addElem(
        'set-type',
        s([
            q('set'),
            q('of'),
            hTo2('base-type')
        ]),
        '='
    );
    addElem(
        'base-type',
        hTo2('ordinal-type'),
        '='
    );
    closeSection();
    addSection('Pointer-types');
    addElem(
        'pointer-type',
        r([
            hTo2('new-pointer-type'),
            hTo2('pointer-type-identifier')
        ]),
        '='
    );
    addElem(
        'new-pointer-type',
        s([
            q('^'),
            hTo2('domain-type')
        ]),
        '='
    );
    addElem(
        'domain-type',
        hTo2('type-identifier'),
        '='
    );
    closeSection();
    addSection('Variable-declarations');
    addElem(
        'variable-declaration',
        s([
            hTo2('identifier-list'),
            q(':'),
            hTo2('type-denoter')
        ]),
        '='
    );
    addElem(
        'variable-access',
        r([
            hTo2('entire-variable'),
            hTo2('component-variable'),
            hTo2('identified-variable')
//            hTo2('buffer-variable')
        ]),
        '='
    );
    addElem(
        'entire-variable',
        hTo2('variable-identifier'),
        '='
    );
    addElem(
        'variable-identifier',
        hTo2('identifier'),
        '='
    );
    closeSection();
    addSection('Component-variables');
    addElem(
        'component-variable',
        r([
            hTo2('indexed-variable'),
            hTo2('field-designator')
        ]),
        '='
    );
    addElem(
        'indexed-variable',
        r([
            hTo2('array-variable'),
            q('['),
            hTo2('index-expression'),
            zRm(s([
                q(','),
                hTo2('index-expression')
            ])),
            q(']')
        ]),
        '='
    );
    addElem(
        'array-variable',
        hTo2('variable-access'),
        '='
    );
    addElem(
        'index-expression',
        hTo2('expression'),
        '='
    );
    closeSection();
    addSection('Field-designators');
    addElem(
        'field-designator',
        r([
            s([
                hTo2('record-variable'),
                q('.'),
                hTo2('field-specifier')
            ]),
            hTo2('field-designator-identifier')
        ]),
        '='
    );
    addElem(
        'record-variable',
        hTo2('variable-access'),
        '='
    );
    addElem(
        'field-specifier',
        hTo2('variable-access'),
        '='
    );
    closeSection();
    addSection('Procedure-declarations');
    addElem(
        'procedure-declaration',
        r([
            s([
                hTo2('procedure-heading'),
                q(';'),
                hTo2('directive')
            ]),
            s([
                hTo2('procedure-identification'),
                q(';'),
                hTo2('procedure-block')
            ]),
            s([
                hTo2('procedure-heading'),
                q(';'),
                hTo2('procedure-block')
            ])
        ]),
        '='
    );
    addElem(
        'procedure-heading',
        s([
            q('procedure'),
            hTo2('identifier'),
            zRo(hTo2('formal-parameter-list'))
        ]),
        '='
    );
    addElem(
        'procedure-identification',
        s([
            q('procedure'),
            hTo2('procedure-identifier')
        ]),
        '='
    );
    addElem(
        'procedure-identifier',
        hTo2('identifier'),
        '='
    );
    addElem(
        'procedure-block',
        hTo2('block'),
        '='
    );
    closeSection();
    addSection('Function-declarations');
    addElem(
        'function-declaration',
        r([
            s([
                hTo2('function-heading'),
                q(';'),
                hTo2('directive')
            ]),
            s([
                hTo2('function-identification'),
                q(';'),
                hTo2('function-block')
            ]),
            s([
                hTo2('function-heading'),
                q(';'),
                hTo2('function-block')
            ])
        ]),
        '='
    );
    addElem(
        'function-heading',
        s([
            q('function'),
            hTo2('identifier'),
            zRo(hTo2('formal-parameter-list')),
            q(':'),
            hTo2('result-type')
        ]),
        '='
    );
    addElem(
        'function-identification',
        s([
            q('function'),
            hTo2('function-identifier')
        ]),
        '='
    );
    addElem(
        'function-identifier',
        hTo2('identifier'),
        '='
    );
    addElem(
        'result-type',
        r([
            hTo2('simple-type-identifier'),
            hTo2('pointer-type-identifier')
        ]),
        '='
    );
    addElem(
        'function-block',
        hTo2('block'),
        '='
    );
    closeSection();
    addSection('Parameters');
    addElem(
        'formal-parameter-list',
        s([
            q('('),
            hTo2('formal-parameter-section'),
            zRm(s([
                q(';'),
                hTo2('formal-parameter-section')
            ])),
            q(')')
        ]),
        '='
    );
    addElem(
        'formal-parameter-section',
        r([
            hTo2('value-parameter-specification'),
            hTo2('variable-parameter-specification'),
            hTo2('procedure-parameter-specification'),
            hTo2('function-parameter-specification')
        ]),
        '>'
    );
    addElem(
        'value-parameter-specification',
        s([
            hTo2('identifier-list'),
            q(':'),
            hTo2('type-identifier')
        ]),
        '='
    );
    addElem(
        'variable-parameter-specification',
        s([
            q('var'),
            hTo2('identifier-list'),
            q(':'),
            hTo2('type-identifier')
        ]),
        '='
    );
    addElem(
        'procedural-parameter-specification',
        hTo2('procedure-heading'),
        '='
    );
    addElem(
        'functional-parameter-specification',
        hTo2('function-heading'),
        '='
    );
    addElem(
        'formal-parameter-section',
        hTo2('conformant-array-parameter-specification'),
        '>'
    );
    addElem(
        'conformant-array-parameter-specification',
        r([
            hTo2('value-conformant-array-specification'),
            hTo2('variable-conformant-array-specification')
        ]),
        '='
    );
    addElem(
        'value-conformant-array-specification',
        s([
            hTo2('identifier-list'),
            q(':'),
            hTo2('conformant-array-schema')
        ]),
        '='
    );
    addElem(
        'variable-conformant-array-specification',
        s([
            q('var'),
            hTo2('identifier-list'),
            q(':'),
            hTo2('conformant-array-schema')
        ]),
        '='
    );
    addElem(
        'conformant-array-schema',
        r([
            hTo2('packed-conformant-array-schema'),
            hTo2('unpacked-conformant-array-schema')
        ]),
        '='
    );
    addElem(
        'packed-conformant-array-schema',
        s([
            q('packed'),
            q('array'),
            q('['),
            hTo2('index-type-specification'),
            q(']'),
            q('of'),
            hTo2('type-identifier')
        ]),
        '='
    );
    addElem(
        'unpacked-conformant-array-schema',
        s([
            q('array'),
            q('['),
            hTo2('index-type-specification'),
            zRm(s([
                q(';'),
                hTo2('index-type-specification')
            ])),
            q(']'),
            q('of'),
            g(r([
                hTo2('type-identifier'),
                hTo2('conformant-array-schema')
            ]))
        ]),
        '='
    );
    addElem(
        'index-type-specification',
        s([
            hTo2('identifier'),
            q('..'),
            hTo2('identifier'),
            q(':'),
            hTo2('ordinal-type-identifier')
        ]),
        '='
    );
    addElem(
        'factor',
        hTo2('bound-identifier'),
        '>'
    );
    addElem(
        'bound-identifier',
        hTo2('identifier'),
        '='
    );
    closeSection();
    addSection('Expressions');
    addElem(
        'expression',
        s([
            hTo2('simple-expression'),
            zRo(
                s([
                    hTo2('relational-operator'),
                    hTo2('simple-expression')
                ])
            )
        ]),
        '='
    );
    addElem(
        'Boolean-expression',
        hTo2('expression'),
        '='
    );
    addElem(
        'simple-expression',
        s([
            zRo(hTo2('sign')),
            hTo2('term'),
            zRm(s([
                hTo2('adding-operator'),
                hTo2('term')
            ]))
        ]),
        '='
    );
    addElem(
        'term',
        s([
            'factor',
            zRm(s([
                hTo2('multiplying-operator'),
                hTo2('factor')
            ]))
        ]),
        '='
    );
    addElem(
        'factor',
        r([
            hTo2('variable-access'),
            hTo2('unsigned-constant'),
            hTo2('function-designator'),
            hTo2('set-constructor'),
            s([
                q('('),
                hTo2('expression'),
                q(')')
            ]),
            s([
                q('not'),
                hTo2('factor')
            ])
        ]),
        '>'
    );
    addElem(
        'unsigned-constant',
        r([
            hTo2('unsigned-number'),
            hTo2('character-string'),
            hTo2('constant-identifier'),
            q('nil')
        ]),
        '='
    );
    addElem(
        'set-constructor',
        s([
            q('['),
            zRo(s([
                hTo2('member-designator'),
                zRm(s([
                    q(','),
                    hTo2('member-designator')
                ]))
            ])),
            q(']')
        ]),
        '='
    );
    addElem(
        'member-designator',
        s([
            hTo2('expression'),
            zRo(s([
                q('..'),
                hTo2('expression')
            ]))
        ]),
        '='
    );
    closeSection();
    addSection('Function-designators');
    addElem(
        'function-designator',
        s([
            hTo2('function-identifier'),
            zRo(hTo2('actual-parameter-list'))
        ]),
        '='
    );
    addElem(
        'actual-parameter-list',
        s([
            q('('),
            hTo2('actual-parameter'),
            zRm(s([
                q(','),
                hTo2('actual-parameter')
            ])),
            q(')')
        ]),
        '='
    );
    addElem(
        'actual-parameter',
        r([
            hTo2('expression'),
            hTo2('variable-access'),
            hTo2('procedure-identifier'),
            hTo2('function-identifier')
        ]),
        '='
    );
    closeSection();
    addSection('Simple-statements');
    addElem(
        'simple-statement',
        r([
            hTo2('empty-statement'),
            hTo2('assignment-statement'),
            hTo2('procedure-statement')
        ]),
        '='
    );
    addElem(
        'empty-statement',
        '',
        '='
    );
    addElem(
        'assignment-statement',
        s([
            g(r([
                hTo2('variable-access'),
                hTo2('function-identifier')
            ])),
            q(':='),
            hTo2('expression')
        ]),
        '='
    );
    addElem(
        'procedure-statement',
        s([
            hTo2('procedure-identifier'),
            g(r([
                zRo(hTo2('actual-parameter-list')),
//                hTo2('read-parameter-list'),
//                hTo2('readln-parameter-list'),
//                hTo2('write-parameter-list'),
//                hTo2('writeln-parameter-list')
            ]))
        ]),
        '='
    );
    closeSection();
    addSection('Structured-statements');
    addElem(
        'structured-statement',
        r([
            hTo2('compound-statement'),
            hTo2('conditional-statement'),
            hTo2('repetitive-statement'),
            hTo2('with-statement')
        ]),
        '='
    );
    addElem(
        'statement-sequence',
        s([
            hTo2('statement'),
            zRm(s([
                q(';'),
                hTo2('statement')
            ]))
        ]),
        '='
    );
    addElem(
        'compound-statement',
        s([
            q('begin'),
            hTo2('statement-sequence'),
            q('end')
        ]),
        '='
    );
    addElem(
        'conditional-statement',
        r([
            hTo2('if-statement'),
            hTo2('case-statement')
        ]),
        '='
    );
    addElem(
        'if-statement',
        s([
            q('if'),
            hTo2('Boolean-expression'),
            q('then'),
            hTo2('statement'),
            zRo(hTo2('else-part'))
        ]),
        '='
    );
    addElem(
        'else-part',
        s([
            q('else'),
            hTo2('statement')
        ]),
        '='
    );
    addElem(
        'case-statement',
        s([
            q('case'),
            hTo2('case-index'),
            q('of'),
            hTo2('case-list-element'),
            zRm(s([
                q(';'),
                hTo2('case-list-element')
            ])),
            zRo(q(';')),
            q('end')
        ]),
        '='
    );
    addElem(
        'case-list-element',
        s([
            hTo2('case-constant-list'),
            q(';'),
            hTo2('statement')
        ]),
        '='
    );
    addElem(
        'case-index',
        hTo2('expression'),
        '='
    );
    addElem(
        'repetitive-statement',
        r([
            hTo2('repeat-statement'),
            hTo2('while-statement'),
            hTo2('for-statement')
        ]),
        '='
    );
    addElem(
        'repeat-statement',
        s([
            q('repeat'),
            hTo2('statement-sequence'),
            q('until'),
            hTo2('Boolean-expression')
        ]),
        '='
    );
    addElem(
        'while-statement',
        s([
            q('while'),
            hTo2('Boolean-expression'),
            q('do'),
            hTo2('statement')
        ]),
        '='
    );
    addElem(
        'for-statement',
        s([
            q('for'),
            hTo2('control-variable'),
            q(':='),
            hTo2('initial-value'),
            g(r([
                q('to'),
                q('downto')
            ])),
            hTo2('final-value'),
            q('do'),
            hTo2('statement')
        ]),
        '='
    );
    addElem(
        'control-variable',
        hTo2('entire-variable'),
        '='
    );
    addElem(
        'initial-value',
        hTo2('expression'),
        '='
    );
    addElem(
        'final-value',
        hTo2('expression'),
        '='
    );
    addElem(
        'with-statement',
        s([
            q('with'),
            hTo2('record-variable-list'),
            q('do'),
            hTo2('statement')
        ]),
        '='
    );
    addElem(
        'record-variable-list',
        s([
            hTo2('record-variable'),
            zRm(s([
                q(','),
                hTo2('record-variable')
            ]))
        ]),
        '='
    );
    addElem(
        'field-designator-identifier',
        hTo2('identifier'),
        '='
    );
    closeSection();
    addSection('Program');
    addElem(
        'program',
        s([
            hTo2('program-heading'),
            q(';'),
            hTo2('program-block'),
            q('.')
        ]),
        '='
    );
    addElem(
        'program-heading',
        s([
            q('program'),
            hTo2('identifier'),
            zRo(s([
                q('('),
                hTo2('program-parameter-list'),
                q(')')
            ]))
        ]),
        '='
    );
    addElem(
        'program-parameter-list',
        hTo2('identifier-list'),
        '='
    );
    addElem(
        'program-block',
        hTo2('block'),
        '='
    );
    addElem(
        'identifier-list',
        s([
            hTo2('identifier'),
            zRm(s([
                q(','),
                hTo2('identifier')
            ]))
        ]),
        '='
    );
    closeSection();




    document.getElementById('refs').innerHTML = refs;
    document.getElementById('main').innerHTML = main;
}