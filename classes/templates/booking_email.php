<?php

//booking email
function sendBookingMail(){
  if(isset($_REQUEST['seminar-register-form']) && $_REQUEST['seminar-register-form']=='set'){
    global $wpdb;
    $name = $_REQUEST['dsr-modal-name'];
    $email = $_REQUEST['dsr-modal-email'];
    $participation_method = $_REQUEST['dsr-modal-participation-method'];
    $seminar_id = $_REQUEST['dsr-modal-seminar-id'];
    $post_table = $wpdb->prefix . 'posts';
    $table_name = $wpdb->prefix . 'dan_seminar_register';

    $result = $wpdb->get_results("SELECT $post_table.post_title
      FROM $post_table
      
      WHERE $post_table.ID=$seminar_id");

    $title = "Booking Confirmation : ".$result[0]->post_title ;
    $greeting = "Hi " .$name;
    $signature = "Regards,\neSocSci Team.";
    $bookingContent = "";
    if($participation_method == 'in-person'){
      $bookingContent=get_post_meta($seminar_id, "_BookingEmailInPersonInstructions", true);
    }else if($participation_method == 'video-conference'){
      $bookingContent=get_post_meta($seminar_id, "_BookingEmailZoomInstructions", true);
    }

    $body = sprintf('%s<br/><br/>%s<br/><br/>%s', $greeting ,$bookingContent, $signature);

    //sending the booking email
    if(!empty($bookingContent)){
     $bookingMail = wp_mail( $email, $title, $body);
    }

    if($bookingMail=='1'){
      //updating the booking_email_sent field in the database;
      $wpdb->update( $table_name, array('booking_email_sent'=> 1), array('seminar_id'=>$seminar_id, 'user_email'=> $email) );
    }

  }
}
