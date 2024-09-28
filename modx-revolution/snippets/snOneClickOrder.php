<?php
if (empty($_POST['phone'])) {
    return $FetchIt->error('В форме содержатся ошибки!', [
        'phone' => 'Вы не заполнили Телефон'
    ]);
} else {
    $ms2 = $modx->getService('minishop2');
    $ms2->initialize('web');
    $ms2->cart->clean();
    $count = 1;
    $ms2->cart->add((int)$_POST['id'], $count, ''); // add item to cart
    $ms2->order->config['json_response'] = true; // please return the json
    if ($_POST['name']) {
        $ms2->order->add('receiver', $_POST['name']);
    }
    if ($_POST['email']) {
        $ms2->order->add('email', $_POST['email']);
    }
    if ($_POST['message']) {
        $ms2->order->add('comment', $_POST['message']);
    }

    $phone = preg_replace('/[^0-9]/', '', $_POST['phone']);
    $ms2->order->add('phone', $phone);

    $ms2->order->add('delivery', 1);
    $ms2->order->add('payment', 1);
    $order = $ms2->order->submit(); // placing an order

    return $order;
}
