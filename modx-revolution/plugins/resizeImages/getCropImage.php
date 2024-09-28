<?php
require_once MODX_CORE_PATH . './customPhpThumb/vendor/autoload.php';
$iamgePath = $input;

if ($iamgePath !== '' && $iamgePath !== 'scr2pages/') {
    $croppedDirectory = 'assets/images/scriptCrop/';
    $fullPathCroppedDirectory = MODX_BASE_PATH . $croppedDirectory;
    $dir = scandir($fullPathCroppedDirectory);

    $fullPathImage = MODX_BASE_PATH . $iamgePath;

    $hash = md5_file($fullPathImage);
    $newFullName = $hash . '.' . 'webp';

    // Check for an already cropped image
    if (!in_array($newFullName, $dir) && file_exists($fullPathImage)) {
        // Shape the cropped image
        $thumb = new PHPThumb\GD($fullPathImage);
        $thumb->crop(0, 0, 1440, 1440); // Set parameters for trimming
        $thumb->save(($fullPathCroppedDirectory . $newFullName), 'webp'); // Save the picture
    }
    return $croppedDirectory . $newFullName;
}
