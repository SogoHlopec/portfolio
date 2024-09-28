<?php
require_once __DIR__ . '/vendor/autoload.php';

// Ключ доступа к сервисному аккаунту
$googleAccountKeyFilePath = 'google-api-key.json';
putenv('GOOGLE_APPLICATION_CREDENTIALS=' . $googleAccountKeyFilePath);

$client = new Google_Client();
$client->useApplicationDefaultCredentials();
$client->setScopes(Google\Service\Sheets::SPREADSHEETS);
$service = new Google\Service\Sheets($client);

// table ID
$spreadsheetId = '1mhEIlIXHFaaYcePyQyjifeRxbrjGx_5Z772iGyBC6BM';

$response = $service->spreadsheets->get($spreadsheetId);
$sheet = $response->getSheets()[0];

// Retrieving the contents of an entire sheet by its name
$range = $sheet->getProperties()->title;
$response = $service->spreadsheets_values->get($spreadsheetId, $range);
$values = $response['values'];

$data = [];

foreach ($values as $rowIndex => $value) {
    if ($rowIndex > 0) {
        $priceTag = $value[0];
        if ($priceTag) {
            $priceTag = implode(explode(' ', $priceTag));
            $price = $value[2];
            $data[$priceTag] = $price;
        }
    }
}

// var_dump($data);
return $data;
