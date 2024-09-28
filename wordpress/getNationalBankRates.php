<?php
/*
* Plugin Name: ThemeName get National Bank rates
* Description: We get exchange rates from the National Bank. Получаем курсы валют от нацбанка.
* Author URI: https://github.com/SogoHlopec
* Author: Yura Daineka
* Version: 1.0
*/

// Add a function for querying the api and saving the result to an option (stored in the database)
function total_child_custom_update_currency_exchange_rate_eur()
{
    $response = json_decode(file_get_contents('https://api.nbrb.by/exrates/rates/451?periodicity=0'));
    if ($response && isset($response->Cur_OfficialRate)) {
        $exchangeRate = $response->Cur_OfficialRate;
        if ($exchangeRate) {
            update_option('currency_exchange_rate_eur', $exchangeRate);
        } else {
            update_option('currency_exchange_rate_eur', 0);
        }
        date_default_timezone_set('Europe/Minsk');
        $currentDateUpdate = date('d-m-Y H:i:s');
        update_option('currency_exchange_rate_eur_update', $currentDateUpdate);
    } else {
        update_option('currency_exchange_rate_eur', 0);
    }
}
add_action('total_child_custom_currency_update_daily', 'total_child_custom_update_currency_exchange_rate_eur');
// Adds a rates to the admin bar
function total_child_custom_add_rates_to_admin_bar($wp_admin_bar)
{
    $exchangeRate = get_option('currency_exchange_rate_eur');
    $lastUpdate = get_option('currency_exchange_rate_eur_update');
    $wp_admin_bar->add_menu(array(
        'id'    => 'menu_id',
        'title' => $exchangeRate . ' EUR/BYN ' . $lastUpdate,
    ));
}
add_action('admin_bar_menu', 'total_child_custom_add_rates_to_admin_bar', 1000);
// Getting the course when activating the plugin
function total_child_custom_update_currency_exchange_rate_activate()
{
    total_child_custom_update_currency_exchange_rate_eur();
}
register_activation_hook(__FILE__, 'total_child_custom_update_currency_exchange_rate_activate');
// Clearing data when deactivating the plugin
function total_child_custom_update_currency_exchange_rate_deactivate()
{
    delete_option('currency_exchange_rate_eur');
    delete_option('currency_exchange_rate_eur_update');
}
register_deactivation_hook(__FILE__, 'total_child_custom_update_currency_exchange_rate_deactivate');
// Enable cron scheduler
if (!wp_next_scheduled('total_child_custom_currency_update_daily')) {
    wp_schedule_event(time(), 'daily', 'total_child_custom_currency_update_daily');
}
// Create a shortcode to use in the admin and display the result on the page
function total_child_custom_converted_price($price)
{
    $exchangeRate = (float) get_option('currency_exchange_rate_eur');
    $convertedPrice = $price * $exchangeRate;
    $convertedPrice = round($convertedPrice, 2);
    return $convertedPrice;
}
function total_child_custom_converted_price_shortcode($atts)
{
    $priceEur = do_shortcode("[types field='price_eur' output='raw']");
    $convertedPrice = total_child_custom_converted_price($priceEur);
    return $convertedPrice;
}
add_shortcode('total_child_custom_converted_price', 'total_child_custom_converted_price_shortcode');
