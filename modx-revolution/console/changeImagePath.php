<?php
setlocale(LC_ALL, 'ru_RU.utf8'); // fix for wrong servers, otherwise patchinfo will not work with Cyrillic alphabet.
// Get all resources
$resourcesIds = $modx->getChildIds('0', '10', array('context' => 'web'));
$resources = $modx->getCollection('modResource', array('id:IN' => $resourcesIds));

foreach ($resources as $resource) {
    $id = $resource->get('id');
    $src = $resource->getTVvalue('scr') ?: '';

    echo $id . PHP_EOL;
    if ($src !== '') {
        $src = explode('assets/media/', $src)[1];

        $fullPath = MODX_BASE_PATH . $src;
        if (file_exists($fullPath)) {
            $path = pathinfo($fullPath);
            $hashFile = md5_file($fullPath);

            $newFileName = $hashFile . '.' . 'jpg';
            $newFullPath = MODX_BASE_PATH . 'assets/media/' . $newFileName;

            if ($hashFile) {
                copy($fullPath, $newFullPath);
            }

            $resource->setTVValue('src', $newFileName);
            $resource->save();
        }
    }
}
