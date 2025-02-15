<?php
set_time_limit(120000000);
ini_set('memory_limit', -1);

require_once MODX_CORE_PATH . 'components/importGoogleSheet/vendor/autoload.php';

define('SPREAD_SHEET_ID', 'YOUR_SHEET_ID'); // Google sheet ID
define('SHEET_TITLE', 'Name'); // Specify the name of the required Sheet in the table
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

function importProducts($modx, $dataProducts, $parent, $template = '')
{
    $substringOption = 'option-';

    foreach ($dataProducts as $index => $data) {
        $pagetitle = $data['pagetitle'];

        // Check if there is a product with the same pagetitle
        $existingProduct = $modx->getObject('msProduct', [
            'pagetitle' => $pagetitle,
        ]);
        // If the product already exists, update its data
        if ($existingProduct) {
            $id = $existingProduct->get('id');

            $existingProduct->fromArray([
                'pagetitle' => $pagetitle,
                'alias' => $existingProduct->cleanAlias($pagetitle),
                'category' => $parent
            ]);
            $existingProduct->save();

            // Trigger the OnDocFormSave event
            $modx->invokeEvent('OnDocFormSave', [
                'mode' => 'upd',
                'resource' => $existingProduct,
                'reloadOnly' => false
            ]);

            // Setting product options
            setProductOptions($modx, $id, $data, $substringOption);

            echo "Product with id = $id updated" . PHP_EOL;
        } else {
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

                // Trigger the OnDocFormSave event
                $modx->invokeEvent('OnDocFormSave', array(
                    'mode' => 'upd',
                    'resource' => $newProduct,
                    'reloadOnly' => false
                ));

                // Setting product options
                setProductOptions($modx, $id, $data, $substringOption);

                echo "Product $id successfully created" . PHP_EOL;
            } else {
                print_r($newProduct->getErrors());
            }
        }
    }
}

$data = parseGoogleSheet(START_LIMIT, END_LIMIT, $importFields);

importProducts($modx, $data, PARENT_ID, TEMPLATE_ID);
