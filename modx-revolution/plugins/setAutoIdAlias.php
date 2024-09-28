<?php
switch ($modx->event->name) {
    case 'OnBeforeDocFormSave':
        if ($mode == 'new') {
            $resource->set('alias', '0');
            $resource->save();
        }
        break;
    case 'OnDocFormSave':
        $resourceId = $resource->get('id');
        $resourceAlias = (string)$resourceId;
        $resource->set('alias', $resourceAlias);
        $resource->save();
        break;
    default:
        break;
}
