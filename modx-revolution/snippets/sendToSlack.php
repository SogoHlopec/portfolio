<?php
// Send a message to slack

define('WEBHOOK_URL', 'https://hooks.slack.com/services/YOUR_KEY');

try {
    $values = $hook->getValues();

    if ($values['messenger'] === 'telegram') {
        return true;
    }

    $checkOrderUrl = 'https://' . $_SERVER['HTTP_HOST'] . '/order?';
    $data = array(
        'Test' => $values['test'] ?: '',
    );
    $message = "*Test*\n";
    foreach ($data as $key => $value) {
        if (!is_array($value)) {
            $message .= "*$key:* $value\n";
        } else {
            $message .= "*$key:* " . implode(", ", $value) . "\n";
        }
    }
    $payload = json_encode(['text' => $message]);
    
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => WEBHOOK_URL,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        CURLOPT_POSTFIELDS => $payload,
        CURLOPT_FAILONERROR => true,
        CURLOPT_RETURNTRANSFER => true,
    ]);
    $result = curl_exec($ch);
    $curlError = curl_error($ch);
    curl_close($ch);
    if ($result === false) {
        $modx->log(modX::LOG_LEVEL_ERROR, "CURL error: {$curl_error}");
    } else {
        $modx->log(modX::LOG_LEVEL_INFO, "Slack API response: {$result}");
    }
    return true;
} catch (\Throwable $th) {
    $modx->log(1, $th);
    return true;
}