<?php
function dan_seminar_register_activate(){
	//setting up the database tables
	global $wpdb;
	  // $version = get_option( 'my_plugin_version', '1.0' );
		$charset_collate = $wpdb->get_charset_collate();
		$table_name = $wpdb->prefix . 'dan_seminar_register';

		$sql = "CREATE TABLE $table_name (
      id mediumint(9) NOT NULL AUTO_INCREMENT,
      seminar_id mediumint(9) NOT NULL,
			user_email varchar(50) NOT NULL,
      name varchar(255) NOT NULL,
      participation_method varchar(20) NOT NULL,
			booking_email_sent tinyint(1) DEFAULT '0',
			reminder_email_sent tinyint(1) DEFAULT '0',
			time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			UNIQUE KEY id (id)
		) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );




}
