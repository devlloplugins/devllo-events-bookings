<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Devllo_Events_Bookings_Activator {
	public function __construct(){
        add_action( 'admin_init', array( $this, 'load_bookings_plugin' ) );
        
    }

    public static function devllo_events_bookings_activate() { 
        add_option( 'Activated_Plugin', 'devllo-event-bookings' );

		$devllo_events_bookings_current_version = 0.1;

    }

    function load_bookings_plugin(){
		global $wpdb;
        if ( is_admin() && get_option( 'Activated_Plugin' ) == 'devllo-event-bookings' ) {
            delete_option( 'Activated_Plugin' );

            add_role('devllo_event_participant', __(
                'Participant'),
                array(
                    'read'            => true, // Allows a user to read
                )
                );

            	// Create Sign Up Page
			if ( null === $wpdb->get_row( "SELECT post_name FROM {$wpdb->prefix}posts WHERE post_name = 'registration'", 'ARRAY_A' ) ) {
				$current_user = wp_get_current_user();
				$new_page_content = '[devllo-register]';
				// create post object
				$page = array(
					'post_title'  => __( 'Sign Up' ),
					'post_status' => 'publish',
					'post_content' => $new_page_content,
					'post_author' => $current_user->ID,
					'post_type'   => 'page',
				  );
				  // insert the post into the database
					wp_insert_post( $page );
				}

			// Create Booking Page
			if ( null === $wpdb->get_row( "SELECT post_name FROM {$wpdb->prefix}posts WHERE post_name = 'booking'", 'ARRAY_A' ) ) {
				$current_user = wp_get_current_user();
				$new_page_content = '[devllo-checkout]';
				// create post object
				$page = array(
					'post_title'  => __( 'Booking' ),
					'post_status' => 'publish',
					'post_content' => $new_page_content,
					'post_author' => $current_user->ID,
					'post_type'   => 'page',
					);
					// insert the post into the database
					wp_insert_post( $page );
				}
            

        }

    }

	public static function devllo_events_bookings_database() {
		global $wpdb;
	
		$table_name = $wpdb->prefix . 'devllo_events_participants';
		
		$charset_collate = $wpdb->get_charset_collate();
	
		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			name tinytext NOT NULL,
            user_email varchar(100) NOT NULL,
            user_id mediumint(9) NOT NULL,
            event_id mediumint(9) NOT NULL,
			text text NOT NULL,
			url varchar(55) DEFAULT '' NOT NULL,
			PRIMARY KEY  (id)
		) $charset_collate;";
	
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
        
	
	}

  
}

new Devllo_Events_Bookings_Activator();
