<?php

require_once dirname(dirname(__FILE__)) . '/vendor/autoload.php';

// Ключ доступа к сервисному аккаунту
$googleAccountKeyFilePath = './components/google-api-key.json';
putenv('GOOGLE_APPLICATION_CREDENTIALS=' . $googleAccountKeyFilePath);

define('SPREAD_SHEET_ID', '12aoy8v_TXkgRmQ2BpT2-J2CKX429RRK7-OHnPorlCME');

function readGoogleSheet()
{
    try {
        $client = new Google_Client();
        $client->useApplicationDefaultCredentials();
        $client->setScopes(Google\Service\Sheets::SPREADSHEETS);
        $service = new Google\Service\Sheets($client);
        $response = $service->spreadsheets->get(SPREAD_SHEET_ID);
        $spreadsheetProperties = $response->getProperties();

        var_dump($spreadsheetProperties->title);

        $sheet = $response->getSheets()[0];

        // Получение содержимого всего листа по его имени
        $range = $sheet->getProperties()->title;
        var_dump($range);
        $response = $service->spreadsheets_values->get(SPREAD_SHEET_ID, $range);
        $values = $response['values'];

        foreach ($values as $rowIndex => $value) {
            var_dump($value);
        }
    } catch (\Throwable $th) {
        setLog('Что-то пошло не так: Не удалось прочитать в таблицу' . $th, 'p');
        echo "Что-то пошло не так: Не удалось прочитать в таблицу\n" . $th . "\n";
    }
    
}

function writeGoogleSheet($data, $sheetIndex, $sheetTitle, $rowIndex, $category)
{
    try {
        echo "Start writing to table sheet: $sheetIndex - $sheetTitle - $category\n";

        $guzzleClient = new \GuzzleHttp\Client(array('curl' => array(CURLOPT_SSL_VERIFYPEER => false,),));
        $client = new Google_Client();
        $client->setHttpClient($guzzleClient);
        $client->useApplicationDefaultCredentials();
        $client->setScopes(Google\Service\Sheets::SPREADSHEETS);
        $service = new Google\Service\Sheets($client);
        $response = $service->spreadsheets->get(SPREAD_SHEET_ID);

        $sheet = $response->getSheets()[$sheetIndex];

        // Clear sheet
        $rangeToClear = "{$sheetTitle}!A{$rowIndex}:Z";
        $clearRequest = new Google_Service_Sheets_ClearValuesRequest();
        $service->spreadsheets_values->clear(SPREAD_SHEET_ID, $rangeToClear, $clearRequest);


        $sheetProperties =  $sheet->getProperties();
        $sheetProperties->setTitle($sheetTitle);
        $range = $sheetProperties->title;
        $range = $range . "!A{$rowIndex}";

        $body = new Google_Service_Sheets_ValueRange([
            'values' => $data
        ]);
        $params = [
            'valueInputOption' => 'RAW'
        ];
        $service->spreadsheets_values->update(SPREAD_SHEET_ID, $range, $body, $params);

        $rowIndex = $rowIndex + count($data);
        return $rowIndex;
        echo "End writing\n";
    } catch (\Throwable $th) {
        setLog('Что-то пошло не так: Не удалось записать в таблицу' . $th, 'p');
        echo "Что-то пошло не так: Не удалось записать в таблицу\n" . $th . "\n";
    }
    
}
