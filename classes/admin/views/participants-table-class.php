<?php

if( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Participants_List_Table extends WP_List_Table {
  var $data = null;
	var $in_person_participants = 0;
	var $video_conference_participants = 0;
	var $all_participants = 0;

  function get_data(){
    global $wpdb;
    $table_name = $wpdb->prefix . 'dan_seminar_register';
		$seminar_id = $_REQUEST['seminar'];
		$action = $_REQUEST['action'];
    $data=array();

    if(isset($seminar_id) && isset($action) && $action=="view" ){

			if(isset($_REQUEST['s'])){
				$search=$_REQUEST['s'];
	      $search = trim($search);

				$participants = $wpdb->get_results("SELECT seminar_id, name, user_email, participation_method, booking_email_sent, reminder_email_sent
					 																	FROM $table_name
																						WHERE seminar_id=$seminar_id
																						AND name LIKE '%$search%'");//and column_name_four='value'"

			}else{
				$participants =$wpdb->get_results("SELECT seminar_id, name, user_email, participation_method, booking_email_sent, reminder_email_sent
					 																	FROM $table_name
																						WHERE seminar_id=$seminar_id");
			}
      // $wk_post = $wpdb->get_results("SELECT ID, post_title,post_type FROM $table_name WHERE post_title LIKE '%$search%' and post_status='publish' and post_type='tribe_events'" );//and column_name_four='value'"
    }
		$seminar_id = array();
    $name = array();
    $user_email = array();
		$participation_method = array();
		$booking_email_sent = array();
		$reminder_email_sent = array();
    $i=0;


    foreach ($participants as $participant) {
        $seminar_id[]= $participant->seminar_id;
				$name[]=$participant->name;
        $user_email[]=$participant->user_email;
        $participation_method[]=$participant->participation_method;
				$booking_email_sent[]=$participant->booking_email_sent;
        $reminder_email_sent[]=$participant->reminder_email_sent;

        $data[] = array(
                'seminar_id' => $seminar_id[$i],
                'name'  => $name[$i],
                'user_email' =>   $user_email[$i],
                'participation_method' => $participation_method[$i],
								'booking_email_sent' => $booking_email_sent[$i],
                'reminder_email_sent' => $reminder_email_sent[$i]
                );
        $i++;
    }

		$this->all_participants = $this->count_participants($participation_method,'all');
		$this->in_person_participants = $this->count_participants($participation_method,'in-person');
		$this->video_conference_participants = $this->count_participants($participation_method,'video-conference');


    return $data;
  }

  function __construct(){
    global $status, $page;
        parent::__construct( array(
            'singular'  => __( 'Participant', 'dan-seminar-register' ),     //singular name of the listed records
            'plural'    => __( 'Participants', 'dan-seminar-register' ),   //plural name of the listed records
            'ajax'      => false        //does this table support ajax?
    ) );
    add_action( 'admin_head', array( &$this, 'admin_header' ) );
  }

  function admin_header() {
    $page = ( isset($_GET['page'] ) ) ? esc_attr( $_GET['page'] ) : false;
    if( 'dan-seminar-subscriber' != $page )
    return;
    echo '<style type="text/css">';
		echo '.wp-list-table .column-booking_email_sent { text-align:center; }';
    echo '.wp-list-table .column-reminder_email_sent { text-align:center; }';
    echo '</style>';
  }
  function no_items() {
    _e( 'No participants found.' );
  }
  function column_default( $item, $column_name ) {
    switch( $column_name ) {
        case 'name':
        case 'user_email':
        case 'participation_method':
				case 'booking_email_sent':
        case 'reminder_email_sent':
            return $item[ $column_name ];
        default:
            return print_r( $item, true ) ; //Show the whole array for troubleshooting purposes
    }
  }
  function get_sortable_columns() {
    $sortable_columns = array(
      'name'  => array('name',false),
			'user_email' => array('user_email',false),
      'participation_method' => array('participation_method',false)
    );
    return $sortable_columns;
  }
	// function get_bulk_actions() {
  //   $actions = array(
  //     'send_email'    => 'Send Reminder Email for Zoom participants'
  //   );
  //   return $actions;
  // }
  function get_columns(){
          $columns = array(

              'name' => __( 'Name', 'mylisttable' ),
              'user_email'    => __( 'Email', 'mylisttable' ),
							'participation_method'      => __( 'Participation Method', 'mylisttable' ),
							'booking_email_sent'      => __( 'Booking Email', 'mylisttable' ),
              'reminder_email_sent'      => __( 'Reminder Email', 'mylisttable' ),
          );
           return $columns;
  }
  function usort_reorder( $a, $b ) {
    // If no sort, default to title
    $orderby = ( ! empty( $_GET['orderby'] ) ) ? $_GET['orderby'] : 'name';
    // If no order, default to asc
    $order = ( ! empty($_GET['order'] ) ) ? $_GET['order'] : 'asc';
    // Determine sort order
    $result = strcmp( $a[$orderby], $b[$orderby] );
    // Send final sort direction to usort
    return ( $order === 'asc' ) ? $result : -$result;
  }
	function column_booking_email_sent($item){
		$color = array('0'=> '#888', '1'=>'green', '2'=>'red');

    return sprintf('<div title="N/A" class="wpseo-score-icon a" style="background:%s;"></div>', $color[$item['booking_email_sent']]);
  }
	function column_reminder_email_sent($item){
		$color = array('0'=> '#888', '1'=>'green', '2'=>'red');
    return sprintf('<div title="N/A" class="wpseo-score-icon a" style="background:%s;"></div>', $color[$item['reminder_email_sent']]);
  }
  // function get_bulk_actions() {
  //   $actions = array(
  //     'delete'    => 'Delete'
  //   );
  //   return $actions;
  // }

	function count_participants($arr, $text){
		switch ($text) {
			case 'all':
				return count($arr);
				break;

			case 'in-person':
				$counts = array_count_values($arr);
				return $counts[$text];
				break;

			case 'video-conference':
				$counts = array_count_values($arr);
				return $counts[$text];
				break;

			default:
				# code...
				return 0;
				break;
		}
	}
  function prepare_items() {

    $this->data = $this->get_data();
    $columns  = $this->get_columns();
    $hidden   = array();
    $sortable = $this->get_sortable_columns();
    $this->_column_headers = array( $columns, $hidden, $sortable );
    usort( $this->data, array( &$this, 'usort_reorder' ) );
    $per_page = 20;
    $current_page = $this->get_pagenum();
    $total_items = count( $this->data );
    $this->set_pagination_args( array(
      'total_items' => $total_items,
      'per_page'    => $per_page
    ) );
    $this->items = array_slice( $this->data,( ( $current_page-1 )* $per_page ), $per_page );
  }
} //class
