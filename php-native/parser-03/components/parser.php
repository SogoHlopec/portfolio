<?php

use DiDom\Document;

function curlGetPage($url, $referer = 'https://google.by/', $encoding = true)
{
    sleep(rand(2, 5));
    // Initialisation of cURL
    $ch = curl_init();

    // Setting cURL parameters
    if ($encoding) {
        $header = [
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
        ];
    } else {
        $header = [
            'cache-control: max-age=0',
            'upgrade-insecure-requests: 1',
            'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36", "X-Amzn-Trace-Id": "Root=1-65800231-4ec7579a3e2845874b18ad24',
            'sec-fetch-user: ?1',
            'accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
            'x-compress: null',
            'sec-fetch-site: none',
            'sec-fetch-mode: navigate',
            'accept-language: ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7',
        ];
    }

    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt_array($ch, [
        CURLOPT_URL             => $url,
        CURLOPT_RETURNTRANSFER  => true,
        CURLOPT_ENCODING        => '',
        CURLOPT_TIMEOUT         => 1500,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION    => CURL_HTTP_VERSION_1_1,
        CURLOPT_HTTPHEADER      => $header,
        CURLOPT_REFERER => $referer,
    ]);

    // Executing a request
    $response = curl_exec($ch);

    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    // Error checking
    if (curl_errno($ch)) {
        echo 'Curl error: ' . curl_error($ch);
        setLog('Curl error: ' . curl_error($ch), 'p');
    } elseif ($http_code != 200) {
        echo 'HTTP error: ' . $http_code;
        setLog('HTTP error: ' . $http_code, 'p');
    }

    // Closing a cURL session
    curl_close($ch);

    return $response;
}

// Writing logs to a file
function setLog($message, $tag)
{
    try {
        $filePath = 'logs.html';

        // Check if the file exists
        if (!file_exists($filePath)) {
            // If the file does not exist, create it and write the line
            file_put_contents($filePath, '<meta charset="UTF-8">', FILE_APPEND);
        }
        if ($tag === 'h1') {
            date_default_timezone_set('Europe/Minsk');
            $currentDate = date('d-m-Y H:i:s');
            $str = "<{$tag}>{$message} {$currentDate}</{$tag}>";
            file_put_contents($filePath, $str . PHP_EOL, FILE_APPEND);
        } else {
            $str = "<{$tag}>$message</{$tag}>";
            file_put_contents($filePath, $str . PHP_EOL, FILE_APPEND);
        }
    } catch (\Throwable $th) {
        echo "Something went wrong: Couldn't get a log.\n" . $th . "\n";
    }
}

function parsingSiteCategory($url, $category, $productCounter, $referer = 'https://google.by/')
{
    $result = [];

    try {

        echo "Page $url \n";
        setLog("Page $url", 'h4');

        $document = new Document();
        $html = curlGetPage($url, $referer);
        if ($html === false) {
            setLog("Something went wrong: Failed to retrieve HTML from $url", 'p');
            echo "Something went wrong: Failed to retrieve HTML from $url\n";
        }

        $document->loadHtml($html);

        $sectionProducts = $document->first('.product-table');
        if (!$sectionProducts) {
            setLog('Something went wrong: Unable to locate items', 'p');
            echo "Unable to find items\n";
        }

        $productCards = $sectionProducts->find('.product-table__item');
        if (count($productCards) > 0) {
            foreach ($productCards as $i => $card) {
                //Parametrs
                $name = 'No product name';
                $mark = '';
                $standart = '';

                if ($card->has('.short-product__title')) {
                    $name = $card->first('.short-product__title')->text();
                }

                if ($name) {
                    $pattern = '/(.+)(\s.+\sмм)\s(.+)\s(ГОСТ\s.+)/u';


                    if (preg_match($pattern, $name, $matches)) {
                        $mark = trim($matches[1]);
                        $standart = trim($matches[2]);
                        var_dump($mark);
                        var_dump($standart);
                    }
                }

                if ($card->has('.short-product__info a')) {
                    $url = $card->first('.short-product__info a')->attr('href');
                    $url = 'https://site.com/' . $url;
                }

                $productCounter++;
                echo "Successful product parsing $productCounter: $name + $url\n";
                setLog("Successful product parsing $productCounter: $name + $url", 'p');
                $result[] = [$name, $mark, $standart, $category, $url];
            }
        }

        if ($document->has('.pagination')) {
            $pagination = $document->first('.pagination');
            if ($pagination->has('li.pagination__active')) {
                $pageItemActive = $pagination->first('li.pagination__active');
                if ($pageItemActive->nextSibling('li.pagination__li')) {
                    $url = 'https://site.com/' . $pageItemActive->nextSibling('li.pagination__li')->first('a')->attr('href');
                    $result = array_merge($result, parsingSiteCategory($url, $category, $productCounter));
                    return $result;
                }
            }
        }

        return $result;
    } catch (\Throwable $th) {
        return $result;
        setLog('Something went wrong: Couldnt spar the item.' . $th, 'p');
        echo "Something went wrong: Couldnt spar the item.\n" . $th . "\n";
    }
}
