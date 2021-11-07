<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Devllo_Events_Bookings_Email {
    public function __construct(){
        add_action('devllo_events_bookings_after_add_attendee', array($this, 'devllo_send_organiser_email_after_checkout'));
        add_action('devllo_events_bookings_after_add_attendee', array($this, 'devllo_send_email_after_checkout'));
        add_filter('wp_mail_content_type', array($this, 'devllo_events_format_email'));        
    }


    public function devllo_send_organiser_email_after_checkout(){
        global $wp_locale;

        $current_user = wp_get_current_user();

        if (isset($_COOKIE['devllo_event_post_id'] )){

        $event_id = intval($_COOKIE['devllo_event_post_id']);

        }

        $event_title = get_the_title( $event_id );
        $event_link = get_permalink( $event_id );

        $organiser_id = get_post_field( 'post_author', $event_id );
        $organiser_email = get_the_author_meta( 'user_email', $organiser_id );

        $to = $organiser_email;
        $subject = 'New User signed up for an event'; 
        $message = 'A user has registered to attend <a href="' . $event_link . ' ">' . $event_title . '</a>.';

        wp_mail( $to, $subject, $message );
        
    }


    public function devllo_send_email_after_checkout(){
        global $wp_locale;

        $current_user = wp_get_current_user();

        if (isset($_COOKIE['devllo_event_post_id'] )){

            $event_id = intval($_COOKIE['devllo_event_post_id']);

        }

        $event_title = get_the_title( $event_id );
        $event_link = get_permalink( $event_id );

        // Event Online Link
        $event_online_link = get_post_meta( $event_id, 'devllo_event_event_link_key', true );
        $url = get_post_meta( $event_id, 'devllo_event_url_key', true );

        $map_location = get_post_meta( $event_id, 'devllo_event_location_key', true );

        $content_post = get_post($event_id);
        $content = $content_post->post_content;

        // Event Price
        $event_price = get_post_meta( $event_id, 'devllo_event_price_key', true );

        $startday = get_post_meta( $event_id, '_start_day', true );
        $startmonth = get_post_meta( $event_id, '_start_month', true );
        $startyear =  get_post_meta( $event_id, '_start_year', true );
        $startweekday = date("l", mktime(0, 0, 0, $startmonth, $startday, $startyear));

        $endday = get_post_meta( $event_id, '_end_day', true );
        $endmonth = get_post_meta( $event_id, '_end_month', true );
        $endyear =  get_post_meta( $event_id, '_end_year', true );
        $endweekday = date("l", mktime(0, 0, 0, $endmonth, $endday, $endyear));

        $startdate = $startweekday . ', ' . get_post_meta( $event_id, '_start_day', true ). ', ' . $wp_locale->get_month($startmonth) . ' ' . get_post_meta( $event_id, '_start_year', true ) . '<br/>Time: ' . get_post_meta($event_id, '_start_hour', true) . ':' . get_post_meta($event_id, '_start_minute', true);
        $enddate =  $endweekday . ', ' . get_post_meta( $event_id, '_end_day', true ). ', ' . $wp_locale->get_month($endmonth) . ' ' . get_post_meta( $event_id, '_end_year', true ) . '<br/>Time: '. get_post_meta($event_id, '_end_hour', true) . ':' . get_post_meta($event_id, '_end_minute', true);                  


        $location_name = get_post_meta( $event_id, 'devllo_event_location_name_key', true );

        $organiser_id = get_post_field( 'post_author', $event_id );
        $organiser_email = get_the_author_meta( 'user_email', $organiser_id );

        $to = $current_user->user_email;
        $subject = $event_title; 
        $message = 'You have successfully registered to attend <a href="' . $event_link . ' ">' . $event_title . '</a>.';
        $message .= '<br/>Thank you for registering. <br/>';
        $message .= '<br/>See below for the event details';
        $message .= '<br/>' . $content;

        if(!empty($event_price)){ 
        $message .= '<br/>Event Cost: $' .$event_price;
        }

        if(!empty($event_online_link)){
        $message .= '<br/><br/>Online Link: ' .$event_online_link;
        }

        if(!empty($map_location)){
        $message .= '<br/><br/>Address: ' .$map_location;
        }

        if(!empty($url)){
        $message .= '<br/><br/>Event Website: ' .$url;
        }

        $message .= '<br/><br/>Start Date: ' .$startdate;
        $message .= '<br/>End Date: ' .$enddate;

        if(!empty($location_name)){ 
        $message .= '<br/><br/>Location: ' .$location_name;
        }

        $message .= '<br/><br/>Please submit any questions to ' .$organiser_email;

        wp_mail( $to, $subject, $message );

    }

    // Format Emails
    function devllo_events_format_email(){
        return 'text/html';
    }
}

new Devllo_Events_Bookings_Email();