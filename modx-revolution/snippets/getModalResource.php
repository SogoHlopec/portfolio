<?php
// It will respond ONLY to ajax requests
if (empty($_SERVER['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') {
    return;
}

// The snippet will handle more than one type of requests, so we will work on the requested action
// If there is no action in the POST array - exit
if (empty($_POST['action'])) {
    return 'ID is required!';
}

$result = '';
switch ($_POST['action']) {
    case 'getModalResource':
        $id = $_POST['id'];
        // Get data on the resource with the specified ID
        $resource = $modx->getObject('modResource', $id);
        $idTypePages = 3;
        $innerResourceId = '';

        if (!$resource) {
            return 'Resource not found!';
        }
        // Get parent ID
        $parentId = $resource->get('parent');
        // Get the object of the parent resource
        $parentResource = $modx->getObject('modResource', $parentId);

        // Get all resources inside the resource with id = $idTypePages
        $typePagesResources = $modx->getCollection('modResource', array(
            'parent' => $idTypePages,
            'deleted' => false,
            'published' => true
        ));

        // Going through the resources we've gotten
        foreach ($typePagesResources as $innerResource) {
            $innerResourcePagetitle = $innerResource->get('pagetitle');
            $innerResourceId = $innerResource->get('id');
        }

        // Getting data for the chunk
        $chunkData = array(
            'innerResourceId' => $innerResourceId,
            'innerResourcePagetitle' => $innerResourcePagetitle,
            'parentResourceId' => $parentId,
            'id' => $id,
            'tvParam' => $resource->getTVValue('tvParam'),
            // other data...
        );

        // Receive the contents of the chunk and transfer the data
        $modalChunkContent = $modx->getChunk('tplCardModal', $chunkData);

        // Если у нас есть, что отдать на запрос - отдаем и прерываем работу парсера MODX
        $result = $modalChunkContent;
        break;
    default:
        break;
}

if (!empty($result)) {
    die($result);
}
