<?php
set_time_limit(120000000);
ini_set('memory_limit', -1);

$counter = 1;
$startLimiter = 0;
$endLimiter = 500;
$parentId = 10;

$children = $modx->getCollection('modResource', array(
    'parent' => $parentId,
));

echo 'Total ' . count($children) . PHP_EOL;

if (!empty($children)) {
    foreach ($children as $key => $resource) {
        if ($counter < $startLimiter) {
            $counter++;
            continue;
        } elseif ($counter > $endLimiter) {
            echo 'Finish';
            return;
        } else {

            $id = $resource->get('id');
            $resource->set('alias', $id);
            $response = $modx->runProcessor('resource/update', $resource->toArray());

            if ($response->isError()) {
                echo "$counter) Error $id" . PHP_EOL;
                print_r($response->isError());
                return;
            } else {
                echo "$counter) $id Ок, new alias " . $id . PHP_EOL;
            }

            $counter++;
        }
    }
}

// OR SQL update alias
// UPDATE iRsqBiZtmodx_site_content
// SET alias = id, uri = id
// WHERE class_key = 'msProduct';