<?php
if (!empty($resourceId)) {
    $resource = $modx->getObject('modResource', $resourceId);
    $output = '';

    if ($resource) {
        $createdByUserId = $resource->get('createdby');
        $createdDate = $resource->get('createdon');
        $editedByUserId = $resource->get('editedby');
        $editeDate = $resource->get('editedon');

        if ($createdByUserId) {
            $createdByUser = $modx->getObject('modUser', $createdByUserId);
            $editedByUser = $modx->getObject('modUser', $editedByUserId);
            $profile = $modx->getObject('modUserProfile', array('internalKey' => $createdByUserId));
            if ($profile) {
                $fullname = $profile->get('fullname');
            }
            $profileEdited = $modx->getObject('modUserProfile', array('internalKey' => $editedByUserId));
            if ($profileEdited) {
                $fullnameEdited = $profileEdited->get('fullname');
            }

            $output = '<div class="author"><p>Добавил: ' . $fullname . ' ' . $createdDate . '</p>
            <p>Обновил: ' . $fullnameEdited . ' ' . $editeDate . '</p></div>';
        } else {
            $profileEdited = $modx->getObject('modUserProfile', array('internalKey' => $editedByUserId));
            if ($profileEdited) {
                $fullnameEdited = $profileEdited->get('fullname');
            }

            $output = '<div class="author"><p>Cоздано скриптом из notion ' . $createdDate . '</p>
            <p>Обновил: ' . $fullnameEdited . ' ' . $editeDate . '</p></div>';
        }
    }
    return $output;
}
