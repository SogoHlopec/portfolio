<?php
/*
* Plugin Name: Custom stamp for WC
* Description: Adds stamps to orders with linkage to each customer.
* Author URI:  https://github.com/SogoHlopec
* Author: Yura Daineka
* Version: 1.1
* License: GPL2
*/


// Adding a meta field when creating a new order
add_action('woocommerce_checkout_create_order', 'add_custom_stamp_meta_to_order', 10, 2);
function add_custom_stamp_meta_to_order($order, $data)
{
    $order->update_meta_data('_billing_custom_stamp', '1');
}

// Adding a meta field when creating an order through the admin area
add_action('woocommerce_admin_order_data_after_order_details', 'add_custom_stamp_meta_to_admin_order', 10, 1);
function add_custom_stamp_meta_to_admin_order($order)
{
    if (!$order->get_meta('_billing_custom_stamp')) {
        $order->update_meta_data('_billing_custom_stamp', '1');
    }
}

// Saving the meta field value when saving an order
add_action('woocommerce_process_shop_order_meta', 'save_custom_stamp_meta_in_admin_order', 10, 1);
function save_custom_stamp_meta_in_admin_order($order_id)
{
    if (!get_post_meta($order_id, '_billing_custom_stamp', true)) {
        update_post_meta($order_id, '_billing_custom_stamp', '1');
    }
}

// Adding the “Stamp” field to the order editing interface
add_action('woocommerce_admin_order_data_after_billing_address', 'display_custom_stamp_in_admin_order', 10, 1);
function display_custom_stamp_in_admin_order($order)
{
    $order_id = $order->get_id();

    $custom_stamp = get_post_meta($order_id, '_billing_custom_stamp', true);

    if ($custom_stamp !== '') {
        echo '<p><strong>' . __('Stamp') . ':</strong> ' . esc_html($custom_stamp) . '</p>';
    }
}

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Load the file with WP_List_Table class 
if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

// Create our class that inherits WP_List_Table 
class Clients_WP_List_Table extends WP_List_Table
{
    private $customers_data;
    private $orders_count_by_customer;
    private $stamps_count_by_customer;

    // Create columns of our table 
    function get_columns()
    {
        $columns = array(
            'customer_id' => 'Customer id',
            'user_id' => 'User id',
            'first_name' => 'Name',
            'username' => 'Username',
            'date_last_active' => 'Last active',
            'date_registered' => 'Date registered',
            'email' => 'Email',
            'orders_count' => 'Orders',
            'stamps_count' => 'Stamps',
        );

        return $columns;
    }

    function prepare_items()
    {

        $this->customers_data = $this->get_customers_data();
        $this->orders_count_by_customer = $this->get_oreders_count_by_customer();
        $this->stamps_count_by_customer = $this->get_stamps_count_by_customer();

        usort($this->customers_data, array(&$this, 'usort_reorder'));

        $perPage = 25;

        // Set pagination data
        $this->set_pagination_args(array(
            'total_items' => count($this->customers_data),
            'per_page'    => $perPage,
        ));

        // Dividing the array into parts for pagination
        $this->customers_data = array_slice(
            $this->customers_data,
            (($this->get_pagenum() - 1) * $perPage),
            $perPage
        );

        // Set column data
        $primary = 'name';
        $this->_column_headers = array(
            $this->get_columns(), // Get an array of colocate names
            $this->get_hidden_columns(), // Get an array of column names to hide
            $this->get_sortable_columns(), // We get an array of column names that can be sorted
            $primary
        );


        $this->items = $this->customers_data;
    }

    public function get_hidden_columns()
    {
        return array();
    }

    public function get_sortable_columns()
    {
        return array(
            'customer_id' => ['customer_id'],
            'user_id' => ['user_id'],
            'first_name' => ['first_name'],
            'username' => ['username'],
            'date_last_active' => ['date_last_active'],
            'date_registered' => ['date_registered'],
            'email' => ['email'],
            'orders_count' => ['orders_count'],
            'stamps_count' => ['stamps_count'],
        );
    }

    // Sorting function
    function usort_reorder($a, $b)
    {
        // If no sort, default to date_last_active
        $orderby = (!empty($_GET['orderby'])) ? $_GET['orderby'] : 'date_last_active';
        // If no order, default to desc
        $order = (!empty($_GET['order'])) ? $_GET['order'] : 'desc';
        // Determine sort order
        $result = strcmp($a[$orderby], $b[$orderby]);
        // Send final sort direction to usort
        return ($order === 'asc') ? $result : -$result;
    }

    // Get data from the database 
    private function get_customers_data()
    {
        global $wpdb;

        $table = $wpdb->prefix . 'wc_customer_lookup';

        //  Get customer data
        $customersData =  $wpdb->get_results(
            "SELECT * from {$table}",
            ARRAY_A
        );

        return $customersData;
    }

    private function get_oreders_count_by_customer()
    {
        global $wpdb;

        // Get the number of orders per customer
        $ordersCountData = $wpdb->get_results(
            "
            SELECT customer_id, COUNT(*) as orders_count
            FROM {$wpdb->prefix}wc_order_stats
            GROUP BY customer_id",
            ARRAY_A
        );

        $oredersCountByCustomer = [];
        foreach ($ordersCountData as $index => $item) {
            $oredersCountByCustomer[$item['customer_id']] = $item['orders_count'];
        }

        return $oredersCountByCustomer;
    }

    private function get_stamps_count_by_customer()
    {
        global $wpdb;

        // Get stamps count per customer
        $stampsCountData = $wpdb->get_results(
            "
            SELECT 
                email_meta.meta_value AS _billing_email,
                COALESCE(SUM(stamp_meta.meta_value), 0) AS _billing_custom_stamp
            FROM 
                {$wpdb->prefix}postmeta AS email_meta
            LEFT JOIN 
                {$wpdb->prefix}postmeta AS stamp_meta 
            ON 
                email_meta.post_id = stamp_meta.post_id 
                AND stamp_meta.meta_key = '_billing_custom_stamp'
            WHERE 
                email_meta.meta_key = '_billing_email'
            GROUP BY 
                email_meta.meta_value",
            ARRAY_A
        );

        $stampsCountByCustomer = [];
        foreach ($stampsCountData as $index => $item) {
            $stampsCountByCustomer[$item['_billing_email']] = $item['_billing_custom_stamp'];
        }

        return $stampsCountByCustomer;
    }

    function column_default($item, $columnName)
    {
        $customerId = $item['customer_id'];
        $customerEmail = $item['email'];

        switch ($columnName) {
            case 'customer_id':
                return $item[$columnName];
            case 'user_id':
                return $item[$columnName];
            case 'first_name':
                if ($item['last_name']) {
                    return $item['first_name'] . ' ' . $item['last_name'];
                } else {
                    return $item[$columnName];
                }
                break;
            case 'username':
                return $item[$columnName];
            case 'date_last_active':
                $date = explode(' ', $item[$columnName])[0];
                return $date;
            case 'date_registered':
                $date = explode(' ', $item[$columnName])[0];
                return $date;
            case 'email':
                return $item[$columnName];
            case 'orders_count':
                return isset($this->orders_count_by_customer[$customerId]) ? $this->orders_count_by_customer[$customerId] : 0;
                break;
            case 'stamps_count':
                return isset($this->stamps_count_by_customer[$customerEmail]) ? $this->stamps_count_by_customer[$customerEmail] : 0;
                break;
            default:
                return $item[$columnName];
        }
    }
}

function update_order_stamps($order_id, $stamps)
{
    // Load the order object
    $order = wc_get_order($order_id);

    if ($order) {
        // Update the meta field
        $order->update_meta_data('_billing_custom_stamp', $stamps);
        $order->save();

        return true;
    }

    return false;
}

// Add a link to the plugin in the admin menu 
function my_add_menu_items()
{
    add_menu_page('Clients', 'Clients', 'activate_plugins', 'Clients_WP_List_Table', 'clients_list_init');
}
add_action('admin_menu', 'my_add_menu_items');


function clients_list_init()
{

    $update_message = '';
    // Handle form submission
    if (isset($_POST['order_id']) && isset($_POST['stamps'])) {
        $order_id = intval($_POST['order_id']);
        $stamps = intval($_POST['stamps']);


        if (update_order_stamps($order_id, $stamps)) {
            $update_message = 'Order stamps updated successfully.';
        } else {
            $update_message = 'Failed to update order stamps. Please check the order ID and try again.';
        }
    }

    // Create an instance of the Clients_WP_List_Table class
    $table = new Clients_WP_List_Table();

    echo '<div class="wrap"><h2>Client List</h2>';

    // Display update message
    if ($update_message) {
        echo '<div id="message" class="updated notice is-dismissible"><p>' . $update_message . '</p></div>';
    }

    echo '<form method="post">
        <h4 style="margin-bottom: 0;">Update stamps in the order</h4>
        <div style="display: flex; gap: 16px;">
            <p><label for="order_id">Order ID: </label><input type="number" name="order_id" id="order_id" required></p>
            <p><label for="stamps">Stamps: </label><input type="number" name="stamps" id="stamps" required></p>
            <p><input type="submit" value="Update" class="button button-primary"></p>
        </div>
    </form>
    ';

    echo '<form method="post">';
    // Forming a table 
    $table->prepare_items();
    // Display table 
    $table->display();
    echo '</form></div>';
}

// Function for receiving the number of stamps by email
function get_customer_stamps_count($email)
{
    global $wpdb;

    $totalStampCount = $wpdb->get_results(
        "
        SELECT 
    COALESCE(SUM(stamp_meta.meta_value), 0) AS _billing_custom_stamp_count
    FROM 
        wp_postmeta AS email_meta
    LEFT JOIN 
        wp_postmeta AS stamp_meta 
    ON 
        email_meta.post_id = stamp_meta.post_id 
        AND stamp_meta.meta_key = '_billing_custom_stamp'
    WHERE 
        email_meta.meta_key = '_billing_email'
        AND email_meta.meta_value = '{$email}';

        ",
        ARRAY_A
    );

    return $totalStampCount[0]['_billing_custom_stamp_count'];
}

function create_woocommerce_coupon($couponCode, $discount, $email)
{
    if (!wc_get_coupon_id_by_code($couponCode)) {

        $date = new DateTime('now', new DateTimeZone('Europe/Warsaw'));
        $date->modify('+14 days');
        $newDate = $date->format('Y-m-d');

        $coupon = new WC_Coupon();

        $coupon->set_code($couponCode); // Coupon code
        $coupon->set_discount_type('percent'); // discount type can be 'fixed_cart', 'percent' or 'fixed_product', defaults to 'fixed_cart'
        $coupon->set_amount($discount); // Discount amount
        $coupon->set_date_expires($newDate); // coupon expiry date
        $coupon->set_individual_use(true); // individual use only
        $coupon->set_email_restrictions([$email]); // allowed emails
        $coupon->set_usage_limit(1); // usage limit per coupon
        $coupon->set_usage_limit_per_user(1); // usage limit per user

        $coupon->save();
    }
}

// Function for adding stamp information to email
function add_stamps_to_email($order, $sent_to_admin, $plain_text, $email)
{
    $billingEmail = $order->get_billing_email();
    $totalStampCount = get_customer_stamps_count($billingEmail);
    $stampCount = 0;
    $discount = 50;
    $maxStamps = 5;
    $imagePath = get_site_url() . '/wp-content/plugins/custom-prosushi-stamp/image.png';
    $output = '
            <div class= "stamps" style="width: 100%;
                margin-bottom: 40px;"
            >
                <table  class="stamps__wrapper" style="width: 100%; max-width: 320px; margin: 0 auto; padding: 20px; border: 1px solid #e5e5e5; border-collapse: collapse;"
                >
                    <thead>
                        <tr>
                            <th
                                class="stamps__title"
                                colspan="5"
                                style="
                                    font-size: 18px;
                                    font-weight: bold;
                                    padding: 10px;
                                    text-align: center;
                                "
                            >
                                Twoje pieczątki
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="stamps__images" style="text-align: center">
        ';

    if (!empty($totalStampCount)) {
        if ($totalStampCount % $maxStamps === 0) {
            $stampCount = $maxStamps;
        } else {
            $stampCount = (int) $totalStampCount % $maxStamps;
        }
    } else {
        $stampCount = 0;
    }

    if ($stampCount === $maxStamps) {
        $orderId = $order->get_id();
        $couponCode = 'coupon' . $orderId;
        create_woocommerce_coupon($couponCode, $discount, $billingEmail);

        for ($i = 0; $i < $maxStamps; $i++) {
            $output .= '<td
                            class="stamps__image"
                            style="height: 60px; width: 60px; padding: 5px"
                        >
                            <div style="height: 50px; width: 50px; padding:5px; border: 1px solid #e5e5e5;">
                                <img src="' . $imagePath . '" alt="" style="
                                width: 100%;
                                height: 100%;
                                object-fit: contain;
                                margin: 0;"
                                >
                            </div>
                        </td>';
        }

        $output .= '</tr>
                        <tr>
                            <td
                                class="stamps__text"
                                colspan="5"
                                style="padding: 10px; text-align: center"
                            >
                                Twój kupon na zniżkę <span style="color: #f51d1d;font-size: 18px; font-weight: bold;">-' . $discount . '%</span> (ważny przez 2 tygodnie): <span style="color: #f51d1d;font-size: 18px; font-weight: bold;">' . $couponCode . '</span>
                            </td>
                        </tr>
                    </tbody>
                </table >
            </div>';

        // $output .= '<p>Total number of stamps: ' . $totalStampCount . '/5</p>';
    } else {
        // Add images
        for ($i = 0; $i < $maxStamps; $i++) {
            if ($i < $stampCount) {
                $output .= '<td
                            class="stamps__image"
                            style="height: 60px; width: 60px; padding: 5px"
                            >
                                <div style="height: 50px; width: 50px; padding: 5px; border: 1px solid #e5e5e5;">
                                    <img src="' . $imagePath . '" alt="" style="
                                    width: 100%;
                                    height: 100%;
                                    object-fit: contain;
                                    margin: 0;"
                                    >
                                </div>
                            </td>';
            } else {
                $output .= '<td
                            class="stamps__image_mr_css_attr"
                            style="
                                height: 60px;
                                width: 60px;
                                padding: 5px;"
                            >
                                <div style="height: 50px; width: 50px; padding: 5px; background: #e5e5e5; border: 1px solid #e5e5e5;"></div>
                            </td>';
            }
        }

        $output .= '</tr>
                        <tr>
                            <td
                                class="stamps__text"
                                colspan="5"
                                style="padding: 10px; text-align: center"
                            >
                                Brakuje tylko <span style="color: #f51d1d;font-size: 18px; font-weight: bold;">' . ($maxStamps - $stampCount) . '</span> pieczątki i będziesz mógł/a skorzystać z rabatu: <span style="color: #f51d1d;font-size: 18px; font-weight: bold;">-' . $discount . '%</span>
                            </td>
                        </tr>
                    </tbody>
                </table >
            </div>';

        // $output .= '<p>Total number of stamps: ' . $totalStampCount . '/' . $maxStamps . '</p>';
    }

    echo $output;
}

// Hook to add stamp information to emails
add_action('woocommerce_email_order_meta', 'add_stamps_to_email', 10, 4);
