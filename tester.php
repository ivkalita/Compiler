#!/usr/bin/php
<?php
/**
 * Created by IntelliJ IDEA.
 * User: kaduev
 * Date: 26.09.14
 * Time: 12:20
 */

    function genHtml($indir, $outdir)
    {
        $trees = [];
        $src = [];
        $files = scandir($outdir . "trees/");
        foreach($files as $file) {
            if ($file[0] == '.') {
                continue;
            }
            $out = file_get_contents($outdir . "trees/$file");
            if (strpos($out, 'exception') !== false) {
                $out = str_replace("\n", "  ", str_replace("\"", "'", $out));
                $trees[] = '{"id":0, name:"' . $out . '"}';
            } else {
                $trees[] = $out;
            }
            $src[] = json_encode(explode("\n", file_get_contents($indir . str_replace("out", "pas", $file))));

        }
        $syntaxTree = '[' . implode(',', $trees) . ']';
        $src = '[' . implode(',', $src) . ']';
        $files = scandir($indir);
        // $syntaxTree       = file_get_contents($outdir . "trees/01.out");
        // $src              = file_get_contents($indir . "01.pas");
        $assetsPath       = "/home/kaduev13/Documents/Compiler/assets";
        $aceEditorPath    = $assetsPath . "/ace/ace.js";
        $baseCSSPath      = $assetsPath . "/Jit/base.css";
        $spaceTreeCSSPath = $assetsPath . "/Jit/Spacetree.css";
        $jitPath          = $assetsPath . "/Jit/jit.js";
        $excanvasPath     = $assetsPath . "/Jit/excanvas.js";
        $jsPath           = "main.js";
        $htmlPath         = "index.html";
        $js =
        "var labelType, useGradients, nativeTextSupport, animate, editor, srcs, trees, cur, st;
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
            trees = $syntaxTree;
            srcs = $src;
            st = new \$jit.ST({
                levelsToShow: 200,
                orientation: \"top\",
                injectInto: 'infovis',
                duration: 0,
                transition: \$jit.Trans.Quart.easeInOut,
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
                    Log.write(\"loading \" + node.name);
                },

                onAfterCompute: function(){
                    Log.write(\"done\");
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
                        node.data.\$color = \"#ff7\";
                    }
                    else {
                        delete node.data.\$color;
                        if(!node.anySubnode(\"exist\")) {
                            var count = 0;
                            node.eachSubnode(function(n) { count++; });
                            node.data.\$color = ['#aaa', '#baa', '#caa', '#daa', '#eaa', '#faa'][count];
                        }
                    }
                },

                onBeforePlotLine: function(adj){
                    if (adj.nodeFrom.selected && adj.nodeTo.selected) {
                        adj.data.\$color = \"#eed\";
                        adj.data.\$lineWidth = 3;
                    }
                    else {
                        delete adj.data.\$color;
                        delete adj.data.\$lineWidth;
                    }
                }
            });
            st.loadJSON(trees[0]);
            st.compute();
            st.onClick(st.root);
            editor = ace.edit(\"editor\");
            editor.setTheme(\"ace/theme/monokai\");
            editor.getSession().setMode(\"ace/mode/pascal\");
            cur = 0;
            switchToTest();
            document.getElementById(\"btnNext\").onclick = function(e) {
                cur++;
                switchToTest();
            }
            document.getElementById(\"btnPrev\").onclick = function(e) {
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
                editor.insert(elem + '\\n');
            });
        }";
        $html =
        "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
        <html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\" lang=\"en\">
        <head>
        <meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />
        <title>Tester</title>

        <!-- CSS Files -->
        <link type=\"text/css\" href=\"$baseCSSPath\" rel=\"stylesheet\" />
        <link type=\"text/css\" href=\"$spaceTreeCSSPath\" rel=\"stylesheet\" />

        <!--[if IE]><script language=\"javascript\" type=\"text/javascript\" src=\"$excanvasPath\"></script><![endif]-->

        <!-- JIT Library File -->
        <script language=\"javascript\" type=\"text/javascript\" src=\"$jitPath\"></script>

        <!-- Example File -->
        <script language=\"javascript\" type=\"text/javascript\" src=\"$jsPath\"></script>
        </head>

        <body onload=\"init();\">
        <div id=\"header\">
            <button id=\"btnPrev\">Предыдущий</button>
            <button id=\"btnNext\">Следующий</button>
        </div>
        <div id=\"container\">
        <div id=\"left-container\">
            <div id=\"editor\"></div>
            <script src=\"$aceEditorPath\" type=\"text/javascript\" charset=\"utf-8\"></script>
        </div>

        <div id=\"center-container\">
            <div id=\"log\"></div>
            <div id=\"infovis\"></div>
        </div>
        </div>
        </body>
        </html>";
        file_put_contents("$outdir/$htmlPath", $html);
        file_put_contents("$outdir/$jsPath", $js);
    }

    function fileCmpLex($out, $etalon)
    {
        if (!file_exists($etalon) || !file_exists($out)) {
            return false;
        }
        $fileOut = fopen($out, 'r');
        $fileEtalon = fopen($etalon, 'r');
        while (!feof($fileOut) && !feof($fileEtalon)) {
            $strOut = fgets($fileOut);
            $strEtalon = fgets($fileEtalon);
            $partsOut = explode('|', $strOut);
            $partsEtalon = explode('|', $strEtalon);
            if (count($partsEtalon) != count($partsOut)) {
                return false;
            }
            for ($i = 0; $i < count($partsOut); $i++) {
                if (trim($partsEtalon[$i]) != trim($partsOut[$i])) {
                    return false;
                }
            }
        }
        return feof($fileEtalon) && feof($fileOut);
    }

    function arrayCmp($ar1, $ar2)
    {
        $k1 = array_keys($ar1);
        $k2 = array_keys($ar2);
        foreach($k1 as $key) {
            if ($key == "id") {
                continue;
            }
            if (!array_key_exists($key, $ar2)) {
                return false;
            }
            if (is_array($ar1[$key])) {
                if (!is_array($ar2[$key])) {
                    return false;
                }
                if (!arrayCmp($ar1[$key], $ar2[$key])) {
                    return false;
                }
            } else {
                if ($ar1[$key] != $ar2[$key]) {
                    return false;
                }
            }
        }
        return true;
    }

    function fileCmpSyntax($out, $etalon)
    {
        // echo "outFile = $out\netalon = $etalon\n";
        // exit;
        if (!file_exists($etalon) || !file_exists($out)) {
            return false;
        }
        $treeOut = json_decode(file_get_contents($out), true);
        $treeEta = json_decode(file_get_contents($etalon), true);
        if ($treeOut == null || $treeEta == null) {
            return false;
        }
        return arrayCmp($treeEta, $treeOut);
    }

    function fileCmpSymbol($out, $etalon)
    {
        if (!file_exists($etalon) || !file_exists($out)) {
            return false;
        }
        $outText = file_get_contents($out);
        $etaText = file_get_contents($etalon);
        return trim($outText) == trim($etaText);
    }

    function testAll($in, $out, $etalon, $mode)
    {
        if ($mode == 2) {
            $root = $out;
            $out .= 'trees/';
        }
        @mkdir($out);
        $failedCnt = 0;
        $successCnt = 0;
        $inFiles = scandir($in);
        foreach($inFiles as &$inFile) {
            if ($inFile[0] == '.') {
                continue;
            }
            $outFile = str_replace("pas", "out", $inFile);
            $outFileFull = $out . $outFile;
            $inFileFull = $in . $inFile;
            $etalonFileFull = $etalon . $outFile;
            $switcher = [
            	1 => 'l',
                2 => 'S',
            	3 => 'T'
            ];
            try {
                $sysStr = "php ./compiler.php -f $inFileFull -o $outFileFull -" . $switcher[$mode];
                system($sysStr);
            } catch (\Exception $e) {
                echo "inFile:<$inFile>\noutFile:<$outFile>\n";
                die($e);
            }
            if ($mode == 1) {
                if (fileCmpLex($outFileFull, $etalonFileFull, $mode)) {
                    $successCnt++;
                    echo "$inFile...OK\n";
                } else {
                    $failedCnt++;
                    echo "$inFile...FAIL\n";
                }
            } else if ($mode == 2) {
                if (fileCmpSyntax($outFileFull, $etalonFileFull)) {
                    $successCnt++;
                    echo "$inFile...OK\n";
                } else {
                    $failedCnt++;
                    echo "$inFile...FAIL\n";
                }
            } else if ($mode == 3) {
                if (fileCmpSymbol($outFileFull, $etalonFileFull)) {
                    $successCnt++;
                    echo "$inFile...OK\n";
                } else {
                    $failedCnt++;
                    echo "$inFile...FAIL\n";
                }
            }
        }
        echo "SUCCESSED/FAILED\n";
        echo "$successCnt/$failedCnt\n";
    }

    //argv = [0, in_dir, out_dir, etalon_dir, mode]
    //mode: 1 -> lex
    //      2 -> syntax
    //      3 -> tables
    testAll($argv[1], $argv[2], $argv[3], $argv[4]);
    if ($argv[4] == 2) {
        genHtml($argv[1], $argv[2]);
    }