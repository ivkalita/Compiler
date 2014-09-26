<?php
    namespace vendor\Utility;

    class Console
    {
        static public $output;
        static public function parseArgs()
        {
            $params = [
                ''    => 'help',
                'h::' => 'help',
                'f:'  => 'file',
                'o:'  => 'output'
            ];
            $help = '';
            $errors = [];
            $filename  = '';
            $options = getopt(implode('', array_keys($params)), $params);
            if (isset($options['help']) || isset($options['h']) || count($errors)) {
                $help = "
Usage: php compiler.php [-help|--h] [-f|--file=filename] [-o|--output=outfile]

Options:
    -h   --help   Show this message
    -f   --file   Provide source filename
    -o   --output Provide output filename

Example:
    php compiler.php -f source.pas
    php compiler.php -f source.pas -o output.out
";
            }
            if (isset($options['file']) || isset($options['f'])) {
                $filename = isset($options['f']) ? $options['f'] : $options['file'];
                if (!file_exists($filename)) {
                    $errors[] = 'input file does not exists';
                } else if (is_dir($filename)) {
                    $errors[] = 'you should provide regular file';
                }
            } else if (!$help) {
                $errors[] = 'filename required';
            }
            if ($errors) {
                $help .= "Errors:\n" . implode("\n", $errors) . "\n";
            }
            if ($help) {
                die($help);
            }
            if (isset($options['o']) || isset($options['output'])) {
                self::$output = fopen(isset($options['o']) ? $options['o']: $options['output'], 'w');
            }
            return [
                'filename' => $filename
            ];
        }

        static public function write($text)
        {
            if (self::$output != null) {
                fwrite(self::$output, $text);
            } else {
                echo $text;
            }
        }

        static public function closeStream()
        {
            if (self::$output != null) {
                fclose(self::$output);
                self::$output = null;
            }
        }

    }