<?php


/**
 * Devllo Events Bookings Admin Settings Page
 *
 * @link       https://devllo.com/
 * @since      1.0.0
 *
 * @package    Devllo_Events
 * @subpackage Devllo_Events/includes
 */


/**
 * Prevent loading file directly
 */

defined( 'ABSPATH' ) || exit;

class Devllo_Events_Bookings_Admin_Settings{

    private static $_instance = null;
    
    public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
    }

    public function __construct() {
	  add_action( 'admin_init', array( $this, 'init_settings'  ) );
	  add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	  add_action ('devllo_events_settings_page_item', array($this, 'devllo_events_bookings_add_page_option'));

      add_action('devllo_events_admin_menu_item', array($this, 'devllo_events_bookings_menu_item'));
	}


    function devllo_events_bookings_menu_item(){
        add_submenu_page( 'edit.php?post_type=devllo_event', __('Devllo Events Payment Page', 'devllo-events-bookings'), __('Payment', 'devllo-events-bookings'), 'manage_options', 'devllo-events-bookings-admin-settings', array( $this, 'devllo_events_bookings_payment_page'  )); 
    }
	
    public function init_settings() {
      register_setting( 'devllo-events-bookings-payment', 'devllo-events-bookings-payment-radio' );
	  register_setting( 'devllo-events-bookings-payment', 'devllo-events-bookings-pbc-instruction' );
	  register_setting( 'devllo-events-pages', 'devllo-event-registration-page' );
	  register_setting( 'devllo-events-pages', 'devllo-event-checkout-page' );


    }

	function devllo_events_bookings_add_page_option(){
        ?>
        <tr>
			<th style="text-align: left;"><?php _e('Register Page', 'devllo-events-bookings'); ?></th>
			<td>
			<em><?php _e('This page should include the shortcode', 'devllo-events-bookings');?> [devllo-register]</em>
			<?php   
			wp_dropdown_pages( array( 
				'name' => 'devllo-event-registration-page', 
				'show_option_none' => __( '— Select —' ), 
				'option_none_value' => '0', 
				'selected' => get_option('devllo-event-registration-page'),
				));
			?>
			</td>
			<td><a target="_blank" href="<?php echo esc_url( get_permalink(get_option('devllo-event-registration-page')) ); ?>" class="button button-secondary"><?php _e('View Page', 'devllo-events'); ?></a></td>
			</tr>

			<tr>
			<th style="text-align: left;"><?php _e('Booking/Chekout Page', 'devllo-events-bookings'); ?></th>
			<td>
			<em><?php _e('This page should include the shortcode', 'devllo-events-bookings');?> [devllo-checkout]</em>
			<?php   
			wp_dropdown_pages( array( 
				'name' => 'devllo-event-checkout-page', 
				'show_option_none' => __( '— Select —' ), 
				'option_none_value' => '0', 
				'selected' => get_option('devllo-event-checkout-page'),
				));
			?>
			</td>
			<td><a target="_blank" href="<?php echo esc_url( get_permalink(get_option('devllo-event-checkout-page')) ); ?>" class="button button-secondary"><?php _e('View Page', 'devllo-events'); ?></a></td>
			</tr>
        <?php
    }

	function enqueue_scripts() {   

        $my_current_screen = get_current_screen();

        if ( isset( $my_current_screen->base ) && 'devllo_event_page_devllo-events-bookings-admin-settings' === $my_current_screen->base ) {
            wp_enqueue_style( 'dashboard-css', DEVLLO_EVENTS_ADMIN_URI. 'assets/css/dashboard.css');
			
			wp_enqueue_style( 'devllo-events-admin-css', DEVLLO_EVENTS_ADMIN_URI. 'assets/css/style.css');	

        }       
  
      }

    
	public static function devllo_events_bookings_payment_page(){
	  $adminpagetitle = get_admin_page_title();
	  
	  ?>
		<div style="width: 100%;">
		</div>
        <?php
		$active_tab = "devllo_events_bookings_payment";
		$tab = filter_input(
			INPUT_GET, 
			'tab', 
			FILTER_CALLBACK, 
			['options' => 'esc_html']
		);
        if( isset( $tab ) ) {
            $active_tab = $tab;
		  } ?>
		<div class="wrapper">

        <!-- SideBar Starts Here -->
		  <?php // Add Sidebar
		 devllo_add_sidebar (); 
		  ?>
        <!-- SideBar Ends -->

		<div class="main">
			<nav class="navbar navbar-expand navbar-light navbar-bg">
				<a class="sidebar-toggle d-flex">
          		<img src="<?php echo DEVLLO_EVENTS_URI . 'icon-256x256.png'; ?>">

            	</a>

				<div class="navbar-collapse collapse">
					<ul class="navbar-nav navbar-align">
					
						<li class="nav-item dropdown">
							<a class="nav-icon dropdown-toggle" href="#" id="messagesDropdown" data-bs-toggle="dropdown">
								<div class="position-relative">
									<i class="align-middle" data-feather="message-square"></i>

								</div>
							</a>
						</li>
					</ul>
				</div>
			</nav>

			<main class="content">
				<div class="container-fluid p-0">

					<h1 class="h3 mb-3"><?php _e('Payment', 'devllo-events'); ?></h1>

					<div class="row">
						<div class="col-md-3 col-xl-3">

							<div class="card">
								<div class="card-header">
									<h5 class="card-title mb-0"></h5>
								</div>

								<div class="list-group list-group-flush" role="tablist">
								

									<a class="list-group-item list-group-item-action <?php echo $active_tab == 'devllo_events_pages' ? 'nav-tab-active' : ''; ?>" data-bs-toggle="list" href="?page=devllo-events-settings&tab=devllo_events_pages&post_type=devllo_event" role="tab">
									<?php _e('Payment', 'devllo-events'); ?></a>

								</div>
							</div>
						</div>

						<div class="col-md-9 col-xl-9">
							<div class="tab-content">
								<div class="tab-pane fade show active" id="account" role="tabpanel">

			
								<div class="card" style="max-width: none;">
										<div class="card-header">

											<h5 class="card-title mb-0"></h5>
										</div>
										<div class="card-body">
										<form method="post" action="options.php">
							<?php

		
							if ( $active_tab == 'devllo_events_bookings_payment' ) {
								settings_fields( 'devllo-events-bookings-payment' );
								do_settings_sections( 'devllo-events-bookings-payment' );
								?>
									<table class="table">
									
									<tr>
									<th colspan=3 style="text-align: left;">
									<h3><?php _e('Payment Gateway', 'devllo-events'); ?></h3></th>
									<td></td>
									<td></td>
									</tr>
									<tr>

									<th colspan=3 style="text-align: left;"><?php _e('Choose a Payment Gateway', 'devllo-events'); ?></th>
									<td></td>
									<td></td>
									</tr>
									<tr>
									<td colspan=3>
									
									<input type="radio" id="devllo-events-bookings-payment-radio" name="devllo-events-bookings-payment-radio" value="pbc" <?php checked('pbc', get_option('devllo-events-bookings-payment-radio'), true); ?>>
									<?php _e('Pay By Check', 'devllo-events-bookings'); ?>
									<br/>

									<input type="radio" id="devllo-events-bookings-payment-radio" name="devllo-events-bookings-payment-radio" value="offsite" <?php checked('offsite', get_option('devllo-events-bookings-payment-radio'), true); ?>>
									<?php _e('Offsite Payment Gateway', 'devllo-events-bookings'); ?></td>
									<td></td>
									</tr>
									<?php
									$pbc_instructions = get_option('devllo-events-bookings-pbc-instruction');

									if (get_option('devllo-events-bookings-payment-radio') == 'pbc'){
								?>
								<tr>
							
								<td colspan=3>
								
								<?php 
								_e('Pay By Check Payment Instructions', 'devllo-events-bookings');
								
								?>
								
								<textarea id="devllo-events-bookings-pbc-instruction" name="devllo-events-bookings-pbc-instruction" rows="3" cols="50" class="large-text" spellcheck="false"><?php if (isset($pbc_instructions)) { echo esc_attr($pbc_instructions); }?></textarea>
								<p class="description"><?php _e('Bank Details for Check Payment and additional instructions needed.', 'devllo-events-bookings'); ?></p>
								</td>
								<td></td>
								</tr>
								<?php
								
							}
							elseif (get_option('devllo-events-bookings-payment-radio') == 'offsite')
							{
								?>				<tr>
								
								<td colspan=3>
								
								<?php 
									_e('Please add the ticket link on the event page', 'devllo-events-bookings');
								
								?>
									</td>
								<td></td>
								</tr>
								<?php }

						
								?>
								</table>
								<?php
							}
							submit_button();
							?>
							</form>										
														</div>
												</div>
												</div>
							
											</div>
										</div>
									</div>

								</div>
							</main>

						<footer class="footer">
							<div class="container-fluid">
								<div class="row text-muted">
									<div class="col-6 text-start">
										<p class="mb-0">
											<a href="https://devlloplugins.com/" class="text-muted"><strong>Devllo Plugins</strong></a> &copy;
										</p>
									</div>
									<div class="col-6 text-end">
										<ul class="list-inline">
											<li class="list-inline-item">
												<a class="text-muted" href="https://devlloplugins.com/support/"><?php _e('Support', 'devllo-events-bookings'); ?></a>
											</li>
											<li class="list-inline-item">
												<a class="text-muted" href="https://devlloplugins.com/documentations/events-by-devllo-documentation/"><?php _e('Help Center', 'devllo-events-bookings'); ?></a>
											</li>
											<!--
											<li class="list-inline-item">
												<a class="text-muted" href="#">Privacy</a>
											</li>
											<li class="list-inline-item">
												<a class="text-muted" href="#">Terms</a>
											</li>
												-->
										</ul>
									</div>
								</div>
							</div>
						</footer>
						
					</div>
				</div>
			<?php
    }

}
Devllo_Events_Bookings_Admin_Settings::instance();
