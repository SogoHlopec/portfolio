<?php
set_time_limit(120000);
ini_set('memory_limit', -1);

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/components/parser.php';
require_once __DIR__ . '/components/saveToYml.php';

try {
    $url = 'https://';

    echo "Start parsing...\n";
    setLog('Start parsing...', 'h1');

    // Set a lock on the file for the script to run in 1 process only
    $lockfile = __DIR__ . '/lockfile.lock';
    $fp = fopen($lockfile, 'w');
    if (!flock($fp, LOCK_EX | LOCK_NB)) {
        exit("The script is already running\n");
    }

    // Parsing of products
    if (!empty($argv[1])) {
        $productSkipCounter = $argv[1];
        parsingProducts($url, $productSkipCounter);
    } else {
        parsingProducts($url);
    }

    echo "End Parsing \n";
    setLog('End Parsing', 'h3');

    echo "Save to table \n";
    setLog('Save to table', 'h3');
    $dataCategories = [];
    $dataProducts = [];
    
    echo round(memory_get_usage() / 1024 / 1024, 4) . ' MB' . PHP_EOL;
    setLog(round(memory_get_usage() / 1024 / 1024, 4) . ' MB' . PHP_EOL, 'p');

    saveToYml($dataCategories, $dataProducts);

    echo "End save to table";
    setLog('End save to table', 'h3');

    flock($fp, LOCK_UN);
    fclose($fp);
} catch (Exception $e) {
    echo 'Error: ',  $e->getMessage(), "\n";
    setLog($e->getMessage(), 'h3');
}
