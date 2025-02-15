<?php
set_time_limit(120000000);
ini_set('memory_limit', -1);

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
            if ($value === null) {
                $value = 'no';
            }
            setOption($modx, $id, $optionName, $value);
        }
    }
}

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

                $rowResult = [];
                foreach ($data as $index => $value) {
                    if (isset($importFields[$index])) {
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

function importProducts($modx, $dataProducts, $parent, $template = '')
{
    $substringOption = 'option-';

    foreach ($dataProducts as $index => $data) {
        $pagetitle = 'Product title ' . $data['option-one'] . ' · ' . $data['option-two'];

        // Product Creation
        $newProduct = $modx->newObject('msProduct');

        $newProduct->fromArray([
            'parent' => $parent,
            'template' => $template,
            'pagetitle' => $pagetitle,
            'alias' => $newProduct->cleanAlias($pagetitle),
            'category' => $parent,
            'published' => 1,
            'show_in_tree' => 0
        ]);

        // Save of resource
        if ($newProduct->save()) {
            $id = $newProduct->get('id');

            // Setting product options
            setProductOptions($modx, $id, $data, $substringOption);

            echo "Product $id successfully created" . PHP_EOL;
        } else {
            echo "Product error" . PHP_EOL;
            print_r($newProduct->getErrors());
            break;
        }
    }
}

define('PARENT_ID', 10);
define('TEMPLATE_ID', 1);
define('START_LIMIT', 0);
define('END_LIMIT', 1000);
// $filePath = __DIR__ . '/testData.csv';
$filePath =  MODX_ASSETS_PATH . 'data/data.csv';

// Pagetitle is mandatory. Write options of the form "option-nameOption"
$importFields = [
    'option-one',
    'option-two',
];

$data = parseCsvFile($filePath, START_LIMIT, END_LIMIT, ';', $importFields);

importProducts($modx, $data, PARENT_ID, TEMPLATE_ID);
