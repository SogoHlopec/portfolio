<?php
require_once('wp-load.php');

if (current_user_can('administrator')) {
    $fileName = "data.csv";
    unlink($fileName);
    echo 'File deleted successfully';
} else {
    echo 'Access denied, insufficient rights.';
}
