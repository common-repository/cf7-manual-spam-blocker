<?php 

/**
 * Add Plugin's Admin Menu
 * Since Version 2.0  
*/	
add_action('admin_menu', 'cf7msb_addmenu_page_in_admin', 10); 
function cf7msb_addmenu_page_in_admin() {

	global $cf7msb_hook; 	
	$cf7msb_hook = array();
	$cf7msb_hook[] = add_menu_page( 'Settings', 'Spam Blocker', 'manage_options', 'cf7msb-settings', 'cf7msb_settings_page', 'dashicons-dismiss');
	$cf7msb_hook[] = add_submenu_page( 'cf7msb-settings', 'WordPress Spam Blocker', 'Global Settings', 'manage_options', 'cf7msb-settings', 'cf7msb_settings_page' );
	// $cf7msb_hook[] = add_submenu_page( 'cf7msb-settings', 'Support', 'Support', 'manage_options', 'cf7msb-help', array(new CF7Msb_SUPPORT(), 'support_page') );
	
	// Add sub menu page under Contact form 7 menu
	add_submenu_page('wpcf7', 'WordPress Spam Blocker', 'WordPress Spam Blocker', 'manage_options', 'cf7msb-settings', 'cf7msb_settings_page');
}

/**
 * Settings Page
 * Since Version 1.0
 * @param none
*/
function cf7msb_settings_page() {
	$cf7msb_settings = get_option('cf7msb_settings', array('show_errors'=>1, 'add_quick_links'=>1, 'block_list'=> array(), 'email_list'=> array())); ?>
	<div class="wrap">
		<div class="cf7msb-layout__header">
            <div class="cf7msb-layout__header-wrapper">
                <h6><?php _e(CF7Msb_PLUGIN_TITLE); ?></h6>
            </div>
        </div>
        <div class="cf7msb-layout__body">
			<!-- Intro Card -->		
			<div class="card" style="max-width: 100% !important;margin-top:0px">
				<div class="card-body">
					<p><?php _e(CF7Msb_PLUGIN_TITLE.' is an easy-to-use plugin to block all those spammers who manually fill out forms on your website. It provides with an extra tab on each <strong>Contact Form 7 (CF7)</strong> edit screen where you can set block conditions for each input field in the form. The global word list and email list also applies to <strong>WP Forms</strong> and <strong>Formidable Forms</strong>') ?></p>
				</div>
			</div> <!-- Intro Card -->		

			<div id="poststuff">

				<div id="post-body" class="metabox-holder columns-2">

					<!-- main content -->
					<div id="post-body-content">

						<div class="meta-box-sortables ui-sortable">

							<div class="postbox">

								<div class="inside">

									<form method="post" class="cf7msb-role-pages-form" action="">
										<table class="form-table">
											<tr>
												<td colspan="2" class="font-weight-bold">
													<h3 style="margin-bottom:0;"><?php esc_attr_e( 'WordPress Spam Blocker Options', 'cf7-manual-spam-blocker' ); ?></h3>
												</td>
											</tr>
											<tr>
												<th scope="row" style="width: 324px"><?php _e( 'Block Non-English Characters' ) ?></th>
												<td>
													<p>
														<label><input class="cf7msb-block_non_english form-control" type="radio" name="block_non_english" value="1" <?php checked( "1", $cf7msb_settings["block_non_english"] ) ?>/>Yes</label>
														<label><input class="cf7msb-block_non_english form-control" type="radio" name="block_non_english" value="0" <?php checked( "0", $cf7msb_settings["block_non_english"] ) ?>/>No</label>
													</p>
												</td>
											</tr>
											<tr>
												<th scope="row" style="width: 324px"><?php _e( 'Show Error Messages for Field with Spam', 'cf7-manual-spam-blocker' ) ?></th>
												<td>
													<p>
														<label><input class="cf7msb-show_errors form-control" type="radio" name="show_errors" value="1" <?php checked( "1", $cf7msb_settings["show_errors"] ) ?>/>Yes</label>
														<label><input class="cf7msb-show_errors form-control" type="radio" name="show_errors" value="0" <?php checked( "0", $cf7msb_settings["show_errors"] ) ?>/>No</label>
													</p>
												</td>
											</tr>
											<tr>
												<th scope="row" style="width: 324px">
													<?php _e( 'Add Quick Links in Email' ) ?>
												</th>
												<td>
													<p>
														<label><input class="cf7msb-add_quick_links form-control" type="radio" name="add_quick_links" value="1" <?php checked( "1", $cf7msb_settings["add_quick_links"] ) ?>/>Yes</label>
														<label><input class="cf7msb-add_quick_links form-control" type="radio" name="add_quick_links" value="0" <?php checked( "0", $cf7msb_settings["add_quick_links"] ) ?>/>No</label>
													</p>
													<?php _e( '<p class="mt-1" style="font-weight:400">(To use Quick links in email, you must send the HTML Content in your email.)</p>') ?>
												</td>
											</tr>
										</table>
										<table class="form-table">
											<tr>
												<th class="py-0" scope="row" ><?php _e( 'Message for Spammers') ?> (Editable in <a href="<?php echo CF7Msb_CC_URL; ?>" target="_blank">Pro</a>)</th>
													<td><input class="cf7msb-global_error_message regular-text" type="text" placeholder="Enter message for spammers" name="global_error_message" value="<?php echo $cf7msb_settings["global_error_message"]; ?>" readonly>
												</td>
											</tr>
											<tr>
												<th class="py-0" scope="row" ><?php _e( 'Message for Spam Field') ?> (Editable in <a href="<?php echo CF7Msb_CC_URL; ?>" target="_blank">Pro</a>)</th>
												<td>
													<input class="cf7msb-global_error_message_for_field form-control regular-text" type="text" placeholder="Enter message for spam field" name="global_error_message_for_field" value="<?php echo $cf7msb_settings["global_error_message_for_field"]; ?>" readonly>
												</td>
											</tr>
										</table>

										<hr>

										<table class="form-table">
											<tr>
												<td colspan="2" class="font-weight-bold">
													<h3 style="margin-bottom:0;">Global Wordlists</h3>
													<p>(Maximum 5 Words Allowed, Unlimited in <a href="<?php echo CF7Msb_CC_URL; ?>" target="_blank">Pro</a>)</p>
												</td>
											</tr>
											<tr>
												<th scope="row" class="pl-2" style="width: 103px !important"><?php _e( 'Word List' ) ?></th>
												<td>
													<p>Enter comma separated terms or one entry per line</p>
													<p><textarea class="cf7msb-block_list form-control" name="block_list" rows="5" cols="50"><?php echo implode(', ', $cf7msb_settings["block_list"]) ?></textarea></p>
													<p style="display:none;">
														<span data-type="marketing" disabled class="ml-2 mt-1 d-none button button-secondary cf7msb-import-words">Import Marketing Words</span>
														<span data-type="abusive" disabled class="ml-2 mt-1 d-none button button-secondary cf7msb-import-words">Import Abusive Words</span>
													</p>
												</td>
											</tr>
											<tr>
												<th scope="row"  class="pl-2" style="width: 103px !important"><?php _e( 'Email List' ) ?></th>
												<td>
													<p>Enter comma separated terms or one entry per line</p>
													<p><textarea class="form-control cf7msb-email_list" name="email_list" rows="5" cols="50"><?php echo implode(', ', $cf7msb_settings["email_list"]) ?></textarea></p>
												</td>
											</tr>
											<tr class="cf7msb_update_settings_button cf7msb_submit_button mt-2">
												<td colspan=2>
													<button class="button-primary cf7msb-update-settings" type="submit"><i class="fa fa-spinner mr-3 d-none" style="margin-right:5px"></i>Save Options</button>
													<div class="cf7msb-result alert mt-3 mb-0 d-none"></div>
												</td>
											</tr>
										</table>
									</form>
								
								</div>
								<!-- .inside -->

							</div>
							<!-- .postbox -->

						</div>
						<!-- .meta-box-sortables .ui-sortable -->

					</div>
					<!-- post-body-content -->

					<!-- sidebar -->
					<?php if(is_plugin_active("contact-form-7/wp-contact-form-7.php")) { ?>
						<div id="postbox-container-1" class="postbox-container">

							<div class="meta-box-sortables">

								<div class="postbox">

									<h2 class="hndle"><span><?php esc_attr_e('Contact Form 7', 'cf7-manual-spam-blocker'); ?></span></h2>
									<div class="inside">
										<?php _e( __( CF7Msb_PLUGIN_TITLE.' is a robust plugin to help you block spam on <b><i>Contact Form 7</i></b> forms.' ), 'cf7-manual-spam-blocker' ); ?>
										<?php echo cf7msb_get_cf7_forms();  ?>

										<ul class="cf7msb-list">
											<li><?php _e( __( 'Just follow these simple steps:' ), 'cf7-manual-spam-blocker' ); ?></li>
											<li><?php _e( __( 'Click "configure" to go to edit section of the form and click on <strong>Block Spam</strong> tab.') ) ?></li>
											<li><?php _e( __( 'In the Email Field you can choose "Value Equals to".') ) ?></li>
											<li><?php _e( __( 'Enter the message to be shown to the spammer. You may want to abuse the bloody ones!') ) ?></li>
											<li><?php _e( __( 'Enter the input values to be blocked, one entry per line and just <strong>Save</strong> it.') ) ?></li>
										</ul>

										<p class="pt-1">
											<?php $link = 'https://www.slickpopup.com/how-to-block-spam-in-contact-form-7-forms?utm_source=cf7msb&utm_medium=supportpage&utm_campaign=OmAkSols&utm_term=demolink'; ?>
											<span class="d-block" style="font-size: 1.15rem"><?php _e( __( 'Check out the <a href="'.$link.'" target="_blank">demo</a> form and how it blocks the spammers.') ) ?></span>
										</p>
									</div>
									<!-- .inside -->

								</div>
								<!-- .postbox -->

							</div>
							<!-- .meta-box-sortables -->

						</div>
						<!-- #postbox-container-1 .postbox-container -->
				<?php } ?>

				</div>
				<!-- #post-body .metabox-holder .columns-2 -->

				<br class="clear">
			</div>
			<!-- #poststuff -->

			<div class="card p-0" style="max-width: 100% !important;">
				<h2 class="title">Get Started</h2>
				<div class="card-body">
					<p class="d-block" style="font-size: 15px"><strong>Block Non-English Character</strong>: Turning this option will check every form field for non-English characters. If these characters are found, then the form submission will be blocked.</p>
					<p class="d-block" style="font-size: 15px"><strong>Show Error Messages for Field with Spam</strong>: If you want to show the users this default message "SPAM is Blocked" or you want to add your own custom message, Then Check "Yes".</p>
					<p class="d-block" style="font-size: 15px"><strong>Add Quick Links in Email</strong>: This option enables you to add a specific email sender from the email itself into the block list. Click on "Yes" if you wish to enable this message.</p>
					<p class="d-block" style="font-size: 15px"><strong>Message for Spammers</strong>: This option enables you to display error message to the spammer.</p>
					<p class="d-block" style="font-size: 15px"><strong>Message for Spam Field</strong>: This option enables you to display error message for individul field to the spammer.</p>
				</div>
			</div>

		</div> <!-- .cf7msb body -->
    </div> <!-- .wrap -->
	<?php
}

/**
 * Ajax Function
 * Called in custom-admin.js
 * Update the global settings
 * Date: 22nd February, 2019
*/
add_action('wp_ajax_cf7msb_update_settings', 'cf7msb_update_settings');
function cf7msb_update_settings() {
	
	if(! current_user_can('manage_options')) {
		$result = array('reason' => 'You not have sufficient permissions to perform this action.', 'reload' => false);
		wp_send_json_error($result);
		wp_die();
	}
	
	// $_POST['fields'] = sanitize_text_field($_POST['fields']); 
	
	//parse the fields
	parse_str($_POST['fields'], $fields);
	
	// TODO sanitize posted fields
	
	if(empty($fields['global_error_message'])) {
		$result = array('reason' => 'Please enter a message for SPAMMERS.', 'reload' => false);
		wp_send_json_error($result);
		wp_die();
	}
	
	// Hint: Create array from CSV and line-separated input values
	$limited_fields = ['block_list', 'email_list']; 
	foreach($limited_fields as $limited_field) {
		if(!isset($fields[$limited_field]) OR empty($fields[$limited_field])) {
			$fields[$limited_field] = array();
		}
		else {	
			
			// Hint: Replace New Line Character with ","
			$fields[$limited_field] = str_replace(["\r\n"], ",", $fields[$limited_field]);
				
			// Explode with Comma
			if(strpos($fields[$limited_field], ",") !== -1) {
				$fields[$limited_field] = explode(",", $fields[$limited_field]);
			}
			
			// Hint: Trim and convert to lower string
			$fields[$limited_field] = array_map('trim', $fields[$limited_field]);
			$fields[$limited_field] = array_map('strtolower', $fields[$limited_field]);
			
			// Hint: Array_filter to remove empty values 
			// Hint: array_values to get values from indexed array returned after filter
			$fields[$limited_field] = array_values(array_filter($fields[$limited_field])); 
		}
	}
	
	// Make sure the settings have correct values
	$fields['block_non_english'] = (!isset($fields['block_non_english']) OR empty($fields['block_non_english'])) ? 0 : $fields['block_non_english'];
	$fields['show_errors'] = (!isset($fields['show_errors']) OR empty($fields['show_errors'])) ? 0 : $fields['show_errors'];
	$fields['add_quick_links'] = (!isset($fields['add_quick_links']) OR empty($fields['add_quick_links'])) ? 0 : $fields['add_quick_links'];
	
	// Free Vesrion: Fixed "SPAM is blocked" message
	$fields['global_error_message'] = CF7Msb_Helpers::settings_defaults('global_error_message'); 
	$fields['global_error_message_for_field'] = CF7Msb_Helpers::settings_defaults('global_error_message_for_field'); 
	
	// Hint: Save only first five values
	$limited_fields = ['block_list', 'email_list']; 
	
	foreach($limited_fields as $limited_field) {
		$fields[$limited_field] = array_unique($fields[$limited_field]);
		if(sizeof($fields[$limited_field])>5) {
			$fields[$limited_field] = array_splice($fields[$limited_field], 0, 5);
		}
	}
	
	// Update the 'cf7msb_settings' option
	update_option('cf7msb_settings', $fields); 

	$result = array('reason' => 'Settings Saved.', 'reload' => true);
	wp_send_json_success($result);
	wp_die();
}

/**
 * Add quick links in the email body
 * Date: 23rd February, 2019
*/
add_filter( 'wpcf7_before_send_mail', 'cf7msb_add_quick_links' );
function cf7msb_add_quick_links($WPCF7_ContactForm) {

	$form = WPCF7_ContactForm::get_current();
    $mail= $WPCF7_ContactForm->prop('mail');
	
	$cf7msb_settings = get_option('cf7msb_settings', array('show_errors'=>1, 'add_quick_links'=>1, 'block_list'=> array(), 'email_list'=> array())); 
	$output = ''; 

	if($cf7msb_settings['add_quick_links']) {
		$admin_url = admin_url('admin.php'); 
		$form_id = (isset($_POST['_wpcf7']) AND !empty($_POST['_wpcf7'])) ? sanitize_text_field($_POST['_wpcf7']) : '';

		$args = array(
			'sep' => ' | ',
			'form_id' => $form_id,
		);

		extract($args); 

		$output .= '<p>';
			$output .= '<strong>IS IT A SPAM? </strong><br>';
			$output .= '<a target="_blank" href="'.add_query_arg(array('page'=>'wpcf7','post'=>$form_id,'action'=>'edit','alert'=>1), $admin_url).'">Edit Spam Blocking</a>';
			$output .= $sep; 
			$output .= '<a target="_blank" href="'.add_query_arg(array('page'=>'cf7msb-settings','alert'=>1), $admin_url).'">Edit Global Block List</a>';	
		$output .= '</p>'; 

		$mail['body'] = $mail['body'] .'</br>' . $output;

		$WPCF7_ContactForm->set_properties(array(
	   		"mail" => $mail
	  	));	
	}
	return $WPCF7_ContactForm;
}