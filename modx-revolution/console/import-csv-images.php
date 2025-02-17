<?php
set_time_limit(120000000);
ini_set('memory_limit', -1);

define('PARENT_ID', 20); // parent catalog
define('TEMPLATE_ID', 3); // template for product
define('START_LIMIT', 1);
define('END_LIMIT', 1000);
define('FILE_PATH', MODX_ASSETS_PATH . 'data/data.csv');

// Pagetitle is mandatory.
// Write options of the form "option-nameOption"
// Write tv of the form "tv-name"
$importFields = [
    'pagetitle',
    'image',
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

function importImages($modx, $dataProducts, $parent, $template = '')
{
    global $modx;
    foreach ($dataProducts as $index => $data) {
        $pagetitle = $data['pagetitle'];
        $fileName = $data['image'];

        // Check if there is a product with the same pagetitle
        $existingProduct = $modx->getObject('msProduct', [
            'pagetitle' => $pagetitle,
            'parent' => $parent,
        ]);
        // If the product already exists, update its data
        if ($existingProduct !== null) {
            $id = $existingProduct->get('id');

            // import image
            $path = 'data/images/' . $fileName;
            setImages($modx, $id, $path);

            $existingProduct->save();
            // trigger the OnDocFormSave event
            $resource_temp = $modx->getObject('modResource', $id);
            $modx->invokeEvent('OnDocFormSave', array(
                'mode' => 'upd',
                'resource' => $resource_temp,
                'reloadOnly' => false
            ));

            echo "Product with id = $id updated" . PHP_EOL;
        }
    }
}

$data = parseCsvFile(FILE_PATH, START_LIMIT, END_LIMIT, ';', $importFields);
// var_dump($data);
importImages($modx, $data, PARENT_ID, TEMPLATE_ID);
