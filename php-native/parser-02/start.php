<?php
set_time_limit(120000);
ini_set('memory_limit', -1);

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/components/parser.php';
require_once __DIR__ . '/components/saveToGoogleTable.php';

$urls = [
    'categoryName' => [
        'subcategory_name' => 'pageUrl',
    ],
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
            $data = $parser($url, $key, $productCounter);
            $result[$section][$key] = $data;
        }
    }

    // Write the array to a file
    $text = serialize($result);
    file_put_contents('dataProduct.txt', $text);

    // Getting data from the file
    // $text = file_get_contents('dataProduct.txt');
    // $result = unserialize($text);`

    // var_dump($result);
    // readGoogleSheet();

    // Save to google sheets
    $sheetIndex = 0;
    // $sheetIndex = 5;
    $sheetTitle = '';
    foreach ($result as $section => $category) {
        $sheetTitle = $section;
        $rowIndex = '2';
        // $rowIndex = '68';
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
