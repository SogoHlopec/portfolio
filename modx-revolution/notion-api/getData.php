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
        $idPage = $properties['id']['number'];
        $price = $properties['Price']['number'];
        $content = $properties['Content']['rich_text'][0]['text']['content'];
        $name = $properties['Name']['title'][0]['text']['content'];
        //  If you need to get pictures
        $src = $properties["src"]["files"][0]["file"]["url"];
        $imageName = $properties["scr"]["files"][0]["name"];
        //...

        // Outputting data to a page
        echo "<p>{$name} + {$price} + {$content}</p><br>";

        // We save pictures to the site
        if ($src && $imageName) {
            // we get a picture
            $imageContent = file_get_contents($src);

            // Path to save on server
            $imageCatalog = 'images/';
            $savePath = MODX_BASE_PATH . $imageCatalog;

            // Full name of the file on the server (including path)
            $filePath = $savePath . $imageName;

            // Checking for file availability
            if (!file_exists($filePath)) {
                // Save an image only if there is no file with the same name
                file_put_contents($filePath, $imageContent);

                echo "<p>{$imageName} saved </p><br>";
            } else {
                echo "<p>{$imageName} already on the server</p><br>";
            }
        }

        // Collecting data into an array
        $dataResource  = array(
            'price' => $price,
            'content' => $content,
            'name' => $name,
            'src' => $src,
            'filePath' => $imageCatalog . $imageName,
        );
        $result[] = $dataResource;
    }

    $catalogId = 3; // ID of the directory, parent of the new resources
    $templateId = 1; // id of the template that we use for new resources
    // $countCreatedResources = 0; // limit counter of 3 resources for tests

    foreach ($result as $item) {
        // If we check by pagetitle whether a resource with this exists in the directory
        // $existingResource = $modx->getObject('modResource', [
        //     'pagetitle' => $item['name'],
        //     'parent' => $catalogId,
        // ]);

        $resourceId = $item['idPage'];

        // Check if a resource with the same id exists and has the same parent
        $existingResource = $modx->getObject('modResource', $resourceId);
        if (!($existingResource && $existingResource->get('parent') == $catalogId)) {
            $existingResource = null;
        }
        // If a resource with the same id exists in such a parent, then update its data
        if ($existingResource !== null) {
            // Setting field values
            $existingResource->set('pagetitle', $item['name']);
            $existingResource->set('content', $item['content']);

            $existingResource->setTVValue('price', $item['price']);
            $existingResource->setTVValue('src', $item['filePath']);
            $existingResource->save();

            echo '<p>Resource with id = ' . $item['idPage'] . ' updated</p>';
        } else {
            // Otherwise, we create a new resource
            $newResource = $modx->newObject('modResource');
            $newResource->set('parent', $catalogId);
            $newResource->set('template', $templateId);

            // Setting field values
            $newResource->set('pagetitle', $item['name']);
            $newResource->set('content', $item['content']);

            $newResource->set('published', 1);
            $newResource->save(); // when creating, first save the resource, then add TV parameters

            $newResource->setTVValue('price', $item['price']);
            $newResource->setTVValue('src', $item['filePath']);

            // We fire the OnDocFormSave event to process plugins based on the event
            // $id = 0;
            $resource_temp = $newResource;
            $modx->invokeEvent('OnDocFormSave', array(
                'mode' => 'upd',
                // 'id' => $id,
                'resource' => $resource_temp,
                'reloadOnly' => false
            ));
            echo '<p>A new resource has been created ' . $item['name'] . '</p>';

            // limit of 3 resources for tests
            // $countCreatedResources++;
            // if ($countCreatedResources >= 3) {
            //     break;
            // }
        }
    }
} else {
    echo 'Error retrieving Notion page.';
}
