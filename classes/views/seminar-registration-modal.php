<?php

//creating the seminar registration modal
function dan_seminar_register_modal() {
?>
<div class="modal fade dan-seminar-register-modal" id="dan_seminar_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">
          Register to "<?php the_title() ?>"
        </h4>
      </div>
      <div class="modal-body">
				<form name="seminar-register" method="GET" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
          <div class="row">
            <div class="col-md-12">
							<?php
								if(is_user_logged_in()){
									 $current_user = wp_get_current_user();
								}
							?>
              <input name='seminar-register-form' type="hidden"  value="set"/>
              <input name='dsr-modal-seminar-id' type="hidden"  value="<?php the_ID() ?>"/>
							<p>
								<label for="dan-seminar-register-modal-name">Name : </label>
								<input placeholder="Full name" type="text" id="dan-seminar-register-modal-name" name="dsr-modal-name" <?php if(is_user_logged_in()){printf("value=\"%s %s\" readonly=\"true\"", esc_html( $current_user->user_firstname ), esc_html( $current_user->user_lastname ) );} ?> />
							</p>

							<p>
								<label for="dan-seminar-register-modal-email">Email : </label>
								<input placeholder="Email" required type="email" id="dan-seminar-register-modal-email" name="dsr-modal-email" <?php if(is_user_logged_in()){printf("value=\"%s\" readonly=\"true\"", esc_html($current_user->user_email));} ?> />
							</p>

							<p>
								<label for="dan-seminar-register-modal-participation-method">Participation Method : </label>
								<select name="dsr-modal-participation-method" id="dan-seminar-register-modal-participation-method">
									<option value="video-conference">Video Conference</option>
									<option value="in-person">In person</option>

								</select>
							</p>
						</div>
          </div>
					<div class="row">
						<div class="col-md-12">
							<input type="submit" href="" class="btn btn-primary pull-right" value="Register">
						</div>
					</div>
				</form>
        <div class="clearfix">

        </div>

      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<?php
}
