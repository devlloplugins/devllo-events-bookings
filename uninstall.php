<?php

// if uninstall.php is not called by WordPress, die
if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

// Delete the table
global $wpdb;

$table_name = $wpdb->prefix . 'devllo_events_participants';
$sql = "DROP TABLE IF EXISTS $table_name";
$wpdb->query($sql);
delete_option("DEVLLO_EVENTS_BOOKINGS_VERSION");