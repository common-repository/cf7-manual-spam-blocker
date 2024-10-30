<?php

class CF7Msb_SUPPORT {

	public function __construct() {

		if ( ! defined( 'CF7Msb_EMAIL_PREFIX' ) ) {
			define( 'CF7Msb_EMAIL_PREFIX', CF7Msb_PLUGIN_PREFIX );
		}
		
		if ( ! defined( 'CF7Msb_SUPPORT_EMAIL' ) ) {
			define( 'CF7Msb_SUPPORT_EMAIL', 'poke@slickpopup.com' );
		}

		if ( ! defined( 'CF7Msb_SUPPORT_USER' ) ) {
			define( 'CF7Msb_SUPPORT_USER', 'slickpopupteam' ); 
		}
		
		add_action( 'wp_ajax_action_cf7msb_contact_support', array($this, 'action_cf7msb_contact_support') );
		//add_action( 'wp_ajax_action_cf7msb_support_access', array($this, 'action_cf7msb_support_access') );
	}
	/**
	 * Help and Support Page
	 * Since Version 1.0
	 * @param none

	 *return none
	 * Creates the post list table
	 */
	function support_page() { ?>
		<style type="text/css">
			.wp-admin select {
				height: 38px;
			}
			.result-area {
				line-height: 1.5em;
				padding: 10px 15px;
			}
			.display-5 {
				font-size: 2.5rem;
				font-weight: 400;
				line-height: 1.2;
			}
			.display-6 {
				font-size: 1.75rem; 
				font-weight: 400; 
				line-height: 1.2;
			}
		</style>
		
		<?php  
			$current_user =  wp_get_current_user();
			$username = isset($current_user->user_display_name) ? $current_user->user_display_name : ((isset($current_user->user_firstname) and !empty($current_user->user_firstname)) ? $current_user->user_firstname : $current_user->user_login);
			$useremail = $current_user->user_email;
		?>
		
		<div class="wrap">
			<div class="card col-md-12 border-secondary">
				<span class="card-title display-5 text-center text-secondary"><?php echo esc_html( __( "Support", 'cf7-manual-spam-blocker' ) ); ?></span>
				<div class="card-body">
					<div class="row">
						<div class="offset-md-3 col-md-6 border border-secondary">
							<span class="text-secondary text-center d-block display-6 pb-2"><?php echo esc_html( __( "Contact Support", 'cf7-manual-spam-blocker' ) ); ?></span>
							<form method="post" class="cf7msb-contact-support" action="">
								<div class="input-group mb-3">
								    <div class="input-group-prepend">
								      <span class="input-group-text"><?php echo esc_html( __( "Name", 'cf7-manual-spam-blocker' ) ); ?></span>
								    </div>
								    <input type="text" class="form-control" name="name" placeholder="<?php echo esc_html( __( "Enter your Name", 'cf7-manual-spam-blocker' ) ); ?>" value="<?php echo $username; ?>" >
								    <input type="hidden" class="form-control" name="plugin_version" value="<?php echo CF7Msb_VERSION ?>" >
								</div>
								<div class="input-group mb-3">
								    <div class="input-group-prepend">
								      <span class="input-group-text"><?php echo esc_html( __( "Email", 'cf7-manual-spam-blocker' ) ); ?></span>
								    </div>
								    <input type="text" class="form-control" name="email" placeholder="<?php echo esc_html( __( "Enter your Email", 'cf7-manual-spam-blocker' ) ); ?>" value="<?php echo $useremail; ?>" >
								</div>
								<div class="input-group mb-3">
								    <div class="input-group-prepend">
								      <span class="input-group-text"><?php echo esc_html( __( "Issue Subject", 'cf7-manual-spam-blocker' ) ); ?></span>
								    </div>
								    <input type="text" class="form-control" name="subject" placeholder="<?php echo esc_html( __( "Enter your Issue Subject", 'cf7-manual-spam-blocker' ) ); ?>">
								</div>
								<div class="input-group mb-3">
								    <div class="input-group-prepend">
								      <span class="input-group-text"><?php echo esc_html( __( "Page URL", 'cf7-manual-spam-blocker' ) ); ?></span>
								    </div>
								    	<?php 
											$args = array(
												'show_option_none' => 'All Pages',
												'name' => 'page_id',
												'class' => 'form-control',
											);
											wp_dropdown_pages($args); 
										?>
								</div>
								<div class="form-group mb-3">
								  <label for="message" class="font-weight-bold"><?php echo esc_html( __( "Issue Details:", 'cf7-manual-spam-blocker' ) ); ?></label>
								  <textarea class="form-control" name="message" rows="6" placeholder="<?php echo esc_html( __( "Please describe your issue in detail", 'cf7-manual-spam-blocker' ) ); ?>"></textarea>
								</div>
								<div class="input-group" style="margin:20px 0 10px;">
									<input type="submit" name="Submit" class="button button-primary cf7msb-submit-btn">	
									<span class="cf7msb-loader" style="margin-left:10px;visibility:hidden;"><i class="fa fa-refresh fa-spin" style="font-size:14px;color:#f56e28;position:relative;left:-8px;"></i></span>
								</div>
								<div class="input-group">
									<div class="result-area"></div>
								</div>
							</form>
						</div>
						<div class="col-md-6 hidden">
							<span class="text-secondary text-center pb-2 d-block display-6"><?php echo esc_html( __( "One Step to Create an Admin User for Support", 'cf7-manual-spam-blocker' ) ); ?></span>
							<div class="text-body font-weight-normal">
								<p><?php echo esc_html( __( "In the past, many of our users were having problem to grant us access to the website so we can solve their query, that is the reason we have built this ", 'cf7-manual-spam-blocker' ) ); ?><strong><?php echo esc_html( __( "'Easy Grant Access'", 'cf7-manual-spam-blocker' ) ); ?></strong><?php echo esc_html( __( " feature.", 'cf7-manual-spam-blocker' ) ); ?></p>
								<p>
									<strong><?php echo esc_html( __( "It will create a new admin user for our email ", 'cf7-manual-spam-blocker' ) ); ?><em><?php echo CF7Msb_SUPPORT_EMAIL; ?></em> <?php echo esc_html( __( " with one click, making it easier for you to grant and revoke access.", 'cf7-manual-spam-blocker' ) ); ?></strong>
								<br><br>
								<?php 
									if(!username_exists(CF7Msb_SUPPORT_USER) && !email_exists(CF7Msb_SUPPORT_EMAIL))
										echo '<button class="button button-primary cf7msb-ajax-btn" data-ajax-action="action_cf7msb_support_access" data-todo="createuser">Grant Temporary Access <i class="fa fa-user"></i></button>';
									else
										echo '<button class="button button-primary cf7msb-ajax-btn" data-ajax-action="action_cf7msb_support_access" data-todo="deleteuser">Revoke Access <i class="fa fa-user"></i></button>';
								
								echo '<span class="cf7msb-loader" style="margin-left:10px;visibility:hidden;"><i class="fa fa-refresh fa-spin" style="font-size:14px;color:#f56e28;position:relative;left:-8px;"></i></span>';
								 								
									if(get_option('cf7msb_grant_access_time')) {
										$cf7msb_grant_access_time = get_option('cf7msb_grant_access_time');
										$cf7msb_grant_access_by = get_option('cf7msb_grant_access_by');
										$date_object = DateTime::createFromFormat('Y-m-d H:i:s', $cf7msb_grant_access_time); 
										$cf7msb_grant_access_by = get_userdata($cf7msb_grant_access_by); 
										
										echo '<div class="cf7msb-last-granted">';
											echo '<strong>Last Granted</strong>: <span class="cf7msb-last-granted-time">'. $date_object->format('j M, Y') . ' (' . $date_object->format('H:i A') . ') by <b>Username</b> - '.$cf7msb_grant_access_by->user_login.'</span>';
										echo '</div>';
									}
								?>
								</p>	
							</div>
							<div class=""><div class="result-area"></div></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php }

	
	function action_cf7msb_contact_support() {
		//print_r( $_POST['fields'] ); 
		$ajaxy = array(); 
		$errors = array(); 
		
		if( !isset($_POST) OR !isset($_POST['fields']) OR empty($_POST['fields']) ) {
			$ajaxy['reason'] = 'Nothing sent to server, please retry.'; 
		}
		
		parse_str($_POST['fields']); 	
		
		// If Nothing is posted through AJAX
		if( !isset($name) OR empty($name) ) {
			$errors[] = 'Please enter your name'; 
		}
		if( !isset($email) OR empty($email) ) {
			$errors[] = 'Please enter your email'; 
		}
		if( !isset($subject) OR empty($subject) ) {
			$errors[] = 'Please enter a subject'; 
		}
		if( !isset($message) OR empty($message) ) {
			$errors[] = 'Please describe the issue your facing'; 
		}
		
		$pages = 'All Pages'; 
		if(!empty($page_id) AND is_numeric($page_id)) {
			$pages = '<a href="'.get_the_permalink($page_id).'" target="_blank">'.get_the_title($page_id).'</a>'; 
		}
		
		if(sizeof($errors)) {
			//$ajaxy['reason'] = '<ul>';
				//foreach($errors as $error) { $ajaxy['reason'] .= '<li>'.$error.'</li>'; }
			//$ajaxy['reason'] .= '</ul>';
			
			$ajaxy['reason'] = implode('<br>', $errors); 		
			wp_send_json_error($ajaxy); 
			wp_die(); 
		}
		
		// Always set content-type when sending HTML email
		$headers = "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
		$headers .= 'From: <'.$email.'>' . "\r\n";
		//$headers .= 'Cc: '.$email . "\r\n";
		
		$mail_subject = CF7Msb_PLUGIN_PREFIX. ': Support Required: ' . $name . ' - ' . $subject . ' (' . site_url(). ')';
		$mail_body = ''; 
		$mail_body .= '<b>Dear Team,<b><br><br>'; 
		$mail_body .= '<table border cellpadding="10">';
			$mail_body .= '<tr>';
				$mail_body .= '<th>A new support request has been received from: </th><td>'.site_url().'</td>';
			$mail_body .= '</tr>';
			$mail_body .= '<tr>';	
				$mail_body .= '<th>Plugin Version: </th><td>'.$plugin_version.'</td>';
			$mail_body .= '</tr>';
			$mail_body .= '<tr>';	
				$mail_body .= '<th>Email: </th><td>'.$email.'</td>';
			$mail_body .= '</tr>';
			$mail_body .= '<tr>';	
				$mail_body .= '<th>Message: </th><td>'.$message.'</td>';
			$mail_body .= '</tr>';
			$mail_body .= '<tr>';	
				$mail_body .= '<th>Page: </th><td>'.$pages.'</td>';
			$mail_body .= '</tr>';
		$mail_body .= '</table>';
		
		$mail = wp_mail(CF7Msb_SUPPORT_EMAIL, $mail_subject, $mail_body, $headers); 
		
		if($mail) {
			$ajaxy['reason'] = 'Your request has been sent to support team, please wait for a response. <br><small style="line-height:1.5em;font-style:italic;display:block;">If you think that this issue will require admin access then please grant temporary access to the <strong>Support Team</strong> by clicking the button on the right.</small>'; 
			wp_send_json_success($ajaxy); 
			wp_die(); 
		}
		
		$ajaxy['reason'] = 'Could not contact support, please retry or send a direct email to ' . CF7Msb_SUPPORT_EMAIL;
		wp_send_json_error($ajaxy); 
		wp_die(); 
	}
		
}
new CF7Msb_SUPPORT();