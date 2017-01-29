<?php
/*
Plugin Name: Seminar Subscriber for the Events Calendar PRO
Description: this plugin will allow users to subscribe to seminar events. Registered users will then get an booking email and a reminder email(video-conference participants)
Version: 1.0.0
Author: dan mahavithana
Author URI: http://www.danmahavithana.com
Text Domain:
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/


//admin section
require_once(dirname(__FILE__).'/classes/admin/reminder_email_cron.php');
require_once(dirname(__FILE__).'/classes/admin/views/seminar-table-class.php');
require_once(dirname(__FILE__).'/classes/admin/views/participants-table-class.php');
require_once(dirname(__FILE__).'/classes/admin/index.php');
require_once(dirname(__FILE__).'/classes/admin/views/emails-meta-box.php');
add_action( 'add_meta_boxes', 'dan_seminar_events_email_metabox' );
add_action('admin_menu', '_dan_seminar_subscriber_admin',11);




//installation hook to creat the tables and cron job
require_once (dirname( __FILE__ ).'/install.php');
register_activation_hook( __FILE__, 'dan_seminar_register_activate' );

//uninstallatin hook to remove the cron job
require_once(dirname(__FILE__).'/uninstall.php');

//seminar-registration-form handler
require_once(dirname(__FILE__).'/classes/views/seminar-registration-form.php');

//seminar-registration-modal
require_once(dirname(__FILE__).'/classes/views/seminar-registration-modal.php');


//initializing the plugin
function _seminar_subscriber_config(){
	//Plugin CSS
	wp_enqueue_style( 'styleSheet', plugins_url( 'styles/main.css', __FILE__ ) );

}

//displaying register now button
require_once(dirname(__FILE__).'/classes/views/register-now.php');

_seminar_subscriber_config();
add_action( 'tribe_events_single_event_after_the_meta', 'display_register_button' );
