#!/usr/bin/php
<?php
    require_once('core/SplClassLoader.php');
    $autoloader = new SplClassLoader('vendor', 'src');
    $autoloader->register();

    use vendor\TokenParser\Scanner;
    use vendor\Utility\Console;
    use vendor\Exception\CompilerException;

//    $console = new Console();
    $params = Console::parseArgs();
    $scanner = new Scanner(file_get_contents($params['filename']));
    $tokens = [];
    try {
        while ($scanner->next()) {
            $tokens[] = $scanner->get();
        }
    } catch (CompilerException $e) {
        Console::write($e);
        exit;
    }
    foreach ($tokens as &$token) {
        Console::write($token->getStr());
    }
    Console::closeStream();