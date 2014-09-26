<?php
/**
 * Created by IntelliJ IDEA.
 * User: kaduev
 * Date: 26.09.14
 * Time: 12:20
 */
    function fileCmp($out, $etalon)
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
        return feof($fileEtalon) && feof($fileEtalon);
    }

    function testAll($in, $out, $etalon)
    {
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
            try {
                system("php ./compiler.php -f $inFileFull -o $outFileFull");
            } catch (\Exception $e) {
                echo "inFile:<$inFile>\noutFile:<$outFile>\n";
                die($e);
            }
            if (fileCmp($outFileFull, $etalonFileFull)) {
                $successCnt++;
                echo "$inFile...OK\n";
            } else {
                $failedCnt++;
                echo "$inFile...FAIL\n";
            }
        }
        echo "SUCCESSED/FAILED\n";
        echo "$successCnt/$failedCnt\n";
    }


    testAll($argv[1], $argv[2], $argv[3]);