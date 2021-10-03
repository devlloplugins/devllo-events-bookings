<?php


/**
 * Devllo Events Admin Attendees Dashboard
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

class Devllo_Events_Attendees_Dashboard {

    public function __construct() {
        add_action('devllo_events_admin_menu_item', array($this, 'devllo_events_attendees_menu_item'));
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        add_action( 'devllo_events_sidebar_item', array( $this, 'devllo_events_bookings_sidebar_item' ) );



    }

    function devllo_events_attendees_menu_item(){
        add_submenu_page( 'edit.php?post_type=devllo_event', __('Devllo Events Attendees Page', 'devllo-events-bookings'), __('Attendees', 'devllo-events-bookings'), 'manage_options', 'devllo-events-attendees-dashboard', array( $this, 'devllo_events_attendees_content'  )); 
    }

    function devllo_events_bookings_sidebar_item(){
         ?>
         
        <li class="sidebar-header"> <?php _e('Bookings', 'devllo-events-bookings'); ?></li>

        <li class="sidebar-item">
			<a class="sidebar-link" href="edit.php?post_type=devllo_event&page=devllo-events-attendees-dashboard">
              <i class="align-middle" data-feather="check-circle"></i> <span class="align-middle"><?php _e('Attendees', 'devllo-events-bookings'); ?></span>
            </a>
		</li>

        <li class="sidebar-item">
			<a class="sidebar-link" href="edit.php?post_type=devllo_event&page=devllo-events-bookings-admin-settings">
              <i class="align-middle" data-feather="check-circle"></i> <span class="align-middle"><?php _e('Payment', 'devllo-events-bookings'); ?></span>
            </a>
            </a>
		</li>
                    <?php
    }

    function enqueue_scripts() {   

        $my_current_screen = get_current_screen();

        if ( isset( $my_current_screen->base ) && 'devllo_event_page_devllo-events-attendees-dashboard' === $my_current_screen->base ) {
            wp_enqueue_style( 'dashboard-css', DEVLLO_EVENTS_ADMIN_URI. 'assets/css/dashboard.css');
			
			wp_enqueue_style( 'devllo-events-admin-css', DEVLLO_EVENTS_ADMIN_URI. 'assets/css/style.css');	

        }       
  
      }
	
    

    function devllo_events_attendees_content() {
        ?>
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

					<h1 class="h3 mb-3"><?php _e('Event Attendees', 'devllo-events-bookings'); ?></h1>

					<div class="row">

                    <div class="col-md-12 col-xl-12">
							<div class="tab-content">
								<div class="tab-pane fade show active" id="account" role="tabpanel">

			
								<div class="card" style="max-width: none;">
										<div class="card-header">

											<h5 class="card-title mb-0"></h5>
										</div>
										<div class="card-body">
										<form method="post" action="options.php">
        


                <table class="table table-hover my-0">
                    <thead>
                    <tr>
                    <th style="text-align: center;" class="d-none d-xl-table-cell">
                    <?php _e('Name', 'devllo-events-bookings') ; ?>
                    </th>
                    <th class="d-none d-xl-table-cell">
                    <?php _e('Email', 'devllo-events-bookings') ; ?>
                    </th>
                    <th class="d-none d-xl-table-cell">
                    <?php _e('Event', 'devllo-events-bookings') ; ?>
                    </th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                        global $wpdb;
                        $result = $wpdb->get_results (
                        "SELECT * FROM " . $wpdb->prefix . "devllo_events_participants" );
                        foreach ( $result as $print )   {
                            $post   = get_post( $print->event_id );
                            $event_url = get_permalink ($print->event_id );

                            $check_post_type = get_post_type($post);

                            $attendee_name = $print->name;
                            $attendee_email = $print->user_email;
                            $event_id = $print->event_id;
                            $user_id = $print->user_id;
                            if ($post){
                            $event_post_title = $post->post_title;
                            }

                            // check post type to avoid loading other post types
                            if ($check_post_type == 'devllo_event'){


                        ?>
                        <tr>
                        <td style="
    text-align: center;
">                         <picture>
                         <source srcset="<?php print get_avatar_url($print->user_id, ['size' => '51']); ?>" media="(min-width: 992px)"/>
                         <img src="<?php print get_avatar_url($print->user_id, ['size' => '40']); ?>"/>
                         </picture> <br/>
                        <?php
                         if ($attendee_name){ echo $attendee_name; }?></td>
                        <td><?php if ($attendee_email){ echo $attendee_email; }?></td>
                        <td><?php if ($event_id && $post) { echo '<a href="'.get_permalink ($event_id ).'">' . $event_post_title. '</a>'; }?></td>

                        </tr>
                        <?php } ?>
                        </tbody>
                            <?php }

                    ?>  
                                        </table>

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

new Devllo_Events_Attendees_Dashboard();