<?php

/*
 * Plugin Name: Custom coefficient
 * Description: Adds a button for the custom factor in the admin panel. Добавляет кнопку для кастомного коэффициента в админ панели.
 * Author URI:  https://github.com/SogoHlopec
 * Author: Yura Daineka
 * Version: 1.0
 */

// Add a menu item to the admin menu
function custom_coefficient()
{
    add_menu_page(
        'Custom Coefficient',
        'Custom Coefficient',
        'manage_options',
        'custom_coefficient_page',
        'custom_coefficient_page_content'
    );
}
add_action('admin_menu', 'custom_coefficient');

// Define the content of the custom page
function custom_coefficient_page_content()
{
    // Check if the form is submitted
    if (isset($_POST['submit'])) {
        // Save the coefficient value to the database
        update_option('coefficient', $_POST['coefficient']);
        echo '<div class="updated"><p>Coefficient saved successfully!</p></div>';
    }
?>
    <div class="wrap">
        <h2>Custom Coefficient</h2>
        <form method="post" action="">
            <label for="coefficient">Enter coefficient:</label>
            <input type="text" id="coefficient" name="coefficient" value="<?php echo get_option('coefficient'); ?>" />
            <input type="submit" name="submit" class="button-primary" value="Save" />
        </form>
    </div>
<?php
}
