<?php
// Chunk output depending on the presence of child resources
$children = $modx->getCollection('modResource', array(
    'parent' => $modx->resource->get('id'),
    'deleted' => false,
    'published' => true
));

if (!empty($children)) {
    echo '[[$tplPageCategory]]';
} else {
    echo '[[$tplPageService]]';
}