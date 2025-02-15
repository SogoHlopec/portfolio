<?php
set_time_limit(120000000);
ini_set('memory_limit', -1);

define('PARENT_ID', 1);
define('LEVEL', 2);
$parent = $modx->getObject('modResource', PARENT_ID);
$parent->set('published', 1);
$parent->set('hidemenu', 0);
$parent->save();

// Get all resources
$resourcesIds = $modx->getChildIds(PARENT_ID, LEVEL, array('context' => 'web'));
$resources = $modx->getCollection('modResource', array('id:IN' => $resourcesIds));
foreach ($resources as $key => $resource) {
    $resource->set('published', 1);
    $resource->set('hidemenu', 0);
    $resource->save();
}
