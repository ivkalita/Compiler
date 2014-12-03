#!/usr/bin/php
<?php
    require_once(__DIR__ . '/core/SplClassLoader.php');

    $autoloader = new SplClassLoader('vendor', __DIR__ . '/src');
    $autoloader->register();

    use vendor\TokenParser\Scanner;
    use vendor\Utility\Console;
    use vendor\Exception\CompilerException;
    use vendor\SyntaxParser\Nodes\Program;
    use vendor\Visualisator\TreeViewer;

    $params = Console::parseArgs();
    $scanner = new Scanner(file_get_contents($params['filename']));
    $tokens = [];
    if ($params['lex']) {
        try {
            while ($scanner->next()) {
                $tokens[] = $scanner->get();
            }
        } catch (CompilerException $e) {
            Console::write($e);
            Console::closeStream();
            exit;
        }
        foreach ($tokens as &$token) {
            Console::write($token->getStr());
        }
    } else {
        try {
            $program = Program::parse($scanner);
        } catch (CompilerException $e) {
            Console::write($e);
            Console::closeStream();
            exit(1);
        }
        if ($params['html']) {
            $syntaxTree = json_encode($program->toIdArray());
            // echo $syntaxTree;
            TreeViewer::genSyntaxHtml($params['html'], "result", $syntaxTree);
        } else {
            $syntaxTree = $program->toIdArray();
            Console::write(json_encode($syntaxTree));
        }
    }
    Console::closeStream();

