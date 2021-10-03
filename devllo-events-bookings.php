<?php
/*
    Plugin Name: Bookings for Devllo Events
    Plugin URI: https://devlloplugins.com/
    Description: This plugin adds a ticketing function to the Devllo Events plugin
    Author: Devllo Plugins
    Version: 0.1
    Author URI: https://devllo.com/
    Text Domain: devllo-events-bookings
    Domain Path: /languages
 */

// Exit if accessed directly

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

if ( (in_array( 'devllo-events/devllo-events.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) )) ) {
/**
 * Current plugin version.
 */
define( 'DEVLLO_EVENTS_BOOKINGS_VERSION', '0.1' );
define( 'DEVLLO_EVENTS_BOOKINGS_URI', plugin_dir_url( __FILE__ ) );
define( 'DEVLLO_EVENTS_BOOKINGS_DIR', dirname(__FILE__) );

if ( ! class_exists( 'Devllo_Events_Bookings' ) ) {

    class Devllo_Events_Bookings {

        public function __construct(){
        include( 'inc/devllo-events-bookings-activator.php');  
        include( 'inc/devllo-events-bookings-functions.php'); 
        include( 'inc/devllo-events-bookings-form.php');
        include( 'inc/devllo-events-bookings-checkout-fields.php');
        include( 'inc/devllo-events-bookings-email-function.php');

        include( 'payments/payments.php'); 

        add_action( 'wp_enqueue_scripts', array( $this, 'devllo_events_reg_enqueue_scripts' ) );

        include( 'admin/devllo-events-bookings-dashboard.php'); 
        include( 'admin/devllo-events-bookings-admin.php'); 

        register_activation_hook( __FILE__, array( 'Devllo_Events_Bookings_Activator', 'devllo_events_bookings_database' ));
        register_activation_hook( __FILE__, array( 'Devllo_Events_Bookings_Activator', 'devllo_events_bookings_activate' ));
        }

        function devllo_events_reg_enqueue_scripts() {   
            wp_enqueue_style( 'devllo-events-dashboard', DEVLLO_EVENTS_BOOKINGS_URI. 'inc/css/styles.css');	
        }

    }

    
}
new Devllo_Events_Bookings();
}else
{
    function devllo_events_bookings_pmpro_admin_notice(){
    echo '<div class="notice notice-error is-dismissible"><p>The Devllo Events plugin is not installed.</p></div>';
    }
    add_action('admin_notices', 'devllo_events_bookings_pmpro_admin_notice');
}