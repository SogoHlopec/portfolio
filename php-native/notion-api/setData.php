<?php
set_time_limit(300);

// Options
$token = 'YOUR_TOKEN';
$notionVersion = '2022-06-28';
$databaseId = "YOUR_DATABASEID";

function getNotionDatabase($token, $notionVersion, $databaseId, $requestData = null)
{
    // An example of using a filter using the custom id parameter (NOT id notion)
    if ($requestData === null) {
        $requestData = json_encode([
            'filter' => [
                'property' => 'id',
                'number' => [
                    'is_not_empty' => true,
                ],
            ],
        ]);
    }

    // Formation of the request URL
    $getUrl = 'https://api.notion.com/v1/databases/' . $databaseId . '/query';

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
        CURLOPT_URL             => $getUrl,
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
        CURLOPT_POSTFIELDS => $requestData,
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

        // We check if all the data has been received, if not, then we get the rest
        if ($data['has_more'] && isset($data['next_cursor'])) {
            $startCursor = $data['next_cursor'];
            $requestData =
                json_encode([
                    'start_cursor' => $startCursor,
                    'filter' => [
                        'property' => 'id',
                        'number' => [
                            'is_not_empty' => true,
                        ],
                    ],
                ]);
            // Recursive function call and concatenation of results
            $nextData =
                getNotionDatabase($token, $notionVersion, $databaseId, $requestData);
            $data['results'] = array_merge($data['results'], $nextData['results']);
        }
    }
    return $data;
}

function setNotionDatabase($token, $notionVersion, $dataArray)
{

    // Formation of the request URL
    $postUrl = 'https://api.notion.com/v1/pages';

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
        CURLOPT_URL             => $postUrl,
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
        CURLOPT_POSTFIELDS      => json_encode($dataArray),
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
        return $data;
    } else {
        return null;
    }
}

function updateNotionPage($token, $notionVersion, $pageId, $dataArray)
{

    // Formation of the request URL
    $patchUrl = 'https://api.notion.com/v1/pages/' . $pageId;

    // Инициализация cURL
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
        CURLOPT_URL             => $patchUrl,
        CURLOPT_RETURNTRANSFER  => true,
        CURLOPT_ENCODING        => '',
        CURLOPT_MAXREDIRS       => 10,
        CURLOPT_TIMEOUT         => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION    => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST   => 'PATCH',
        CURLOPT_HTTPHEADER      => [
            'Authorization: Bearer ' . $token,
            'Notion-Version: ' . $notionVersion,
            'Content-Type: application/json',
        ],
        CURLOPT_POSTFIELDS      => json_encode($dataArray),
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
        return $data;
    } else {
        return null;
    }
}

function deleteNotionPage($token, $notionVersion, $pageId)
{

    // Formation of the request URL
    $patchUrl = 'https://api.notion.com/v1/pages/' . $pageId;

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
        CURLOPT_URL             => $patchUrl,
        CURLOPT_RETURNTRANSFER  => true,
        CURLOPT_ENCODING        => '',
        CURLOPT_MAXREDIRS       => 10,
        CURLOPT_TIMEOUT         => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION    => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST   => 'PATCH',
        CURLOPT_HTTPHEADER      => [
            'Authorization: Bearer ' . $token,
            'Notion-Version: ' . $notionVersion,
            'Content-Type: application/json',
        ],
        CURLOPT_POSTFIELDS      => json_encode(['archived' => true]),
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
        return $data;
    } else {
        return null;
    }
}

//  An example of data that we want to load into notion, just an array of custom ids
$pagesId = range(1, 117);

$existingPages = [];
$data = getNotionDatabase($token, $notionVersion, $databaseId);

// Getting existing pages in notion
if (!empty($data['results'])) {
    foreach ($data['results'] as $page) {
        if (!empty($page['properties']['id']['number'])) {
            $pageIdNotion = $page['id'];
            $pageId = $page['properties']['id']['number'];
            $existingPages[$pageId] = $pageIdNotion;
        }
    }
}

foreach ($pagesId as $id) {
    if ($existingPages[$id]) {
        echo "<p>The page with id = $id already exists, update the page data.</p>";
        $pageData = [
            'properties' => [
                'id' => [
                    'type' => 'number',
                    'number' => $id,
                ],
                'Name' => [
                    'type' => 'title',
                    'title' => [
                        [
                            'type' => 'text',
                            'text' => ['content' => "Name$id"]
                        ]
                    ]
                ]
            ],
        ];

        $data = updateNotionPage(
            $token,
            $notionVersion,
            $existingPages[$id],
            $pageData
        );

        // Processing the response
        if ($data) {
            var_dump(count($data));
            echo "<p>Page data updated.</p>";
        } else {
            echo 'Error retrieving Notion page.';
        }
    } else {
        echo "<p>Page with id = $id does not exist, create a page.</p>";
        $pageData = [
            'parent'     => ['type' => 'database_id', 'database_id' => $databaseId,], // To create, you need to pass the database id
            'properties' => [
                'id' => [
                    'type' => 'number',
                    'number' => $id,
                ],
                'Name' => [
                    'type' => 'title',
                    'title' => [
                        [
                            'type' => 'text',
                            'text' => ['content' => "Name$id"]
                        ]
                    ]
                ]
            ],
        ];

        $data = setNotionDatabase(
            $token,
            $notionVersion,
            $pageData
        );

        // Processing the response
        if ($data) {
            var_dump(count($data));
            echo "<p>Page created.</p>";
        } else {
            echo 'Error retrieving Notion page.';
        }
    }
}

// Removing irrelevant pages in the Notion database
$keysExistingPages = array_keys($existingPages);
foreach ($keysExistingPages as $key) {
    if (!in_array($key, $pagesId)) {
        $pageIdNotion = $existingPages[$key];

        $data = deleteNotionPage(
            $token,
            $notionVersion,
            $pageIdNotion,
        );

        if ($data) {
            var_dump(count($data));
            echo "<p>The page with id = $key is no longer relevant and has been deleted.</p>";
        } else {
            echo 'Error retrieving Notion page.';
        }
    }
}
