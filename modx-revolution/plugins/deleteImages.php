<?php
switch ($modx->event->name) {
    case 'OnBeforeEmptyTrash':
        // Get all resources
        $resources = $modx->getCollection('modResource', array('id:IN' => $ids));
        $croppedDirectory = 'assets/images/scriptCrop/';

        foreach ($resources as $key => $resource) {
            $scr1 = $resource->getTVValue('scr1');
            $scr2 = $resource->getTVValue('scr2');

            if (!empty($scr1)) {
                $fullPathImage = MODX_BASE_PATH . $scr1;

                $hash = md5_file($fullPathImage);
                $newFullName = $hash . '.' . 'webp';
                $fullPathCroppedImage = MODX_BASE_PATH . $croppedDirectory . $newFullName;

                unlink($fullPathCroppedImage);
                unlink($fullPathImage);
            }
            if (!empty($scr2)) {
                $fullPathImage = MODX_BASE_PATH . $scr2;

                $hash = md5_file($fullPathImage);
                $newFullName = $hash . '.' . 'webp';
                $fullPathCroppedImage = MODX_BASE_PATH . $croppedDirectory . $newFullName;

                unlink($fullPathCroppedImage);
                unlink($fullPathImage);
            }
        }
        break;
    default:
        break;
}
