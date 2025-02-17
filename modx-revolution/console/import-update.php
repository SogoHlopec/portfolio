<?php
set_time_limit(120000000);
ini_set('memory_limit', -1);

define('PARENT_ID', 10); // parent catalog
define('TEMPLATE_ID', 3); // template for product
define('START_LIMIT', 1);
define('END_LIMIT', 1000);
define('FILE_PATH', MODX_ASSETS_PATH . 'data/data.csv');

// Pagetitle is mandatory.
// Write options of the form "option-nameOption"
// Write tv of the form "tv-name"
$importFields = [
    'pagetitle',
    'content',
    'tv_name',
    // 'introtext',
    // 'longtitle', 
    // 'option-option_name', // product option
];

function parseCsvFile($filePath, $startLimit, $endLimit, $delimetr = ';', $importFields = [])
{
    if (!file_exists($filePath) || !is_readable($filePath)) {
        echo "Unable to open file.";
        return [];
    }
    $file = fopen($filePath, 'r');
    $result = [];
    $row = 0;
    if ($file !== false) {
        while (($data = fgetcsv($file, 0, $delimetr)) !== false) {
            if ($row === 0 || $row < $startLimit) {
                $row++;
                continue;
            } elseif ($row > $endLimit) {
                break;
            } else {
                // var_dump($data);
                $rowResult = [];
                foreach ($data as $index => $value) {
                    if (isset($importFields[$index])) {
                        $value = str_replace(["\r\n", "\n", "\r"], "\n", $value);
                        $rowResult[$importFields[$index]] = $value;
                    }
                }
                $result[] = $rowResult;
                $row++;
            }
        }
        fclose($file);
        return $result;
    } else {
        echo "Unable to open file.";
    }
}

function setOption($modx, $id, $optionName, $value)
{
    $value = str_replace(',', '.', $value);
    $msProductData = $modx->getObject('msProductData', $id);
    if ($msProductData) {
        // Set the option to the product
        // Clean the old data. Do SQL, because there is no primary key in the table
        $tableName = $modx->getTableName('msProductOption');
        $statement = $modx->prepare("DELETE FROM {$tableName} WHERE `product_id` = :product_id AND `key` = :key");
        $statement->execute([':product_id' => $id, ':key' => $optionName]);
        // Adding a value to the database
        $newOptionProduct = $modx->newObject('msProductOption', [
            'product_id' => $id,
            'key' => $optionName,
            'value' => $value
        ]);
        $newOptionProduct->save();
    }
}

function setProductOptions($modx, $id, $data, $substringOption)
{
    foreach ($data as $key => $value) {
        if (strpos($key, $substringOption) !== false) {
            $optionName = str_replace($substringOption, '', $key);
            $value = $value;
            $value = $value !== null ? $value : '';
            setOption($modx, $id, $optionName, $value);
        }
    }
}

function importProducts($modx, $dataProducts, $parent, $template = '')
{
    global $modx;
    // $substringOption = 'option-';
    foreach ($dataProducts as $index => $data) {
        $pagetitle = $data['pagetitle'];
        $content = $data['content'];
        $tv_name_1 = $data['tv_name_1'];
        // Check if there is a product with the same pagetitle
        $existingProduct = $modx->getObject('msProduct', [
            'pagetitle' => $pagetitle,
            'parent' => $parent,
        ]);
        // If the product already exists, update its data
        if ($existingProduct !== null) {
            $id = $existingProduct->get('id');
            $existingProduct->set('content', $content);
            $existingProduct->setTVValue('tv_name_1', $tv_name_1);
            $existingProduct->save();
            // trigger the OnDocFormSave event
            $resource_temp = $modx->getObject('modResource', $id);
            $modx->invokeEvent('OnDocFormSave', array(
                'mode' => 'upd',
                'resource' => $resource_temp,
                'reloadOnly' => false
            ));
            // Setting product options
            // setProductOptions($modx, $id, $data, $substringOption);

            echo "Product with id = $id updated" . PHP_EOL;
        }
    }
}

$data = parseCsvFile(FILE_PATH, START_LIMIT, END_LIMIT, ';', $importFields);
// var_dump($data);
importProducts($modx, $data, PARENT_ID, TEMPLATE_ID);
