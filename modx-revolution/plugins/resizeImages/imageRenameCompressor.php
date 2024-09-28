<?php
require_once MODX_CORE_PATH . './customPhpThumb/vendor/autoload.php';

switch ($modx->event->name) {
    case 'OnFileManagerBeforeUpload':
        try {
            setlocale(LC_ALL, 'ru_RU.utf8'); // fix for wrong servers, otherwise patchinfo will not work with Cyrillic characters

            foreach ($files as $key => $file) {

                $info = pathinfo($file['name']);
                $name = $info['filename'];
                $extension = $info['extension'];

                $fullname = $name . '.' . $extension;
                $hash = md5_file($file['tmp_name']);
                $newFullName = $hash . '.' . $extension;

                $file['name'] = $newFullName; // Rewrite the file name to a hash file
                $modx->event->params['file'] = $file; // give the modified file variable
                $files[$key] = $file;
            }
            $modx->event->params['files'] = $files; // give the modified files variable
        } catch (\Throwable $th) {
            $modx->log(1, $th);
        }
        break;

    case 'OnFileManagerUpload':
        try {
            setlocale(LC_ALL, 'ru_RU.utf8'); // fix for wrong servers, otherwise patchinfo will not work with Cyrillic characters

            $fullPath = $source->getBases()['pathAbsolute'] . $directory;

            foreach ($files as $file) {
                if (strripos($file['type'], 'image') === false || $file['type'] == 'image/svg+xml') {
                    continue;
                }

                $info = pathinfo($file['name']);
                $name = $info['filename'];
                $fullName = $file['name'];

                $pathToImage = $fullPath . $fullName;
                $newFileName = $name . '.webp';

                $thumb = new PHPThumb\GD($pathToImage);
                $thumb->resize(1440);
                $thumb->save(($fullPath . $newFileName), 'webp'); // Save in new format

            }
        } catch (\Throwable $th) {
            $modx->log(1, $th);
        }
        break;
}
