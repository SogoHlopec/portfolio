<?php
set_time_limit(120000000);
ini_set('memory_limit', -1);
require_once MODX_CORE_PATH . 'components/importGoogleSheet/vendor/autoload.php';

define('SPREAD_SHEET_ID', 'YOUR_SHEET_ID'); // Google sheet ID
define('SHEET_TITLE', 'Title'); // Specify the name of the required Sheet in the table
define('PARENT_ID', 10); // parent catalog
define('TEMPLATE_ID', 1); // template for product
define('START_LIMIT', 0);
define('END_LIMIT', 1000);

// Pagetitle is mandatory. Write options of the form "option-nameOption"
$importFields = [
    'pagetitle',
    'option-one',
    'option-two',
];

// Connect to the database
$host = 'host';
$username = 'username';
$password = 'password';
$database = 'database';
$mysqli = mysqli_connect($host, $username, $password, $database);
if (!$mysqli) {
    die("Connection failed: " . mysqli_connect_error());
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

function setProductOptions($mysqli, $id, $data, $substringOption)
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
        $deleteQuery = "DELETE FROM modx_ms2_product_options WHERE product_id = {$id}";
        mysqli_query($mysqli, $deleteQuery);
        $insertValues = [];
        foreach ($productOptions as $optionName => $value) {
            $insertValues[] = "({$id}, '{$optionName}', '{$value}')";
        }

        if (!empty($insertValues)) {
            $insertQuery = "INSERT INTO modx_ms2_product_options (product_id, `key`, value) VALUES " . implode(',', $insertValues);
            mysqli_query($mysqli, $insertQuery);
        }
    }
}

function importProducts($modx, $mysqli, $dataProducts, $parent, $template = '')
{
    $substringOption = 'option-';

    mysqli_query($mysqli, "SET autocommit = 0");

    foreach ($dataProducts as $data) {
        mysqli_query($mysqli, "START TRANSACTION");
        $pagetitle = mysqli_real_escape_string($mysqli, $data['pagetitle']);
        $alias = time();

        $insertResourceQuery = "INSERT INTO iRsqBiZtmodx_site_content (parent, template, pagetitle, alias, published, show_in_tree)
                               VALUES ({$parent}, {$template}, '{$pagetitle}', '{$alias}', 1, 0)";
        mysqli_query($mysqli, $insertResourceQuery);

        $id = mysqli_insert_id($mysqli);

        $updateAliasQuery = "UPDATE iRsqBiZtmodx_site_content SET alias = '{$id}' WHERE id = {$id}";
        mysqli_query($mysqli, $updateAliasQuery);

        $insertProductQuery = "INSERT INTO iRsqBiZtmodx_ms2_products (id, source)
                               VALUES ({$id}, 2)";
        mysqli_query($mysqli, $insertProductQuery);

        setProductOptions($mysqli, $id, $data, $substringOption);

        mysqli_query($mysqli, "COMMIT");

        $product = $modx->getObject('msProduct', [
            'id' => $id,
        ]);
        $product->save();
        echo "Product {$id} successfully created" . PHP_EOL;
    }

    mysqli_query($mysqli, "SET autocommit = 1");
}

$data = parseGoogleSheet(START_LIMIT, END_LIMIT, $importFields);

importProducts($modx, $mysqli, $data, PARENT_ID, TEMPLATE_ID);

mysqli_close($mysqli);
