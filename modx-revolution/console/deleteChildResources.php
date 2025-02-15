<?php
set_time_limit(120000000);
ini_set('memory_limit', -1);

define('PARENT_ID', 10); // parent resource id

$resources = $modx->getIterator('modResource', array('parent' => PARENT_ID));
foreach ($resources as $resource) {
    $resource->remove();
    echo 'Deleted resource id' . $resource->get('id') . PHP_EOL;
}
