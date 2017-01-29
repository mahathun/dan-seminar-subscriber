<?php

if( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Seminar_List_Table extends WP_List_Table {
  var $data = null;

  function get_data(){
    global $wpdb;
    $table_name = $wpdb->prefix . 'posts';
    $post_meta_table_name = $wpdb->prefix . 'postmeta';
    $data=array();

    if(isset($_REQUEST['s'])){
      $search=$_REQUEST['s'];
      $search = trim($search);
      // $wk_post = $wpdb->get_results("SELECT ID, post_title,post_type FROM $table_name WHERE post_title LIKE '%$search%' and post_status='publish' and post_type='tribe_events'" );//and column_name_four='value'"
      $wk_post = $wpdb->get_results("SELECT  wp_posts.ID, wp_posts.post_title, pm1.meta_value as post_start_date, pm2.meta_value as post_end_date, pm3.meta_value as enable_registration
                                    FROM wp_posts
                                    LEFT JOIN wp_postmeta AS pm1 ON (wp_posts.ID = pm1.post_id AND pm1.meta_key='_EventStartDate')
																		LEFT JOIN wp_postmeta AS pm2 ON (wp_posts.ID = pm2.post_id AND pm2.meta_key='_EventEndDate')
                                    LEFT JOIN wp_postmeta AS pm3 ON (wp_posts.ID = pm3.post_id AND pm3.meta_key='_EnableRegistration')
                                    WHERE wp_posts.post_type = 'tribe_events'
																		AND pm3.meta_value = 'true'
                                    AND wp_posts.post_title LIKE '%$search%'
                                    AND wp_posts.post_status = 'publish'
                                    AND ((pm1.meta_key = '_EventStartDate') OR (pm2.meta_key = '_EventEndDate'))" );//and column_name_four='value'"
    }else{
      $wk_post=$wpdb->get_results("SELECT  wp_posts.ID, wp_posts.post_title, pm1.meta_value as post_start_date, pm2.meta_value as post_end_date, pm3.meta_value as enable_registration
                                    FROM wp_posts
                                    LEFT JOIN wp_postmeta AS pm1 ON (wp_posts.ID = pm1.post_id AND pm1.meta_key='_EventStartDate')
                                    LEFT JOIN wp_postmeta AS pm2 ON (wp_posts.ID = pm2.post_id AND pm2.meta_key='_EventEndDate')
																		LEFT JOIN wp_postmeta AS pm3 ON (wp_posts.ID = pm3.post_id AND pm3.meta_key='_EnableRegistration')
                                    WHERE wp_posts.post_type = 'tribe_events'
																		AND pm3.meta_value = 'true'
                                    AND wp_posts.post_status = 'publish'
                                    AND ((pm1.meta_key = '_EventStartDate') OR (pm2.meta_key = '_EventEndDate'))");
    }
    $post_title = array();
    $post_type = array();
    $i=0;
    foreach ($wk_post as $wk_posts) {

        $post_ID[]= $wk_posts->ID;
        $post_title[]=$wk_posts->post_title;
        $post_content[]=$wk_posts->post_content;
        $post_start_date[]=$wk_posts->post_start_date;
        $post_end_date[]=$wk_posts->post_end_date;

        $data[] = array(
                'ID' => $post_ID[$i],
                'title'  => $post_title[$i],
                'content' =>   $post_content[$i],
                'start_date' => $post_start_date[$i],
                'end_date' => $post_end_date[$i]
                );
        $i++;
    }
    return $data;
  }

  function __construct(){
    global $status, $page;
        parent::__construct( array(
            'singular'  => __( 'Seminar', 'mylisttable' ),     //singular name of the listed records
            'plural'    => __( 'Seminars', 'mylisttable' ),   //plural name of the listed records
            'ajax'      => false        //does this table support ajax?
    ) );
    add_action( 'admin_head', array( &$this, 'admin_header' ) );
  }

  function admin_header() {
    $page = ( isset($_GET['page'] ) ) ? esc_attr( $_GET['page'] ) : false;
    if( 'my_list_test' != $page )
    return;
    echo '<style type="text/css">';
    echo '.wp-list-table .column-id { width: 5%; }';
    echo '.wp-list-table .column-booktitle { width: 40%; }';
    echo '.wp-list-table .column-author { width: 35%; }';
    echo '.wp-list-table .column-isbn { width: 20%;}';
    echo '</style>';
  }
  function no_items() {
    _e( 'No Seminar found.' );
  }
  function column_default( $item, $column_name ) {
    switch( $column_name ) {
        case 'title':
        case 'content':
        case 'start_date':
        case 'end_date':
            return $item[ $column_name ];
        default:
            return print_r( $item, true ) ; //Show the whole array for troubleshooting purposes
    }
  }
  function get_sortable_columns() {
    $sortable_columns = array(
      'title'  => array('title',false),
      'start_date' => array('start_date',false),
      'end_date'   => array('end_date',false)
    );
    return $sortable_columns;
  }
  function get_columns(){
          $columns = array(
              'cb'        => '<input type="checkbox" />',
              'title' => __( 'Title', 'mylisttable' ),
              'start_date'    => __( 'Start Date', 'mylisttable' ),
              'end_date'      => __( 'End Date', 'mylisttable' )
          );
           return $columns;
  }
  function usort_reorder( $a, $b ) {
    // If no sort, default to title
    $orderby = ( ! empty( $_GET['orderby'] ) ) ? $_GET['orderby'] : 'title';
    // If no order, default to asc
    $order = ( ! empty($_GET['order'] ) ) ? $_GET['order'] : 'asc';
    // Determine sort order
    $result = strcmp( $a[$orderby], $b[$orderby] );
    // Send final sort direction to usort
    return ( $order === 'asc' ) ? $result : -$result;
  }
  function column_title($item){
    $actions = array(
              'view'      => sprintf('<a href="?post_type=tribe_events&page=%s&action=%s&seminar=%s">View</a>',$_REQUEST['page'],'view',$item['ID']),

          );
    return sprintf('%1$s %2$s', $item['title'], $this->row_actions($actions) );
  }
  // function get_bulk_actions() {
  //   $actions = array(
  //     'delete'    => 'Delete'
  //   );
  //   return $actions;
  // }
  function column_cb($item) {
          return sprintf(
              '<input type="checkbox" name="book[]" value="%s" />', $item['ID']
          );
  }
  function prepare_items() {
    $this->data = $this->get_data();
    $columns  = $this->get_columns();
    $hidden   = array();
    $sortable = $this->get_sortable_columns();
    $this->_column_headers = array( $columns, $hidden, $sortable );
    usort( $this->data, array( &$this, 'usort_reorder' ) );
    //print_r($this->get_data());
    $per_page = 20;
    $current_page = $this->get_pagenum();
    $total_items = count( $this->data );
    $this->set_pagination_args( array(
      'total_items' => $total_items,                  //WE have to calculate the total number of items
      'per_page'    => $per_page                     //WE have to determine how many items to show on a page
    ) );
    $this->items = array_slice( $this->data,( ( $current_page-1 )* $per_page ), $per_page );
  }
} //class
