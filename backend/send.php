<?php

// echo '<pre>';
// var_dump($_POST);
// echo '</pre>';
$data = json_decode(file_get_contents('php://input'), true);

if ($data['action'] === 'send') {
    $name = trim($data['name']);
    $email = trim($data['email']);
    $seminar = trim($data['seminar']);

    if (empty($name) || empty($email) || empty($seminar)) {
        return json_encode([
            'status' => 'error',
            'message' => 'Все поля должны быть заполнены.',
        ]);
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return json_encode([
            'status' => 'error',
            'message' => 'Некорректный email.',
        ]);
    }

    $to = $email;
    $subject = 'Заявка на семинар';
    $message = "Имя: $name\nEmail: $email\nСеминар: $seminar";

    try {
        $result = mail($to, $subject, $message);
        if ($result) {
            return json_encode([
                'status' => 'success',
                'message' => 'Заявка отправлена.',
            ]);
        } else {
            return json_encode([
                'status' => 'error',
                'message' => 'Ошибка при отправке заявки. Попробуйте позже.',
            ]);
        }
    } catch (\Throwable $th) {
        return json_encode([
            'status' => 'error',
            'message' => 'Ошибка при отправке заявки. Попробуйте позже.',
        ]);
    }
}
