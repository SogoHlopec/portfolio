<?php
define('TELEGRAM_TOKEN', 'YOUR_TOKEN');
define('CHAT_ID', 'YOUR_CHAT_ID');

function sendInTelegramBot($message)
{
    try {
        $text = "<b>Title</b>\n";
        $text .= "$message\n";

        $url = 'https://api.telegram.org/bot' . TELEGRAM_TOKEN . '/sendMessage';
        $response = wp_remote_post($url, array(
            'body' => array(
                'chat_id' => CHAT_ID,
                'text' => $text,
                'parse_mode' => 'html'
            )
        ));

        echo "A message to the Telegram bot has been sent: $message\n";
    } catch (\Throwable $e) {
        echo 'Error: ',  $e->getMessage(), "\n";
    }
}
