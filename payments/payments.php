<?php
function devllo_events_payment_form() {

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
                    
            ?></div>
            <br/>
            <button class="btn btn-primary" type="submit" id="submit"><?php  _e('Accept', 'devllo-events-registration'); ?></button>
            </form>
            <?php
            if($_SERVER['REQUEST_METHOD']=='POST'){ 

                _e('You will be redirected to the event page.', 'devllo-events-bookings'); ?>
                <br/>
                <?php
            
                $Devllo_Events_Bookings_Functions = new Devllo_Events_Bookings_Functions;
            
                $Devllo_Events_Bookings_Functions->devllo_events_add_attendee();
   
                echo '<script type="text/javascript">
                        setTimeout(function(){
                            window.location.href = "'. get_permalink( $value ) .'"
                        }, 5000);
                        console.log('.$value.')
                    </script>';
            }

        }
        else{
            do_action('devllo_events_bookings_payment_checkout');
        }

	}
	
	else
	
	{
		 _e('Please select an event to check out', 'devllo-events-bookings');
	}
    return ob_get_clean();

}
add_shortcode('devllo-checkout', 'devllo_events_payment_form');
