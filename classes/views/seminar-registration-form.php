<?php
global $wpdb;

//loading the booking email
include_once(dirname( __FILE__ ).'/../templates/booking_email.php');

$table_name = $wpdb->prefix . 'dan_seminar_register';


if(isset($_REQUEST['seminar-register-form']) && $_REQUEST['seminar-register-form']=='set'){

  //form is set
  $name = $_REQUEST['dsr-modal-name'];
  $email = $_REQUEST['dsr-modal-email'];
  $participation_method = $_REQUEST['dsr-modal-participation-method'];
  $seminar_id = $_REQUEST['dsr-modal-seminar-id'];

  $registered = $wpdb->get_results("SELECT ID FROM $table_name	WHERE seminar_id = '$seminar_id' AND user_email = '$email'");




  $startDate=get_post_meta($seminar_id, "_EventStartDateUTC", true);
  $startDate = DateTime::createFromFormat( 'Y-m-d H:i:s', $startDate,new DateTimeZone('UTC'));
  $startDatetimestamp = $startDate->getTimestamp();

  $hour = 1*60*60;//hour in seconds

  $currentTimestamp = time();

  if($startDatetimestamp>$currentTimestamp && ($startDatetimestamp-$currentTimestamp)>$hour ){
    if(count($registered)==0){
      $wpdb->insert(
        $table_name,
        array(
          'seminar_id' => $seminar_id,
          'user_email' => $email,
          'time' => current_time( 'mysql' ),
          'name' => $name,
          'participation_method' => $participation_method
        )
      );

      //queueing the booking email

        add_action('plugins_loaded', 'sendBookingMail');



    }else{

    }
  }


}
