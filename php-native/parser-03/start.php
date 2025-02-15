<?php
set_time_limit(120000);
ini_set('memory_limit', -1);

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/components/parser.php';
require_once __DIR__ . '/components/saveToGoogleTable.php';

$urls = [
    'site-name' => [
        'category-name' => 'https://url',
    ],
];

$parsers = [
    'category-name' => 'function-name',
];

$result = [];

try {
    echo "Start parsing...\n";
    setLog("Start parsing...", 'h1');

    // Set a lock on the file for the script to run in 1 process only
    $lockfile = __DIR__ . '/lockfile.lock';
    $fp = fopen($lockfile, 'w');
    if (!flock($fp, LOCK_EX | LOCK_NB)) {
        exit("The script is already running\n");
    }

    foreach ($urls as $section => $category) {
        echo "$section\n";
        setLog("$section", 'h3');

        foreach ($category as $key => $url) {
            $productCounter = 0;
            $data = [];
            if (isset($parsers[$key])) {
                $functionParser = $parsers[$key];
                $data = $functionParser($url, $key, $productCounter);
            }
            $result[$section][$key] = $data;
        }
    }
    // Save to google sheets
    $sheetIndex = 0;
    $sheetTitle = '';
    foreach ($result as $section => $category) {
        $sheetTitle = $section;
        $rowIndex = '2';
        foreach ($category as $key => $productsData) {
            $rowIndex = writeGoogleSheet($productsData, $sheetIndex, $sheetTitle, $rowIndex, $key);
            var_dump($rowIndex);
        }
        $sheetIndex++;
    }

    echo "End parsing\n";
    setLog("End parsing...", 'h1');
} catch (Exception $e) {
    echo 'Error: ',  $e->getMessage(), "\n";
    setLog($e->getMessage(), 'h3');
}
