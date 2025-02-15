<?php
set_time_limit(120000000);
ini_set('memory_limit', -1);

require_once MODX_CORE_PATH . 'components/importGoogleSheet/vendor/autoload.php';

define('SPREAD_SHEET_ID', 'YOUR_SHEET_ID'); // Google sheet ID
define('SHEET_TITLE', 'Title'); // Specify the name of the required Sheet in the table
define('PARENT_ID', 10); // parent catalog
define('TEMPLATE_ID', 1); // template for product
define('START_LIMIT', 0);
define('END_LIMIT', 100);

// Pagetitle is mandatory. Write options of the form "option-nameOption"
$importFields = [
    'pagetitle',
    'option-one',
    'option-two',
];

function parseGoogleSheet($startLimit, $endLimit, $importFields = [])
{
    // Service account access key
    // $googleAccountKeyFilePath = './google-api-key.json';
    $googleAccountKeyFilePath = MODX_CORE_PATH . 'components/importGoogleSheet/google-api-key.json';


    putenv('GOOGLE_APPLICATION_CREDENTIALS=' . $googleAccountKeyFilePath);
    $client = new Google_Client();
    $client->useApplicationDefaultCredentials();
    $client->setScopes(Google\Service\Sheets::SPREADSHEETS);
    $service = new Google\Service\Sheets($client);

    $values = [];

    $response = $service->spreadsheets->get(SPREAD_SHEET_ID);
    if ($response) {
        $sheets = $response->getSheets();

        foreach ($sheets as $index => $sheet) {
            if ($sheet->getProperties()->title === SHEET_TITLE) {
                // Retrieving the contents of an entire sheet by its name
                $response = $service->spreadsheets_values->get(SPREAD_SHEET_ID, SHEET_TITLE);
                $values = $response['values'];
            }
        }
        if (empty($values)) {
            return "Error: no table data " . SHEET_TITLE;
        }

        $result = [];
        foreach ($values as $rowIndex => $data) {
            if ($rowIndex === 0 || $rowIndex < $startLimit) {
                continue;
            } elseif ($rowIndex > $endLimit) {
                break;
            } else {
                $rowResult = [];
                foreach ($data as $index => $value) {
                    if (isset($importFields[$index])) {
                        $value = trim($value);
                        $rowResult[$importFields[$index]] = $value;
                    }
                }
                $result[] = $rowResult;
            }
        }
        return $result;
    }
}

function setProductOptions($modx, $id, $data, $substringOption)
{

    $productOptions = [];
    foreach ($data as $key => $value) {
        if (strpos($key, $substringOption) !== false) {
            $optionName = str_replace($substringOption, '', $key);
            $value = $value !== null ? $value : 'no';
            $productOptions[$optionName] = str_replace(',', '.', $value);
        }
    }

    if (!empty($productOptions)) {
        $tableName = $modx->getTableName('msProductOption');
        $placeholders = implode(',', array_fill(0, count($productOptions), '?'));
        $modx->exec("DELETE FROM {$tableName} WHERE `product_id` = {$id} AND `key` IN ({$placeholders})", array_keys($productOptions));

        foreach ($productOptions as $optionName => $value) {
            $newOptionProduct = $modx->newObject('msProductOption', [
                'product_id' => $id,
                'key' => $optionName,
                'value' => $value
            ]);
            $newOptionProduct->save();
        }
    }
    
}

function importProducts($modx, $dataProducts, $parent, $template = '')
{
    $substringOption = 'option-';
    $modx->getCacheManager()->refresh(['auto_publish' => false]);

    foreach ($dataProducts as $index => $data) {
        $pagetitle = $data['pagetitle'];
        $alias = time();

        // Product Creation
        $newProduct = $modx->newObject('msProduct');
        $newProduct->fromArray([
            'parent' => $parent,
            'template' => $template,
            'pagetitle' => $pagetitle,
            'alias' => $alias,
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
            print_r($newProduct->getErrors());
        }
    }
    $modx->getCacheManager()->refresh(['auto_publish' => true]);
}

$data = parseGoogleSheet(START_LIMIT, END_LIMIT, $importFields);

importProducts($modx, $data, PARENT_ID, TEMPLATE_ID);
