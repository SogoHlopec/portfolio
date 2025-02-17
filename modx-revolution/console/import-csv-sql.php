<?php
set_time_limit(120000000);
ini_set('memory_limit', -1);

define('PARENT_ID', 10); // parent catalog
define('TEMPLATE_ID', 3); // template for product
define('START_LIMIT', 0);
define('END_LIMIT', 1000);
define('FILE_PATH', MODX_ASSETS_PATH . 'data/data.csv');

// Pagetitle is mandatory.
// Write options of the form "option-nameOption"
// Write tv of the form "tv-name"
$importFields = [
    'pagetitle',
    'introtext',
    'longtitle',
    'content',
    'tv_id-1', // tv id
    // product options
    'option-option_name',
    'option-option_name',
];

// Connect to the database
$host = 'host';
$username = 'username';
$password = 'password';
$database = 'name_database';
$mysqli = mysqli_connect($host, $username, $password, $database);
if (!$mysqli) {
    die("Connection failed: " . mysqli_connect_error());
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

function setProductOptions($mysqli, $id, $data, $substringOption)
{
    $productOptions = [];
    foreach ($data as $key => $value) {
        if (strpos($key, $substringOption) !== false) {
            $optionName = str_replace($substringOption, '', $key);
            $value = $value !== null ? $value : '';
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

function setTvParam($mysqli, $contentId, $data, $substringTv)
{
    $productTvs = [];

    foreach ($data as $key => $value) {
        if (strpos($key, $substringTv) !== false) {
            $tmplvarId = (int)str_replace($substringTv, '', $key);
            $value = $value !== null ? $value : '';
            $productTvs[$tmplvarId] = str_replace(',', '.', $value);
        }
    }

    if (!empty($productTvs)) {
        foreach ($productTvs as $tmplvarId => $value) {
            $escapedValue = mysqli_real_escape_string($mysqli, $value);
            $insertQuery = "INSERT INTO modx_site_tmplvar_contentvalues (tmplvarid, contentid, value) 
                        VALUES ({$tmplvarId}, {$contentId}, '{$escapedValue}')";
            mysqli_query($mysqli, $insertQuery);
        }
    }
}

function setImages($modx, $id, $path)
{
    $data = [
        'id' => $id,
        'file' => $modx->getOption('base_path') . 'assets/' . $path,
    ];
    $uploadProcessor  = $modx->runProcessor('gallery/upload', $data, [
        'processors_path' => MODX_CORE_PATH . 'components/minishop2/processors/mgr/',
    ]);
}

function importProducts($modx, $mysqli, $dataProducts, $parent, $template = '')
{
    $substringOption = 'option-';
    $substringTv = 'tv_id-';

    mysqli_query($mysqli, "SET autocommit = 0");
    foreach ($dataProducts as $data) {
        mysqli_query($mysqli, "START TRANSACTION");
        $pagetitle = mysqli_real_escape_string($mysqli, $data['pagetitle']);
        $article = mysqli_real_escape_string($mysqli, $data['pagetitle']);
        $introtext = mysqli_real_escape_string($mysqli, $data['introtext']);
        $longtitle = mysqli_real_escape_string($mysqli, $data['longtitle']);
        $content = str_replace('\n', '<br/>', mysqli_real_escape_string($mysqli, $data['content'])); // save \n for html

        $alias = time();

        $insertResourceQuery = "INSERT INTO modx_site_content (parent, template, pagetitle, introtext, longtitle, alias, published, show_in_tree)
                                VALUES ({$parent}, {$template}, '{$pagetitle}', '{$introtext}', '{$longtitle}', '{$alias}', 1, 0)";
        mysqli_query($mysqli, $insertResourceQuery);
        $id = mysqli_insert_id($mysqli);
        echo "Generated ID: {$id}" . PHP_EOL;

        // set alias = id 
        $updateAliasQuery = "UPDATE modx_site_content SET alias = '{$id}' WHERE id = {$id}";
        mysqli_query($mysqli, $updateAliasQuery);
        $insertProductQuery = "INSERT INTO modx_ms2_products (id, article, source)
                                VALUES ({$id}, '{$article}', 2)";
        if (!mysqli_query($mysqli, $insertProductQuery)) {
            echo "Error inserting into ms2_products: " . mysqli_error($mysqli) . PHP_EOL;
            mysqli_query($mysqli, "ROLLBACK");
            continue;
        }
        setProductOptions($mysqli, $id, $data, $substringOption);
        setTvParam($mysqli, $id, $data, $substringTv);
        mysqli_query($mysqli, "COMMIT");

        $modx->cacheManager->refresh();

        $product = $modx->getObject('msProduct', [
            'id' => $id,
        ]);
        $product->fromArray([
            'alias' => $product->cleanAlias($pagetitle),
            'content' => $content,
        ]);
        $product->save();

        // import image
        // $path = 'data/images/' . $pagetitle . '.jpg';
        // setImages($modx, $id, $path);

        echo "Product {$id} successfully created" . PHP_EOL;
    }
    mysqli_query($mysqli, "SET autocommit = 1");
}

$data = parseCsvFile(FILE_PATH, START_LIMIT, END_LIMIT, ';', $importFields);
// var_dump($data);
importProducts($modx, $mysqli, $data, PARENT_ID, TEMPLATE_ID);
