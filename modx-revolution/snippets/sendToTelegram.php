<?php
// Send a message to telegram
// FetchIt snippet's hook

$CHAT_ID = 'YOUR_CHAT_ID';
$TELEGRAM_TOKEN = 'YOR_TOKEN';
try {
    $values = $hook->getValues();

    $checkOrderUrl = 'https://' . $_SERVER['HTTP_HOST'] . '/order?';
    $data = array(
        'Test' => $values['test'] ?: '',
    );
    $message = "<b>Title</b>\n";
    foreach ($data as $key => $value) {
        if (!is_array($value)) {
            $message .= "<b>$key: </b>$value\n";
        } else {
            $message .= "<b>$key: </b>" . implode(", ", $value) . "\n";
        }
    }
    $message = urlencode($message);
    $url = 'https://api.telegram.org/bot' . $TELEGRAM_TOKEN . '/sendMessage?chat_id=' . $CHAT_ID . '&parse_mode=html&text=' . $message;
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_FAILONERROR => true,
        CURLOPT_RETURNTRANSFER => true,
    ]);
    $result = curl_exec($ch);
    $curlError = curl_error($ch);
    curl_close($ch);
    if ($result === false) {
        $modx->log(modX::LOG_LEVEL_ERROR, "CURL error: {$curl_error}");
    } else {
        $modx->log(modX::LOG_LEVEL_INFO, "Telegram API response: {$result}");
    }
    return true;
} catch (\Throwable $th) {
    $modx->log(1, $th);
    return true;
}
