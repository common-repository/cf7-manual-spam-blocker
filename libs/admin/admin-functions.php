<?php


function cf7msb_get_cf7_forms($type="list") {
    $args = array('post_type' => 'wpcf7_contact_form', 'posts_per_page' => -1);
    $cf7Forms = get_posts( $args );

    if(! sizeof($cf7Forms)) {
        return "You do not have any forms created yet.";
    }

    $post_ids = wp_list_pluck( $cf7Forms , 'ID' );
    $form_titles = wp_list_pluck( $cf7Forms , 'post_title' );

    $output = '<p>Forms from Contact Form 7</p>';
    // print_r($cf7Forms);
    $output .= '<ol>';
        for($i=0; $i<sizeof($form_titles); $i++) {
            $output .= '<li style="color:red;">' . $form_titles[$i]  . '- <a href="http://localhost/clients/omak/pluginstesting/wp-admin/admin.php?page=wpcf7&post='.$post_ids[$i].'&action=edit">Configure</a></li>';
        }
    $output .= '</ol>';

    return $output;
}
/*
 * cf7msb_notice_dismissable
 * Ajax action to do tasks on notice dismissable
 * Require class: cf7msb-dismissable
*/
add_action( 'wp_ajax_cf7msb_notice_dismissable', 'cf7msb_notice_dismissable' );
function cf7msb_notice_dismissable() {
	
	// Sanitize string for added security
	$data_btn = isset($_POST['dataBtn']) ? sanitize_text_field($_POST['dataBtn']) : '';
	
	if(empty($data_btn)) return; 
	
	$today = DateTime::createFromFormat('U', current_time('U')); 
	
	switch($data_btn) {
		case 'ask-later':
			$ask_later = get_option('cf7msb_review_notice') ? get_option('cf7msb_review_notice') : 0;
			$updated = update_option('cf7msb_review_notice', ++$ask_later); 
			break; 
		case 'ask-never':
			$updated = update_option('cf7msb_review_notice', -1); 
			break; 
	}
	
	$ajaxy = ($updated) ? 'Updated' : 'Not updated'; 
	wp_send_json_success($ajaxy); 
	wp_die(); 
}

function cf7msb_get_notice_css() {
	return  "
		<style>
		 	.cf7msb-notice-left {
		 		display:inline-block;
		 		vertical-align:middle;
		 	}
		 	.cf7msb-notice-right {
		 		max-width: calc(100% - 200px);
				display: inline-block; 
				vertical-align: middle; 
				margin-left: 10px;
		 	}
		 </style> 
		";
}

add_action( 'admin_notices', 'cf7msb_admin_notices' );
function cf7msb_admin_notices() {
	
	// Version: 1.1.2 - Do not show review notice if user is not on a plugin's page
	if(!isset($_GET['page']) || strpos($_GET['page'], 'cf7msb')===false) {
		return; 
	}
	
	// Get cf7msb_install_date from options
	$install_date = get_option('cf7msb_install_date') ? get_option('cf7msb_install_date') : current_time('Y-m-d H:i:s'); 
	
	$review_notice = get_option('cf7msb_review_notice');
	
	// review_notice - numeric counter for multiplying 14 days
	$review_notice =  (isset($review_notice) AND !empty($review_notice)) ? $review_notice : 1; 
	
	$install_date_object = DateTime::createFromFormat('Y-m-d H:i:s', $install_date);
	$today = DateTime::createFromFormat('U', current_time('U')); 
	$diff = $today->diff($install_date_object); 	
	
	if($review_notice!=-1) {
		if($diff->days >= 14*$review_notice) {
			echo cf7msb_get_notice_css();
			echo '<div class="notice notice-success is-dismissible">
					<div class="row">
						<div class="cf7msb-notice-left">
							<img src="'.CF7Msb_PLUGIN_IMG_URL.'/plugin-notice-logo.png" style="padding:5px; height: 80px">
						</div>
						<div class="cf7msb-notice-right">
							<h2 style="margin:0.5em 0;">Hope you are enjoying - <span style="color:#0073aa;">'.CF7Msb_PLUGIN_TITLE.'</span></h2>
							<p>'.__( 'Thanks for using the Contact Form 7 Manual Spam Blocker to block all those spammers who manually fill out forms on your website. We hope that it has been useful for you and would like you to leave review on WordPres.org website, it will help us improve the product features.', 'cf7-manual-spam-blocker' ).'
							<br><br>
							<a class="button-primary" href="https://wordpress.org/plugins/cf7-manual-spam-blocker/reviews">Leave a Review</a>
							&nbsp;<a class="button-link cf7msb-dismissable" data-btn="ask-later" href="#">Ask Later</a> |
							<a class="button-link cf7msb-dismissable" data-btn="ask-never" href="#">Never Show Again</a></p>
						</div>
					</div>
				</div>';		
		}
	}
}

/**
 * Show a notice to anyone who has just updated this plugin
 * This notice shouldn't display to anyone who has just installed the plugin for the first time
*/
function cf7msb_display_update_notice() {
	// Check the transient to see if we've just updated the plugin
	if(get_transient( 'cf7msb_updated' ) ) {
		echo cf7msb_get_notice_css();
		echo '<div class="notice notice-success is-dismissible">
				<div class="row">
					<div class="cf7msb-notice-left">
						<img src="'.CF7Msb_PLUGIN_IMG_URL.'/plugin-notice-logo.png" style="padding:5px; height: 80px">
					</div>
					<div class="cf7msb-notice-right">
						<h2 style="margin:0.5em 0;">Hope you are enjoying - <span style="color:#0073aa;">'.CF7Msb_PLUGIN_TITLE.'</span></h2>
						<p>
						'.sprintf(__( 'The WordPress Plugin with the ability to block all those spammers who manually fill out forms on your website.<br><span style="display: block; margin: 0.5em 0.5em 0 0; clear: both; font-weight: bold;"><a href="%s">Global Settings</a>', 'cf7-manual-spam-blocker') , admin_url('?page=cf7msb-settings')).'
						</p>
					</div>
				</div>
			</div>';	
		
		// Save cf7msb_install_date for already existing users (before: 1.5.3)
		if(!get_option('cf7msb_install_date'))
			update_option('cf7msb_install_date', current_time('Y-m-d H:i:s'));		
		
		delete_transient( 'cf7msb_updated' );
	}
}
add_action( 'admin_notices', 'cf7msb_display_update_notice' );

/**
 * Show a notice to anyone who has just installed the plugin for the first time
 * This notice shouldn't display to anyone who has just updated this plugin
*/
function cf7msb_display_install_notice() {
	// Check the transient to see if we've just activated the plugin
	if( get_transient( 'cf7msb_activated' ) ) {
		echo cf7msb_get_notice_css();
		echo '<div class="notice notice-success is-dismissible">
				<div class="row">
					<div class="cf7msb-notice-left">
						<img src="'.CF7Msb_PLUGIN_IMG_URL.'/plugin-notice-logo.png" style="padding:5px; height: 80px">
					</div>
					<div class="cf7msb-notice-right">
						<h2 style="margin:0.5em 0;">Thanks for installing - <span style="color:#0073aa;">'.CF7Msb_PLUGIN_TITLE.'</span></h2>
						<p>
						'.sprintf(__( 'The WordPress Plugin with the ability to block all those spammers who manually fill out forms on your website.<br><span style="display: block; margin: 0.5em 0.5em 0 0; clear: both; font-weight: bold;"><a href="%s">Global Settings</a>', 'cf7-manual-spam-blocker' ), admin_url('?page=cf7msb-settings')).'
						</p>
					</div>
				</div>
			</div>';
		
		// Delete the transient so we don't keep displaying the activation message
		delete_transient( 'cf7msb_activated' );
	}
}
add_action( 'admin_notices', 'cf7msb_display_install_notice' );

?>