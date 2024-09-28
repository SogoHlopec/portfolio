<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
// Connecting WordPress
require_once('wp-load.php');

// Check if the current user has administrator rights
if (current_user_can('administrator')) {

    // Check if WooCommerce exists
    if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
        // Request all WooCommerce orders
        $orders = wc_get_orders(array(
            'limit' => -1, // Getting all the orders
        ));

        // Checking for orders
        if (!empty($orders)) {
            // Create an array for storing order data
            $orderData = array();

            // For each order we get the required data
            foreach ($orders as $order) {
                // Create a temporary array to store data about products in an order
                $productsInfo = array();

                // Get all the elements of the order
                $items = $order->get_items();

                // For each order item we get information about the product and its quantity
                foreach ($items as $item) {
                    $productName = $item->get_name();
                    $quantity = $item->get_quantity();

                    $productsInfo[] = "$productName (Количество: $quantity)";
                }
                // Receiving order information
                $orderInfo = array(
                    'ID' => $order->get_id(),
                    'Дата' => $order->get_date_created()->format('Y-m-d H:i:s'),
                    'Номер телефона' => $order->get_billing_phone(),
                    'Имя' => $order->get_formatted_billing_full_name(),
                    'Адресс' => $order->get_shipping_address_1() . ' ' . $order->get_shipping_address_2() . ' ' . $order->get_shipping_postcode(),
                    'Email' => $order->get_billing_email(),
                    'Сумма заказа' => $order->get_total(),
                    'Состав заказа' => implode(", ", $productsInfo),
                );

                $orderData[] = $orderInfo;
            }

            $currentDate = date('d_m_Y_h_m_s');
            $fileName = "data.csv";
            $csvFile = fopen($fileName, 'w');

            fputcsv($csvFile, array_keys($orderData[0]));

            foreach ($orderData as $row) {
                fputcsv($csvFile, $row);
            }

            fclose($csvFile);

            header("Location: $fileName");
        } else {
            echo 'No orders for export.';
        }
    } else {
        echo 'WooCommerce is not installed.';
    }
} else {
    echo 'Access denied, insufficient rights.';
}
