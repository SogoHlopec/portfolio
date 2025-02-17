<?php
set_time_limit(120000000);
ini_set('memory_limit', -1);

define('PARENT_ID', 10); // parent catalog
define('START_LIMIT', 0);
define('END_LIMIT', 1000);
define('FOLDER_PATH', MODX_CORE_PATH . 'export/');
define('FILE_NAME', 'test.csv');
define('FIRST_ROW_CSV', [
    'id',
    'Article',
    'Name',
    'Full name',
]);

// Write options of the form "option-nameOption"
// Write tv of the form "tv-tvName"
define('EXPOSRT_FIELDS', [
    'id',
    'pagetitle',
    'longtitle',
    // 'content',
    // 'introtext',
    // 'tv-tv_name', 
    // 'option-option_name', 
]);

function prepareFile($filePath)
{
    if (!file_exists(FOLDER_PATH)) {
        mkdir(FOLDER_PATH, 0777, true);
    }

    if (file_exists($filePath)) {
        file_put_contents($filePath, ''); // Clear the file if it exists
    }

    $file = fopen($filePath, 'w');
    if (!$file) {
        die("Failed to create the file: $filePath");
    }

    // Add BOM for UTF-8 to support Cyrillic in Excel
    fwrite($file, "\xEF\xBB\xBF");

    fputcsv($file, FIRST_ROW_CSV, ';');
    fclose($file);
}

function getLocalizedField($modx, $resourceId, $locale, $field)
{
    $localization = $modx->getObject('localizatorContent', [
        'key' => $locale,
        'resource_id' => $resourceId,
    ]);

    return $localization ? $localization->get($field) : '';
}

function extractProductData($modx, $product, $exportFields)
{
    $row = [];

    foreach ($exportFields as $field) {
        if (stripos($field, 'tv-') === 0) {
            $tvName = str_replace('tv-', '', $field);
            $row[] = $product->getTVValue($tvName) ?: '';
        } elseif (stripos($field, 'option-') === 0) {
            $optionName = str_replace('option-', '', $field);
            $row[] = $product->get($optionName . '.value') ?: '';
        } else {
            $row[] = $product->get($field) ?: '';
        }
    }

    // Add localized data (EN longtitle)
    $row[] = getLocalizedField($modx, $product->get('id'), 'en', 'longtitle');

    return $row;
}

function exportProducts($modx, $startLimit, $endLimit,  $parent, $exportFields, $fileName = 'data.csv')
{
    global $modx;

    $filePath = FOLDER_PATH . $fileName;
    prepareFile($filePath);
    $file = fopen($filePath, 'a');
    if (!$file) {
        die("Failed to open the file for writing: $filePath");
    }

    $products = $modx->getCollection('modResource', array(
        'parent' => $parent,
    ));

    $count = 0;
    foreach ($products as $index => $product) {
        $count++;
        if ($count === 0 || $count < $startLimit) {
            continue;
        } elseif ($count > $endLimit) {
            break;
        } else {
            $row = extractProductData($modx, $product, $exportFields);
            fputcsv($file, $row, ';');
        }
    }
    fclose($file);
    echo "Export is complete. The data has been saved to: $filePath";
}

exportProducts($modx, START_LIMIT, END_LIMIT, PARENT_ID, EXPOSRT_FIELDS, FILE_NAME);
