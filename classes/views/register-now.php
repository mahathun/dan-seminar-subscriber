<?php

//displaying the Register Now button
function display_register_button(){
global $wpdb;
$seminar_id = get_the_ID();
$enableRegistration = get_post_meta($seminar_id, '_EnableRegistration', true);
$startDate=get_post_meta($seminar_id, "_EventStartDateUTC", true);
$startDate = DateTime::createFromFormat( 'Y-m-d H:i:s', $startDate,new DateTimeZone('UTC'));
$startDatetimestamp = $startDate->getTimestamp();

$hour = 1*60*60;//hour in seconds

$currentTimestamp = time();

  if($enableRegistration){
    if($startDatetimestamp>$currentTimestamp && ($startDatetimestamp-$currentTimestamp)>$hour ){
      //if event registration cut off time isn't met
      if(is_user_logged_in()){
         $current_user = wp_get_current_user();

         $email = esc_html($current_user->user_email);
         $table_name = $wpdb->prefix . 'dan_seminar_register';

         $registered = $wpdb->get_results(
           "
           SELECT ID
           FROM $table_name
           WHERE seminar_id = '$seminar_id'
             AND user_email = '$email'
           "
         );
         if(count($registered)>0){
           echo '<div class="alert alert-success" role="alert">
                    You have successfully registered for this event.
                 </div>';
         }else{
           register_button_and_modal();
         }
      }else{
          register_button_and_modal();
      }
    }else{
      //after event registration cut off time
      echo '<div class="alert alert-danger" role="alert">
               Registration for this event is now closed.
            </div>';
    }
  }
}


function register_button_and_modal(){
  echo "
      <div class=\"tribe-events-after-html dan-test-display\">
        <a data-toggle=\"modal\" href=\"#dan_seminar_modal\" class=\"btn btn-primary\">Register Now</a>
      </div>
  ";
  dan_seminar_register_modal();
}
