<?php

class Devllo_Events_Bookings_Form {

        public function __construct(){
            add_shortcode( 'devllo_register', array($this, 'devllo_events_bookings_form'));
            add_action( 'init', array( $this, 'devllo_events_add_new_user' ) );
        }

    // user bookings login form
    function devllo_events_bookings_form() {
    
        // only show the bookings form to non-logged-in members
        if(!is_user_logged_in()) {
    
            // check if bookings is enabled
           // $bookings_enabled = get_option('users_can_register');

            if ( ! get_option( 'users_can_register' ) ) { 
            update_option( 'users_can_register', true ); 
            }

                $output = $this->devllo_events_bookings_fields();
   
            return $output;
        }
    }

        // bookings form fields
        function devllo_events_bookings_fields() {
            
            ob_start(); ?>	
                <h3 class="devllo_events_reg_header"><?php _e('Register New Account'); ?></h3>
                
                <?php 
                // show any error messages after form submission
                $this->devllo_events_bookings_messages(); ?>
                
                <form id="devllo_events_bookings_form" class="devllo_events_form" action="" method="POST">
                    <fieldset>
                        <p>
                            <label for="devllo_events_bookings_user_Login"><?php _e('Username'); ?></label>
                            <input name="devllo_events_bookings_user_login" id="devllo_events_bookings_user_login" class="devllo_events_bookings_user_login" type="text"/>
                        </p>
                        <p>
                            <label for="devllo_events_bookings_user_email"><?php _e('Email'); ?></label>
                            <input name="devllo_events_bookings_user_email" id="devllo_events_bookings_user_email" class="devllo_events_bookings_user_email" type="email"/>
                        </p>
                        <p>
                            <label for="devllo_events_bookings_user_first"><?php _e('First Name'); ?></label>
                            <input name="devllo_events_bookings_user_first" id="devllo_events_bookings_user_first" type="text" class="devllo_events_bookings_user_first" />
                        </p>
                        <p>
                            <label for="devllo_events_bookings_user_last"><?php _e('Last Name'); ?></label>
                            <input name="devllo_events_bookings_user_last" id="devllo_events_bookings_user_last" type="text" class="devllo_events_bookings_user_last"/>
                        </p>
                        <p>
                            <label for="password"><?php _e('Password'); ?></label>
                            <input name="devllo_events_bookings_user_pass" id="password" class="password" type="password"/>
                        </p>
                        <p>
                            <label for="password_again"><?php _e('Password Again'); ?></label>
                            <input name="devllo_events_bookings_user_pass_confirm" id="password_again" class="password_again" type="password"/>
                        </p>
                        <p>
                            <input type="hidden" name="devllo_events_csrf" value="<?php echo wp_create_nonce('devllo-eb-csrf'); ?>"/>
                            <input type="submit" value="<?php _e('Register Your Account'); ?>"/>
                        </p>
                    </fieldset>
                </form>
            <?php
            return ob_get_clean();
        }

        // Register a new user
        function devllo_events_add_new_user() {
            if (isset( $_POST["devllo_events_bookings_user_login"] ) && wp_verify_nonce($_POST['devllo_events_csrf'], 'devllo-eb-csrf')) {
            $user_login		= $_POST["devllo_events_bookings_user_login"];

            if (isset( $_POST["devllo_events_bookings_user_email"] )){
            $user_email		= $_POST["devllo_events_bookings_user_email"];
            }

            if (isset( $_POST["devllo_events_bookings_user_first"] )){
            $user_first 	= $_POST["devllo_events_bookings_user_first"];
            }

            if (isset( $_POST["devllo_events_bookings_user_last"] )){
            $user_last	 	= $_POST["devllo_events_bookings_user_last"];
            }

            if (isset( $_POST["devllo_events_bookings_user_pass"] )){
            $user_pass		= $_POST["devllo_events_bookings_user_pass"];
            }

            if (isset( $_POST["devllo_events_bookings_user_pass_confirm"] )){
            $pass_confirm 	= $_POST["devllo_events_bookings_user_pass_confirm"];
            }
            
            // this is required for username checks
          //  require_once(ABSPATH . WPINC . '/bookings.php');
            
            if(username_exists($user_login)) {
                // Username already registered
                $this->devllo_events_bookings_errors()->add('username_unavailable', __('Username already taken'));
            }
            if(!validate_username($user_login)) {
                // invalid username
                $this->devllo_events_bookings_errors()->add('username_invalid', __('Invalid username'));
            }
            if($user_login == '') {
                // empty username
                $this->devllo_events_bookings_errors()->add('username_empty', __('Please enter a username'));
            }
            if(!is_email($user_email)) {
                //invalid email
                $this->devllo_events_bookings_errors()->add('email_invalid', __('Invalid email'));
            }
            if(email_exists($user_email)) {
                //Email address already registered
                $this->devllo_events_bookings_errors()->add('email_used', __('Email already registered'));
            }
            if($user_pass == '') {
                // passwords do not match
                $this->devllo_events_bookings_errors()->add('password_empty', __('Please enter a password'));
            }
            if($user_pass != $pass_confirm) {
                // passwords do not match
                $this->devllo_events_bookings_errors()->add('password_mismatch', __('Passwords do not match'));
            }
            
            $errors = $this->devllo_events_bookings_errors()->get_error_messages();
            
            // if no errors then cretate user
            if(empty($errors)) {
                
                $new_user_id = wp_insert_user(array(
                        'user_login'		=> $user_login,
                        'user_pass'	 		=> $user_pass,
                        'user_email'		=> $user_email,
                        'first_name'		=> $user_first,
                        'last_name'			=> $user_last,
                        'user_registered'	=> date('Y-m-d H:i:s'),
                        'role'				=> 'subscriber'
                    )
                );
                if($new_user_id) {
                    // send an email to the admin
                    wp_new_user_notification($new_user_id);
                    
                    // log the new user in
                    wp_set_auth_cookie($user_login, $user_pass, true);
                    wp_set_current_user($new_user_id, $user_login);	
                    do_action('wp_login', $user_login);


                    $creds = array();
                    $creds['user_login'] = $user_login;
                    $creds['user_password'] = $user_pass;
                    $creds['remember']      = true;
        
                    wp_signon( $creds, true );
                    
                    // send the newly created user to the home page after logging them in
                    update_option( 'users_can_register', false );

                    wp_redirect(home_url('/events')); exit;
                }
                
            }
        
        }
        }

        // used for tracking error messages
        function devllo_events_bookings_errors(){
            static $wp_error; // global variable handle
            return isset($wp_error) ? $wp_error : ($wp_error = new WP_Error(null, null, null));
        }

        // displays error messages from form submissions
        function devllo_events_bookings_messages() {
            $devllo_events_bookings_errors = $this->devllo_events_bookings_errors();

            if($codes = $this->devllo_events_bookings_errors()->get_error_codes()) {
                echo '<div class="devllo_events_bookings_errors">';
                    // Loop error codes and display errors
                foreach($codes as $code){
                        $message = $this->devllo_events_bookings_errors()->get_error_message($code);
                        echo '<span class="error"><strong>' . __('Error') . '</strong>: ' . $message . '</span><br/>';
                    }
                echo '</div>';
            }	
        }

    }

new Devllo_Events_Bookings_Form();