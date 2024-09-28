<?php
switch ($modx->event->name) {
    case 'OnDocUnPublished':
        $id = $resource->get('id');
        // Get all resources
        $resourcesIds = $modx->getChildIds($id, '10', array('context' => 'web'));
        $resources = $modx->getCollection('modResource', array('id:IN' => $resourcesIds));

        foreach ($resources as $key => $resource) {
            $resource->set('published', 0);
            $resource->save();
        }
        break;
    case 'OnDocPublished':
        $id = $resource->get('id');
        // Get all resources
        $resourcesIds = $modx->getChildIds($id, '10', array('context' => 'web'));
        $resources = $modx->getCollection('modResource', array('id:IN' => $resourcesIds));

        foreach ($resources as $key => $resource) {
            $resource->set('published', 1);
            $resource->save();
        }
        break;
    default:
        break;
}
