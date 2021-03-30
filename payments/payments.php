<?php
function devllo_events_stripe_payment_form() {

    ob_start();

	if (isset($_COOKIE['devllo_event_post_id'] )){

		$value = $_COOKIE['devllo_event_post_id']; 

		$amount = get_post_meta( $value, 'devllo_event_price_key', true );

        $payment_gateway = get_option('devllo-events-bookings-payment-radio');

        $pbc_instructions = get_option('devllo-events-bookings-pbc-instruction');


if ($payment_gateway == "pbc"){

            echo "<form action='' method='POST' id='payment-form'><h3>Pay By Check </h3><div>";
            if (isset($pbc_instructions)) {
                 echo esc_attr($pbc_instructions); 
                }   
                
    ?></div><br/><button class="btn btn-primary" type="submit" id="submit"><?php  _e('Accept', 'devllo-events-registration'); ?></button>
    </form>
    <?php
    if($_SERVER['REQUEST_METHOD']=='POST'){ 

        echo "You will be redirected to the event page.<br/>";
    
        $Devllo_Events_Bookings_Functions = new Devllo_Events_Bookings_Functions;
    
        $Devllo_Events_Bookings_Functions->devllo_events_add_attendee();

        ?>
			<script>
				setTimeout(function(){
					window.location.href = '<?php get_permalink( $value ); ?>';
				}, 5000);
			</script>
        <?php
    }

}else{
    do_action('devllo_events_bookings_payment_checkout');
}


	}
	
	else
	
	{
		echo 'Please select an event to check out';
	}
    return ob_get_clean();

}
add_shortcode('devllo_checkout', 'devllo_events_stripe_payment_form');
