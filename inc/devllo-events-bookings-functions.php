<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Devllo_Events_Bookings_Functions {
	public function __construct(){
      //  add_action( 'add_meta_boxes', array($this, 'add_participant_meta_box') );
      //  add_action( 'save_post', array($this, 'save_participant_meta_box_data' ));
      add_action ('devllo_events_after_side_single_event', array($this, 'devllo_events_reg_button'));
      add_action ('devllo_events_after_side_single_event', array($this, 'devllo_events_reg_user'));
      add_action ('devllo_events_after_side_single_event', array($this, 'devllo_events_reg_login_func'));

      add_action ('wp_head', array($this, 'checkoutstartSession'), 1);
    
     // add_action ('devllo_events_after_side_single_event', array($this, 'devllo_events_reg_login_button'));

      add_action ('devllo_events_after_main_single_event', array($this, 'devllo_events_show_attendees'));

      add_shortcode( 'reg_login', array($this, 'devllo_events_reg_login_button'));

    }

    // Start Checkout Session
    function checkoutstartSession() {

            global $post;
            if ( 'devllo_event' === $post->post_type ) {

            $value = $post->ID;

            setcookie("devllo_event_post_id", $value, time()+300, '/');
            }
            print_r($_COOKIE);
    }

    // Create Bookings Button
    function devllo_events_reg_button(){
        global $post;
        
        $event_price = get_post_meta( $post->ID, 'devllo_event_price_key', true );

        // Event Status - Past, Ongoing, Upcoming
        $startcheckdate = get_post_meta( $post->ID, '_start_year', true ). '-' .get_post_meta( $post->ID, '_start_month', true ). '-' .get_post_meta( $post->ID, '_start_day', true );
        $endcheckdate = get_post_meta( $post->ID, '_end_year', true ). '-' .get_post_meta( $post->ID, '_end_month', true ). '-' .get_post_meta( $post->ID, '_end_day', true );

        $startchecktime = get_post_meta( $post->ID, '_start_hour', true ). ':' .get_post_meta( $post->ID, '_start_minute', true );
        $endchecktime = get_post_meta( $post->ID, '_end_hour', true ). ':' .get_post_meta( $post->ID, '_end_minute', true );

        if (new DateTime() > new DateTime("$endcheckdate $endchecktime")) {
        # current time is greater than 2010-05-15 16:00:00
        # in other words, 2010-05-15 16:00:00 has passed
        $bgstyle = 'bg-danger';
        $event_status = 'Past Event';
        }

        if (isset($event_status) && $event_status == 'Past Event'){
            ?>
            <br/>
            <input type="submit" name="devllo_attend_event" class="button" value="<?php echo __('This Event has ended', 'devllo-events-bookings');?>" /> 
            <?php
        }
        else
        { ?>  
            <form method="post" style="padding: 10px;">
            <div>
            <?php
            do_action("devllo_after_bookings_before_checkout_form"); 
            ?>
            </div>
            <input type="submit" name="devllo_attend_event" class="button" value="<?php echo __('Attend Event', 'devllo-events-bookings');?>" /> 
            </form>
         <?php }
     }

    function devllo_events_reg_login_button(){
        global $post;
        ?>
        <form method="post" style="padding: 10px;"> 
        <input type="submit" name="devllo_attend_event_login" class="button" value="<?php echo __('Log In', 'devllo-events-bookings');?>" /> 
        <input type="submit" name="devllo_attend_event_reg" class="button" value="<?php echo __('Register', 'devllo-events-bookings');?>" /> 
        </form>
        <?php
    }

    // Load Login or Bookings Page 
    function devllo_events_reg_login_func(){
        if(isset($_POST['devllo_attend_event_login'])) { 
            wp_login_form();
        }
        elseif(isset($_POST['devllo_attend_event_reg'])){
           wp_redirect( get_permalink(get_option('devllo-event-registration-page')) );
            // Get Register URL
         //   echo'<script> window.location="' .home_url('bookings'). '"; </script> ';

        }
    }

    // Check if USer is Registered, if not let them Register
    function devllo_events_reg_user(){
        global $post;
        $postID = $post->ID;
        if(isset($_POST['devllo_attend_event'])) { 
            if ( !is_user_logged_in() ) {
        
            echo do_shortcode( '[reg_login]' );
            _e('You need to Log in/Register as an attendee to attend this event', '');
            
            }
            else
            // Check if event is free or paid
            {
                $this->devllo_events_check_user_bookings();
            }
        }
    }

    function devllo_events_check_user_bookings()
    {
        global $post;

        $postID = $post->ID;

        global $wpdb;

        include_once(ABSPATH . 'wp-includes/pluggable.php');
    
        $user = wp_get_current_user();
        $user_id = $user->ID;
        $event_id = $postID;
        
        $id = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT id FROM " . $wpdb->prefix . "devllo_events_participants 
                WHERE user_id = %d AND event_id = %d LIMIT 1",
                $user_id, $event_id
        )
        );

        if ( $id > 0 )
        {
            echo 'You are already registered for this event';
        }
        else
            // Check if event is free or paid
        {
            $this->devllo_events_check_for_price();
        }

    }

    // Check for event price
    function devllo_events_check_for_price(){
        global $post;

        $postID = $post->ID;

        $event_price = get_post_meta( $post->ID, 'devllo_event_price_key', true );
        // If event is paid, load checkout page

        if (isset($event_price) && $event_price > 0){
            $this->devllo_events_redirect_to_checkout();

        } else {
        // If event is free, continue to add attendee function
            $this->devllo_events_add_free_attendee();
        }
    }

    // Load Checkout Page function
    function devllo_events_redirect_to_checkout(){

       $payment_gateway = get_option('devllo-events-bookings-payment-radio');

       if ($payment_gateway == "pbc"){ 
           $url = get_permalink(get_option('devllo-event-checkout-page'));   
        //   $url = get_site_url() . "/1526-2/";

           wp_redirect( $url );
        }
        elseif ($payment_gateway == "offsite"){
        global $post;
        $url = get_post_meta( $post->ID, 'devllo_event_url_key', true );

        if ($url){
           wp_redirect( $url );
        }

        } else {
           do_action('devllo_events_redirect_to_checkout_function');
        }
       // echo'<script> window.location="' . home_url('/1526-2/'). '"; </script> '; 
    }

    // Add user as attendee
    function devllo_events_add_free_attendee(){

        global $post;

       // $postID = $_COOKIE['devllo_event_post_id']; 

        $postID = $post->ID;

        global $wpdb;

        include_once(ABSPATH . 'wp-includes/pluggable.php');
    
        $user = wp_get_current_user();
        $user_id = $user->ID;
        $event_id = $postID;
        
        $id = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT id FROM " . $wpdb->prefix . "devllo_events_participants 
                WHERE user_id = %d AND event_id = %d LIMIT 1",
                $user_id, $event_id
        )
        );

        $table_name = $wpdb->prefix . 'devllo_events_participants';
    
        $wpdb->insert( 
        $table_name, 
        array( 
            'time' => current_time( 'mysql' ), 
            'name' => $user->user_login,
            'user_email' => $user->user_email,
            'user_id' => $user->ID,
            'event_id' => $postID, 
                //'text' => $welcome_text, 
            ) 
        );
        
        echo '<br/>You are Registered, you will be attending this event';

        do_action('devllo_events_bookings_after_add_attendee');
    
    }

    // Add Paid Attendee
    function devllo_events_add_attendee(){

        global $post;

       $postID = $_COOKIE['devllo_event_post_id']; 

      // $postID = $post->ID;

        global $wpdb;

        include_once(ABSPATH . 'wp-includes/pluggable.php');
    
        $user = wp_get_current_user();
        $user_id = $user->ID;
        $event_id = $postID;
        
        $id = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT id FROM " . $wpdb->prefix . "devllo_events_participants 
                WHERE user_id = %d AND event_id = %d LIMIT 1",
                $user_id, $event_id
        )
        );

        $table_name = $wpdb->prefix . 'devllo_events_participants';
    
        $wpdb->insert( 
        $table_name, 
        array( 
            'time' => current_time( 'mysql' ), 
            'name' => $user->user_login,
            'user_email' => $user->user_email,
            'user_id' => $user->ID,
            'event_id' => $postID, 
                //'text' => $welcome_text, 
            ) 
        );
        
        echo '<br/>You are Registered, you will be attending this event';

        do_action('devllo_events_bookings_after_add_attendee');
    
    }

    // Display Attendees Name and Avatar
    function devllo_events_show_attendees(){ ?>
        <section class="section">
        <h3><?php _e('Attendees', 'devllo-events-bookings') ?></h3>
        <div>
            <ul class="flex gridList">
        <?php
        global $wpdb;
        global $post;
        $postID = $post->ID;
        $event_id = $postID;

         $result = $wpdb->get_results (
            $wpdb->prepare(
                "SELECT * FROM " . $wpdb->prefix . "devllo_events_participants 
                WHERE event_id = %d",
                $event_id
            )
            );
        foreach ( $result as $print )   {
                 ?><li class="gridList-item flex-item">
            <picture>
            <source srcset="<?php print get_avatar_url($print->user_id, ['size' => '51']); ?>" media="(min-width: 992px)"/>
            <img src="<?php print get_avatar_url($print->user_id, ['size' => '40']); ?>"/>
            </picture><br/>
            <span class="attendee-name"><?php echo $print->name; ?></class>
        </li>

        <?php
        }
        ?> </ul></div> 
        </section>
        <?php
    }   
        
}

new Devllo_Events_Bookings_Functions();