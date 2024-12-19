<?php
header('Content-Type: application/json');
$data = json_decode(file_get_contents('php://input'), true);

if ($data['action'] === 'send') {
    $name = trim($data['name']);
    $email = trim($data['email']);
    $seminar = trim($data['seminar']);

    if (empty($name) || empty($email) || empty($seminar)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Все поля должны быть заполнены.',
        ]);
        die();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Некорректный email.',
        ]);
        die();
    }

    $headers = "From: site@simple-seminar.atservers.net\r\n";
    $headers .= "Reply-To: $email\r\n";
    $to = $email;
    $subject = 'Заявка на семинар';
    $message = "Имя: $name\nEmail: $email\nСеминар: $seminar";

    try {
        $result = mail($to, $subject, $message, $headers);
        // $result = false;
        if ($result) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Заявка отправлена.',
            ]);
            die();
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Ошибка при отправке заявки. Попробуйте позже.',
            ]);
            die();
        }
    } catch (\Throwable $th) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Ошибка при отправке заявки. Попробуйте позже.',
        ]);
        die();
    }
}
