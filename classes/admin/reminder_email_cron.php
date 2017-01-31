<?php

add_action('plugins_loaded', 'sendReminderEmail');

function sendReminderEmail(){
  global $wpdb;
  $table_name = $wpdb->prefix . 'dan_seminar_register';


  $results = $wpdb->get_results("SELECT  wp_posts.ID, wp_posts.post_title,
                                  pm1.meta_value as post_start_date,
                                  pm2.meta_value as post_end_date,
                                  pm3.meta_value as reminder_email_zoom_url,
                                  pm4.reminder_email_sent as reminder_email_sent,
                                  pm4.user_email as email,
                                  pm4.name as name
                                FROM wp_posts
                                LEFT JOIN wp_postmeta AS pm1 ON (wp_posts.ID = pm1.post_id AND pm1.meta_key='_EventStartDateUTC')
                                LEFT JOIN wp_postmeta AS pm2 ON (wp_posts.ID = pm2.post_id AND pm2.meta_key='_EventEndDateUTC')
                                LEFT JOIN wp_postmeta AS pm3 ON (wp_posts.ID = pm3.post_id AND pm3.meta_key='_ReminderEmailZoomUrl')
                                LEFT JOIN wp_dan_seminar_register AS pm4 ON (wp_posts.ID =pm4.seminar_id AND pm4.reminder_email_sent = '0')
                                WHERE wp_posts.post_type = 'tribe_events'
                                AND pm4.reminder_email_sent = '0'
                                AND pm4.participation_method = 'video-conference'
                                AND wp_posts.post_status = 'publish'
                                AND ((pm1.meta_key = '_EventStartDateUTC') OR (pm2.meta_key = '_EventEndDateUTC'))");


  foreach ($results as $result) {
    # code...
    $seminar_id = $result->ID;
    $email = $result->email;
    $startDate = DateTime::createFromFormat( 'Y-m-d H:i:s', $result->post_start_date,new DateTimeZone('UTC'));
    $startDatetimestamp = $startDate->getTimestamp();

    $endDate = DateTime::createFromFormat( 'Y-m-d H:i:s', $result->post_end_date,new DateTimeZone('UTC'));
    $endDatetimestamp = $endDate->getTimestamp();

    $currentTimestamp = time();
    // echo "<pre>";
    // printf("%s : %s", $startDatetimestamp, $currentTimestamp);
    // print_r($result);
    // echo "</pre>";
    if($endDatetimestamp > $currentTimestamp && $startDatetimestamp> $currentTimestamp ){

      $timeDifference =  $startDatetimestamp - $currentTimestamp;

      //if timeDifference less than 24 hours
      $oneDayInSeconds = 24*60*60;
      if($timeDifference < $oneDayInSeconds){

        $title = "Reminder : ".$result->post_title ;
        $greeting = "Hi ".$result->name;
        $signature = "Regards,eSocSci Team.";
        $zoomEmailContent = $result->reminder_email_zoom_url;

        $body = sprintf('%s<br/><br/>%s<br/><br/>%s', $greeting ,$zoomEmailContent, $signature);
        if(!empty($zoomEmailContent)){
          $reminderMail = wp_mail( $email, $title, $body);
        }

       if($reminderMail=='1'){
         //updating the booking_email_sent field in the database;
         $wpdb->update( $table_name, array('reminder_email_sent'=> 1), array('seminar_id'=>$seminar_id, 'user_email'=> $email) );
       }


      }

      //wp_mail('mahathun.online@gmail.com', 'test cron title', 'test cron body');
    }


  }





}
