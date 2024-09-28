<?php
ini_set('max_execution_time', 0);
$q = $modx->newQuery('modResource');

echo "<table style='width:100%'>";
foreach ($modx->getCollection('modResource', $q) as $resource) {
    echo "<tr>";
    // Defining a new Alias
    $title = $resource->get('pagetitle');
    $new_alias = $resource->cleanAlias($title);
    $resource->set('alias', $new_alias);

    $response = $modx->runProcessor('resource/update', $resource->toArray());
    if ($response->isError()) {
        echo "<td>С обновлением ресурса проблемы:<pre>";
        print_r($response_ar->getResponse() . true);
        echo "</pre></td>";
        return;
    } else {
        echo "<td>Ок! " . $resource->get('id') . " </td>";
        echo "<td> " . $new_alias . " </td>";
    }
}
echo "</table>";
