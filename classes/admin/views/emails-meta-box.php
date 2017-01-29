<?php

function dan_seminar_events_email_metabox() {
	add_meta_box('dsr_email_metabox', 'Registrants email templates', 'dsr_email_metabox', 'tribe_events', 'normal', 'default');
}

// The email template Metabox

function dsr_email_metabox() {
	global $post;

	// Noncename needed to verify where the data originated
	echo '<input type="hidden" name="eventmeta_noncename" id="eventmeta_noncename" value="' .
	wp_create_nonce( plugin_basename(__FILE__) ) . '" />';

	// Get the email meta data data if its already been entered
	$enableRegistration = get_post_meta($post->ID, '_EnableRegistration', true);
	$bookingInPerson = get_post_meta($post->ID, '_BookingEmailInPersonInstructions', true);
  $bookingZoom = get_post_meta($post->ID, '_BookingEmailZoomInstructions', true);

	$reminderZoom = get_post_meta($post->ID, '_ReminderEmailZoomURL', true);

	$enableRegistration = ($enableRegistration=="true")?'checked':'';
	//$reminder = get_post_meta($post->ID, '_ReminderEmail', true);

  echo '
				<div>
					<p><label><input id="_EnableRegistration" value="true" type="checkbox" name="_EnableRegistration" '.$enableRegistration.' onclick="testFun(this)"/> Enable Registration for this event</label></p>
				</div>
				<div>Example Email :
        <div style="background-color:#f9f9f9;border:1px solid #ddd;padding:.5em">
          <p>Hi <strong>Participant Name</strong></p>
          <p style="color:red">content of the email</p>
          <p>Regards</br>eSocSci Team</p>

        </div>
				<p><strong>Relavant email will not send if a content box left empty.</strong></p>
				</div>';

	// Echo out the field
  echo "<p>Booking Email Template for in-person participants: </p>";
	echo '<textarea id="booking1"   placeholder="Instructions for in-person participants" name="_BookingEmailInPersonInstructions" class="widefat" >' . $bookingInPerson  . '</textarea>';

  // Echo out the field
  echo "<p>Booking Email Template for video-conference participants: </p>";
	echo '<textarea id="booking2" placeholder="Instructions for zoom joining participants" name="_BookingEmailZoomInstructions" class="widefat" >' . $bookingZoom  . '</textarea>';

	// Echo out the field
	echo "<p>Reminder Email Template for video-conference participants: </p>";
	echo '<textarea id="reminder1" placeholder="Reminder for zoom joining participants" name="_ReminderEmailZoomURL" class="widefat" >' . $reminderZoom  . '</textarea>';

	echo "<script>

		function testFun(el){
			if(el.checked){
				document.getElementById('booking1').disabled = false;
				document.getElementById('booking2').disabled = false;
				document.getElementById('reminder1').disabled = false;
			}else{
				document.getElementById('booking1').disabled = true;
				document.getElementById('booking2').disabled = true;
				document.getElementById('reminder1').disabled = true;
			}
		}

		testFun(document.getElementById('_EnableRegistration'));
	</script>";
}

// Save the Metabox Data

function save_dsr_email_metabox_values($post_id, $post) {

	// verify this came from the our screen and with proper authorization,
  if ( !wp_verify_nonce( $_POST['eventmeta_noncename'], plugin_basename(__FILE__) )) {
    return $post->ID;
	}

	// Is the user allowed to edit the event?
	if ( !current_user_can( 'edit_post', $post->ID ))
		return $post->ID;

	//authenticated, putting all the meta values into an array for easy navigation

	$events_meta['_EnableRegistration'] = $_POST['_EnableRegistration'];
  $events_meta['_BookingEmailInPersonInstructions'] = $_POST['_BookingEmailInPersonInstructions'];
	$events_meta['_BookingEmailZoomInstructions'] = $_POST['_BookingEmailZoomInstructions'];
	$events_meta['_ReminderEmailZoomURL'] = $_POST['_ReminderEmailZoomURL'];

	// Add values of $events_meta as custom fields
	foreach ($events_meta as $key => $value) { // Cycle through the $events_meta array!
		if( $post->post_type == 'revision' ) return; // Don't store custom data twice
		$value = implode(',', (array)$value); // If $value is an array, make it a CSV (unlikely)
		if(get_post_meta($post->ID, $key, FALSE)) { // If the custom field already has a value
			update_post_meta($post->ID, $key, $value);
		} else { // If the custom field doesn't have a value
			add_post_meta($post->ID, $key, $value);
		}
		if(!$value) delete_post_meta($post->ID, $key); // Delete if blank
	}

}

add_action('save_post', 'save_dsr_email_metabox_values', 1, 2); // save the custom fields
