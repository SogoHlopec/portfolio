<?php

// Options
$token = 'YOUR_TOKEN';
$notionVersion = '2022-06-28';
$databaseId = "YOUR_DATABASEID";

function getNotionDatabase($token, $notionVersion, $databaseId, $nextRequestDataJson = '')
{
    // Formation of the request URL
    $get_url = 'https://api.notion.com/v1/databases/' . $databaseId . '/query';

    // Initializing cURL
    $ch = curl_init();

    // Setting cURL options
    curl_setopt(
        $ch,
        CURLOPT_SSL_VERIFYHOST,
        false
    );
    curl_setopt(
        $ch,
        CURLOPT_SSL_VERIFYPEER,
        false
    );
    curl_setopt_array($ch, [
        CURLOPT_URL             => $get_url,
        CURLOPT_RETURNTRANSFER  => true,
        CURLOPT_ENCODING        => '',
        CURLOPT_MAXREDIRS       => 10,
        CURLOPT_TIMEOUT         => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION    => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST   => 'POST',
        CURLOPT_HTTPHEADER      => [
            'Authorization: Bearer ' . $token,
            'Notion-Version: ' . $notionVersion,
            'Content-Type: application/json',
        ],
        CURLOPT_POSTFIELDS      => $nextRequestDataJson,
    ]);

    // Executing the request
    $response = curl_exec($ch);

    // Checking for errors
    if (curl_errno($ch)) {
        echo 'Curl error: ' . curl_error($ch);
    }

    // Closing a cURL session
    curl_close($ch);

    if ($response) {
        $data = json_decode($response, true);

        // If there is a lot of data, then we get the remaining data
        if ($data['has_more'] && isset($data['next_cursor'])) {
            $startCursor = $data['next_cursor'];
            $nextRequestData = ['start_cursor' => $startCursor];
            $nextRequestDataJson = json_encode($nextRequestData);
            // Recursive function call and concatenation of results
            $nextData =
                getNotionDatabase($token, $notionVersion, $databaseId, $nextRequestDataJson);
            $data['results'] = array_merge($data['results'], $nextData['results']);
        }
        return $data;
    } else {
        return null;
    }
}

$data = getNotionDatabase($token, $notionVersion, $databaseId, $nextRequestDataJson);

// Processing the response
if ($data) {
    $result = array();
    $count = 0;
    var_dump(count($data['results'])); // Displaying the total number of pages in the response
    foreach ($data['results'] as $page) {
        $count++;
        var_dump($count); // Counter for each page
        $properties = $page["properties"];
        // Accessing data you have confidence in (bad practice)
        $name = $properties["name"]["title"][0]["text"]["content"];
        $url = $properties["url"]["formula"]["string"];
        //...

        // Outputting data to a page
        echo "<p>{$name} + {$url}<br></p>";

        // Collecting data into an array
        $dataPage  = array(
            'name' => $name,
            'url' => $url,
            //...
        );
        $result[] = $dataPage;
    }
} else {
    echo 'Error retrieving Notion page.';
}
