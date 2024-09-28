<?php

use DiDom\Document;

function curlGetPage($url, $referer = 'https://google.by/')
{
    sleep(rand(1, 3));
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt_array($ch, [
        CURLOPT_URL             => $url,
        CURLOPT_RETURNTRANSFER  => true,
        CURLOPT_ENCODING        => '',
        CURLOPT_TIMEOUT         => 1500,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION    => CURL_HTTP_VERSION_1_1,
        CURLOPT_HTTPHEADER      => [
            'cache-control: max-age=0',
            'upgrade-insecure-requests: 1',
            'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36", "X-Amzn-Trace-Id": "Root=1-65800231-4ec7579a3e2845874b18ad24',
            'sec-fetch-user: ?1',
            'accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
            'x-compress: null',
            'sec-fetch-site: none',
            'sec-fetch-mode: navigate',
            'accept-encoding: gzip, deflate, br',
            'accept-language: ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7',
        ],
        CURLOPT_REFERER => $referer,
    ]);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        echo 'Curl error: ' . curl_error($ch);
        setLog('Curl error: ' . curl_error($ch), 'p');
    }

    curl_close($ch);

    return $response;
}

function setLog($message, $tag)
{
    $filePath = 'logs.html';

    if (!file_exists($filePath)) {
        file_put_contents($filePath, '<meta charset="UTF-8">', FILE_APPEND);
    }
    if ($tag === 'h1') {
        date_default_timezone_set('Europe/Minsk');
        $currentDate = date('d-m-Y H:m:s');
        $str = "<{$tag}>{$message} {$currentDate}</{$tag}>";
        file_put_contents($filePath, $str . PHP_EOL, FILE_APPEND);
    } else {
        $str = "<{$tag}>$message</{$tag}>";
        file_put_contents($filePath, $str . PHP_EOL, FILE_APPEND);
    }
}

function getDataProduct($url, $count, $category, $filterKeys, $referer = 'https://google.ru/')
{
    try {
        $document = new Document();

        $html = curlGetPage($url, $referer);
        if ($html === false) {
            echo "Проблемы со страницей продукта  $count - $url \n";
            return [
                'id' => $count,
                'category' => $category['category']['name'],
                'categoryId' => $category['category']['id'],
                'subCategory' => $category['subCategory']['name'],
                'subCategoryId' => $category['subCategory']['id'],
                'url' => $url,
                'title' => 'Не найдено',
                'vendor' => '',
                'price' => '',
                'availability' => '',
                'article' => '',
                'images' => '',
                'description' => '',
                'specification' => '',
                'weight' => '',
                'detailedSpecification' => '',
            ];
        }

        $document->loadHtml($html);
        // Parsing the page into parameters
        $result = [];
        echo "Парсим продукт $count - $url \n";
        setLog("Парсим продукт $count - $url", 'p');

        echo round(memory_get_usage() / 1024 / 1024, 4) . ' MB' . PHP_EOL;
        setLog(round(memory_get_usage() / 1024 / 1024, 4) . ' MB' . PHP_EOL, 'p');

        if ($document->has('h1')) {
            $title = $document->first('h1')->text();
        } else {
            echo "Не найдено название продукта  $count - $url \n";
            $title = "Не найдено";
        }

        if ($document->has('selector')) {
            $vendor = $document->first('selector')->attr('alt');
        } else {
            $vendor = 'Не найдено';
        }

        if ($document->has('selector')) {
            $price = preg_replace('/[^0-9]/', '', $document->first('selector')->text());
        } else {
            $price = 'Цена по запросу';
        }

        // Availability
        if ($document->has('selector')) {
            $availability = $document->first('selector')->text();
        }
        if ($availability !== false) {
            if (strpos(mb_strtolower($availability), 'нет') !== false || strpos(mb_strtolower($availability), 'отсутствует') !== false || strpos(mb_strtolower($availability), 'под заказ') !== false) {
                $availability = '0';
            } elseif (strpos(mb_strtolower($availability), 'заканчивается') !== false) {
                $availability = '1';
            } else {
                $availability = '10';
            }
        } else {
            $availability = 'Не найдено';
        }

        if ($document->has('selector')) {
            $article = $document->first('selector')->text();
        } else {
            $article = 'Не найдено';
        }

        // Collecting links to pictures
        $images = [];
        if ($document->has('selector') && $document->first('selector')->has('selector')) {
            $imagesWrapper = $document->first('selector')->first('selector');
            $imagesLink = $imagesWrapper->find('selector');
            if (count($imagesLink) > 0) {
                foreach ($imagesLink as $item) {
                    $images[] = 'https://url' . $item->attr('href');
                }
            }
        } else {
            if ($document->has('selector #photo-area__cur-image')) {
                $images[] = 'https://url' . $document->first('selector')->attr('href');
            }
        }

        // Collect a description, characterization
        $isDetailedSpecification = false;
        $indexDetailed = 0;

        if ($document->has('selector') && count($document->first('selector')->find('selector')) > 0) {
            $tabsLink = $document->first('selector')->find('selector');

            if (count($tabsLink) > 0) {
                foreach ($tabsLink as $index => $item) {
                    if ($item->text() === 'Подробная спецификация') {
                        $isDetailedSpecification = true;
                        $indexDetailed = $index;
                    } else {
                        $detailedSpecification = 'Не найдено';
                    }
                }
            } else {
                $detailedSpecification = 'Не найдено';
            }
        } else {
            $detailedSpecification = 'Не найдено';
            echo ("Не удалось найти Описание, Характеристики! tabsLink === 0\n");
        }

        if ($document->has('selector')) {
            $tabsWrap = $document->first('selector');
            $tabContents = $tabsWrap->find('selector');

            if ($tabsWrap->has('selector')) {
                $specification = $tabsWrap->first('selector');
                $table = createSpecificationTable($specification);
                $specification = $specification->setInnerHtml($table)->html();
            } else {
                $specification = 'Не найдено';
            }

            if ($tabsWrap->has('selector')) {

                $description = $tabsWrap->first('selector')->html();

                if ($tabsWrap->first('selector')->has(' img[alt*=Вес в упаковке]')) {
                    $weight = extractWeight($tabsWrap->first('selector')->first(' img[alt*=Вес в упаковке]')->parent()->text());
                } else {
                    $weight = 'Не найдено';
                }

                $img = $document->first('selector')->firstInDocument('selector')->findInDocument('img');

                if (count($img) > 0) {
                    foreach ($img as $item) {
                        $item->remove();
                    }
                }

                $description = $document->first('selector')->first('selector')->html();
            } else {
                $description = 'Не найдено';
                $weight = 'Не найдено';
            }

            if (count($tabContents) > 0) {
                foreach ($tabContents as $index => $item) {
                    if ($isDetailedSpecification === true && $index === $indexDetailed) {
                        $detailedSpecification = $item->html();
                    }
                }
            }
        } else {
            $specification = 'Не найдено';
            $description = 'Не найдено';
            $weight = 'Не найдено';

            echo ("Не удалось найти Описание, Характеристики, вес! tabsWrap === 0\n");
        }

        $result = [
            'id' => $count,
            'category' => $category['category']['name'],
            'categoryId' => $category['category']['id'],
            'subCategory' => $category['subCategory']['name'],
            'subCategoryId' => $category['subCategory']['id'],
            'url' => $url,
            'title' => $title,
            'vendor' => $vendor,
            'price' => $price,
            'availability' => $availability,
            'article' => $article,
            'images' => implode("; ", $images),
            'description' => str_replace('&#13;', '', $description),
            'specification' => $specification,
            'weight' => $weight,
            'detailedSpecification' => str_replace('&#13;', '', $detailedSpecification),
            'filterParam' => $filterParam,
        ];
        return $result;
    } catch (Throwable $ex) {
        echo "Что-то пошло не так: Не смогли спарсить товар\n";
        $result = [
            'id' => $count,
            'category' => $category['category'],
            'categoryId' => $category['category']['id'],
            'subCategory' => $category['subCategory']['subCategory'],
            'subCategoryId' => $category['subCategory']['id'],
            'url' => $url,
            'title' => 'Не найдено',
            'vendor' => '',
            'price' => '',
            'availability' => '',
            'article' => '',
            'images' => '',
            'description' => '',
            'specification' => '',
            'weight' => '',
            'detailedSpecification' => '',
            'filterParam' => '',
        ];
        return $result;
    }
}

function parsingProducts($url, $productSkipCounter = 0)
{
    echo "Страница $url \n";

    $subCategoryLinks = getSubCategoryLinks($url);
    if ($subCategoryLinks) {
        $count = 1;
        $id = 1;
        foreach ($subCategoryLinks as $index => $value) {
            if ($value['category'] === '') {
                continue;
            }
            $categoryId = $id;
            $id++;
            foreach ($value['subCategories'] as $subCategory) {
                $subCategoryId = $id;
                $id++;
                $category = [
                    'category' => ['name' => $value['category'], 'id' => $categoryId],
                    'subCategory' => ['name' => $subCategory['subCategory'], 'link' => $subCategory['link'], 'id' => $subCategoryId],
                ];
                $data = parsingSubcategoryProductData($category, $count, $productSkipCounter, $url);
                $count = $data['count'];
            }
        }
    } else {
        setLog("Что-то пошло не так: Не удалось найти субкатегории", 'p');
        throw new Exception("Что-то пошло не так: Не удалось найти субкатегории");
    }
}
