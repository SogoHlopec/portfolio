<?php
switch ($modx->event->name) {
    case 'OnBeforeDocFormSave':
        if ($mode == 'new' && ($resource->get('class_key') === 'msProduct')) {
            $resource->set('alias', '0');
        } else if ($resource->get('class_key') === 'msProduct') {
            $resourceId = (string)$resource->get('id');
            $resourceAlias = (string)$resource->get('alias');
            if ($resourceAlias !==  $resourceId) {
                $resource->set('alias', $resourceId);
            }
        }
        break;
    case 'OnDocFormSave':
        if ($resource->get('class_key') === 'msProduct') {
            $resourceId = (string)$resource->get('id');
            $resourceAlias = (string)$resource->get('alias');
            if ($resourceAlias !==  $resourceId) {
                $resource->set('alias', $resourceId);
                $resource->save();
            }
        }
        break;
}
