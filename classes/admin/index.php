<?php
function _dan_seminar_subscriber_admin() {
  //adding the submenu to the events Calendar main menu
  $hook= add_submenu_page(
        'edit.php?post_type=tribe_events', // Third party plugin Slug
        'Seminars',
        'Seminars',
        'manage_options',
        'dan-seminar-subscriber',
        'dan_seminar_subscriber_admin_page'
    );

    // creating options like per page data(pagination)
    // add_action( "load-$hook", 'add_options' );
    add_action( "load-".$hook, 'dan_seminar_table_init' );
}


function dan_seminar_subscriber_admin_page(){

 $page = $_REQUEST['page'];
 $action = $_REQUEST['action'];
 $seminar = $_REQUEST['seminar'];
  if(isset($action) && isset($seminar) && $action='view'){
    global $participantsListTable;
    include_once(dirname(__FILE__).'/views/admin-participants-page.php');

  }else{
    global $seminarListTable;
    echo '</pre><div class="wrap"><h2>Seminars</h2>';
    $seminarListTable->prepare_items();
    ?>
    <form method="post">
      <input type="hidden" name="page" value="dan-seminar-subscriber">
      <?php
      $seminarListTable->search_box( 'search', 'seminar_search_id' );
      $seminarListTable->display();
    echo '</form></div>';
  }


}





function dan_seminar_table_init() {
  global $seminarListTable;
  global $participantsListTable;

  $seminarListTable = new Seminar_List_Table();
  $participantsListTable = new Participants_List_Table();
}
