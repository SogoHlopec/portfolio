<?php
// Generate html products from a json file
<?php
$resourceId = $resourceId;
$start = $start ? $start : 0;
$limit = $limit ? $limit : 250;
$step = $step ? $step : 0;

function renderProductsResourceId115($data)
{
    $output = '';
    foreach ($data as $index => $item) {
        $number = $item[0];
        $title = $item[1];
        $unitOfMeasurement = $item[2];
        $quantity = $item[3];

        $product = '<div class="product masonry">
                <div class="column"><p>' . $number . '.</p></div>
                <div class="column column__title"><p>' . $title . '</p></div>
                <div class="column column__units-of-measurement"><p>' . $quantity . ' ' . $unitOfMeasurement . '</p></div>
            </div>';

        $output .= $product;
    }
    return $output;
}

function renderProductsResourceId116($data)
{
    $output = '';
    foreach ($data as $index => $item) {
        $number = $item[0];
        $title = $item[1];
        $img = $item[2];

        $product = '<div class="product masonry">
                <div class="column"><p>' . $number . '.</p></div>';
        if ($img !== '') {
            $product .= '<div class="column column__image">
                <a class="light-image" href="' . $img . '" data-pswp-width="495" data-pswp-height="325">
                    <img src="' . $img . '">
                </a>
            </div>';
        }

        $product .= '<div class="column column__title"><p>' . $title . '</p></div>
            </div>';

        $output .= $product;
    }
    return $output;
}

$resources = [
    '115' => 'renderProductsResourceId115',
    '116' => 'renderProductsResourceId116',
];

try {
    define('DATA_FOLDER', MODX_CORE_PATH  . 'pathToData');

    $file_name = $resourceId . '.json';
    $filaPath = DATA_FOLDER . '/' . $file_name;

    $json = file_get_contents($filaPath);
    $data = json_decode($json);
    $productQuantity = count($data);

    $result = '';
    if ($productQuantity <= $limit) {
        $result .= '<div class="products masonry">';
        if (isset($resources[$resourceId])) {
            $function = $resources[$resourceId];
            $result .= $function($data);
        }
    } else if ($start > 0) {
        $dataSlice = array_slice($data, $start, $limit);
        if (isset($resources[$resourceId])) {
            $function = $resources[$resourceId];
            $result .= $function($dataSlice);
            $nextStep = $start + $limit >= $productQuantity ? 0 : $step + 1;
            return json_encode([
              'result' => $result,  
              'step' => $nextStep,
            ]);
        }
    } else {
        $dataSlice = array_slice($data, $start, $limit);
        $result = '<div class="products masonry">';
        if (isset($resources[$resourceId])) {
            $function = $resources[$resourceId];
            $result .= $function($dataSlice);
        }
        $step = ($productQuantity / $limit) > 1 ? 1 : 0;
        $result .= '</div><button class="btn btn-show-more" data-step="' . $step . '" data-resource-id="' . $resourceId . '">Загрузить ещё</button>';
    }
    return $result;
} catch (Exception $e) {
    echo 'Error: ',  $e->getMessage(), "\n";
}