<?php
// We're going through all the merchandise
$products = $modx->getIterator('msProduct', array('class_key' => 'msProduct'));
$count = 1;
foreach ($products as $product) {
    $count++;
    // Getting the originals of their pictures
    $files = $product->getMany('Files', array('parent' => 0));
    foreach ($files as $file) {
        // Then we get their prey
        $children = $file->getMany('Children');
        foreach ($children as $child) {
            // We delete these previews, along with the files.
            $child->remove();
        }
        // And generate new ones
        $file->generateThumbnails();

        // If this is the first file in the gallery - update the link to the preview of the product
        /** @var msProductData $data */
        if ($file->get('rank') == 0 && $data = $product->getOne('Data')) {
            $thumb = $file->getFirstThumbnail();
            $data->set('thumb', $thumb['url']);
            $data->save();
        }
    }
    echo "Продукт {$count}";
}
