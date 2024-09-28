<?php

use DiDom\Document;

function curlGetPage($url, $referer = 'https://google.by/', $encoding = true)
{
    sleep(rand(1, 3));
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
        echo "Что-то пошло не так: Не смогли записать лог\n" . $th . "\n";
    }
}

function parser($url, $category, $productCounter, $referer = 'https://google.by/')
{
    try {
        $result = [];
        $currency = 'BYN';

        echo "Страница $url \n";
        setLog("Страница $url", 'h4');

        $document = new Document();
        $html = curlGetPage($url, $referer, false);

        if ($html === false) {
            setLog("Что-то пошло не так: Не удалось получить HTML с $url", 'p');
            echo "Что-то пошло не так: Не удалось получить HTML с $url\n";
        }

        $document->loadHtml($html);

        $sectionProducts = $document->first('selector');
        if (!$sectionProducts) {
            setLog('Что-то пошло не так: Не удалось найти товары', 'p');
            echo "Не удалось найти товары\n";
        }


        $productCards = $sectionProducts->find('selector');
        if (count($productCards) > 0) {
            foreach ($productCards as $i => $card) {
                $name = 'Нет названия товара';
                $price = 0;
                $image = '';

                if ($card->has('selector')) {
                    $name = $card->first('selector')->text();
                }
                if ($card->has('selector')) {
                    $price = $card->first('selector')->text();
                    $price = str_replace([' ', 'BYN'], '', $price);
                    if (empty($price)) {
                        $price = 0;
                    }
                }

                if ($card->has('selector')) {
                    $url = $card->first('selector')->attr('href');
                    $url = 'https://url/' . $url;
                }

                if ($card->has('selector')) {
                    $image = $card->first('selector')->attr('src');
                    $image = 'https://url/' . $image;
                }

                $productCounter++;
                echo "Успешный парсинг товара $productCounter: $name + $price + $url + $image\n";
                setLog("Успешный парсинг товара $productCounter: $name + $price + $url + $image", 'p');
                $result[] = [$name, $price, $currency, $category, $url, $image];
            }
        }

        if ($document->has('selector')) {
            $pagination = $document->first('selector');
            if ($pagination->has('selector')) {
                $pageItemActive = $pagination->first('selector');
                if ($pageItemActive->nextSibling('selector')) {

                    return $result;
                } elseif ($pageItemActive->nextSibling('selector')) {
                    $url = 'https://url/' . $pageItemActive->nextSibling('selector')->first('a')->attr('href');
                    $result = array_merge($result, parser($url, $category, $productCounter));
                    return $result;
                }
            }
        }

        return $result;
    } catch (\Throwable $th) {
        setLog('Что-то пошло не так: Не смогли спарсить товар' . $th, 'p');
        echo "Что-то пошло не так: Не смогли спарсить товар\n" . $th . "\n";
    }
}
