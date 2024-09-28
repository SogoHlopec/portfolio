<?php
switch ($modx->event->name) {
    case 'OnWebPagePrerender':
        $output = &$modx->resource->_output;

        // Parsing content to find image tags
        $pattern = '/<img[^>]+src="([^"]+)"[^>]+>/i';
        preg_match_all($pattern, $output, $matches);

        $processedImages = [];

        // Searching for found images
        foreach ($matches[1] as $imagePath) {
            if (strripos($imagePath, 'webp') === false) {
                // Applying pThumb to each image
                $processedImagePath = $modx->runSnippet('pthumb', [
                    'input' => $imagePath,
                    'options' => 'w=1440&f=webp', // Parameters for PThumb
                ]);
            } else {
                $processedImagePath = $imagePath;
            }

            $processedImages[] = $processedImagePath;
        }

        // Replacing original images in the content with processed images
        for ($i = 0; $i < count($matches[1]); $i++) {
            $output = str_replace($matches[0][$i], '<img src="' . $processedImages[$i] . '" class="card__image resize">', $output);
        }
        break;
}
