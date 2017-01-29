<?php
global $wpdb;
$seminar_id = $_REQUEST['seminar'];
$post_table = $wpdb->prefix. 'posts';
if(isset($seminar_id) && !empty($seminar_id)){
  $posts = $wpdb->get_results("SELECT post_title FROM $post_table WHERE ID=$seminar_id");
}
  $participantsListTable->prepare_items();

  ?>
  <div class="wrap">
    <h2><?php echo ($posts[0]->post_title); ?>
      <a href="/wp-admin/edit.php?post_type=tribe_events&page=dan-seminar-subscriber" class="page-title-action">
      <span class="dashicons dashicons-undo seminar-back-button-icon" style="margin-top: .5em;"></span>
       Back</a>
    </h2>
    <div>
      <p>
        <h3>Total no. of participants for this seminar : <?php echo ($participantsListTable->all_participants); ?><h3>
      </p>

      <p>
        <h4>Total no. of in-person participations for this seminar : <?php echo ($participantsListTable->in_person_participants); ?><h4>
      </p>
      <p>
        <h4>Total no. of video conference participations for this seminar : <?php echo ($participantsListTable->video_conference_participants); ?><h4>
      </p>
    </div>
  <form method="post">
    <input type="hidden" name="page" value="dan-seminar-subscriber">
    <?php
  $participantsListTable->search_box( 'search', 'seminar_search_id' );
  $participantsListTable->display();
  echo '</form>';

   ?>
</div>
