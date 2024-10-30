<?php

class CF7Msb_EMAIL {
	
	function __construct() {
		
	}
	
	public function send_report() {
		
		$get_spam_report_html = $this->get_spam_report_html(); 
		if($get_spam_report_html['send_mail']!=true) {
			return; 
		}
		
		$default_admin_email = get_option('admin_email', ''); 
		$admin_email = CF7Msb_OPTIONS::get_option('admin_email', ''); 
		if(empty($admin_email)) {
			$admin_email = $default_admin_email; 
		}
		
		if(empty($admin_email)) {
			return; 
		}
		
		add_filter( 'wp_mail_content_type', array($this, 'set_test_html_content_type' ) );
		
		$subject = 'Spam Blocker: Daily Block Report';
		// Send the test mail.
		$result = wp_mail(
			$admin_email,
			$subject,
			$this->get_email_message_html($get_spam_report_html['report_html']),
			array(
				'X-Mailer-Type:CF7Msb/Admin/Report',
			)
		);		
		remove_filter( 'wp_mail_content_type', array($this, 'set_test_html_content_type' ) );
		
		if($result) {
			update_option('cf7msb_last_report_email', current_time('Y-m-d')); 
		}
		
		return $result; 
	}

	/**
	 * Get the HTML prepared message for test email.
	 *
	 * @since 1.4.0
	 *
	 * @return string
	 */
	private function get_email_message_html($report_html='') {

		ob_start();
		?>
		<!doctype html>
		<html lang="en">
		<head>
			<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
			<meta http-equiv="X-UA-Compatible" content="IE=edge">
			<meta name="viewport" content="width=device-width">
			<title>WordPress Spam Blocker</title>
			<style type="text/css">@media only screen and (max-width: 599px) {table.body .container {width: 95% !important;}.header {padding: 15px 15px 12px 15px !important;}.header img {width: 200px !important;height: auto !important;}.content, .aside {padding: 30px 40px 20px 40px !important;}}</style>
		</head>
		<body style="height: 100% !important; width: 100% !important; min-width: 100%; -moz-box-sizing: border-box; -webkit-box-sizing: border-box; box-sizing: border-box; -webkit-font-smoothing: antialiased !important; -moz-osx-font-smoothing: grayscale !important; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; color: #444; font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; font-weight: normal; padding: 0; margin: 0; Margin: 0; font-size: 14px; mso-line-height-rule: exactly; line-height: 140%; background-color: #f1f1f1; text-align: center;">
		<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%" class="body" style="border-collapse: collapse; border-spacing: 0; vertical-align: top; mso-table-lspace: 0pt; mso-table-rspace: 0pt; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; height: 100% !important; width: 100% !important; min-width: 100%; -moz-box-sizing: border-box; -webkit-box-sizing: border-box; box-sizing: border-box; -webkit-font-smoothing: antialiased !important; -moz-osx-font-smoothing: grayscale !important; background-color: #f1f1f1; color: #444; font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; font-weight: normal; padding: 0; margin: 0; Margin: 0; text-align: left; font-size: 14px; mso-line-height-rule: exactly; line-height: 140%;">
			<tr style="padding: 0; vertical-align: top; text-align: left;">
				<td align="center" valign="top" class="body-inner wp-mail-smtp" style="word-wrap: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; mso-table-lspace: 0pt; mso-table-rspace: 0pt; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; color: #444; font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; font-weight: normal; padding: 0; margin: 0; Margin: 0; font-size: 14px; mso-line-height-rule: exactly; line-height: 140%; text-align: center;">
					<!-- Container -->
					<table border="0" cellpadding="0" cellspacing="0" class="container" style="border-collapse: collapse; border-spacing: 0; padding: 0; vertical-align: top; mso-table-lspace: 0pt; mso-table-rspace: 0pt; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; width: 600px; margin: 0 auto 30px auto; Margin: 0 auto 30px auto; text-align: inherit;">
						<!-- Header -->
						<tr style="padding: 0; vertical-align: top; text-align: left;">
							<td align="center" valign="middle" class="header" style="word-wrap: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; mso-table-lspace: 0pt; mso-table-rspace: 0pt; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; color: #444; font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; font-weight: normal; margin: 0; Margin: 0; font-size: 14px; mso-line-height-rule: exactly; line-height: 140%; text-align: center; padding: 30px 30px 22px 30px;">
								<h2>WordPress Spam Blocker</h2>
							</td>
						</tr>
						<!-- Content -->
						<tr style="padding: 0; vertical-align: top; text-align: left;">
							<td align="left" valign="top" class="content" style="word-wrap: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; mso-table-lspace: 0pt; mso-table-rspace: 0pt; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; color: #444; font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; font-weight: normal; margin: 0; Margin: 0; text-align: left; font-size: 14px; mso-line-height-rule: exactly; line-height: 140%; background-color: #ffffff; padding: 60px 75px 45px 75px; border-right: 1px solid #ddd; border-bottom: 1px solid #ddd; border-left: 1px solid #ddd; border-top: 3px solid #809eb0;">
								<div class="success" style="text-align: center;">
									<p class="check" style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; color: #444; font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; font-weight: normal; padding: 0; font-size: 14px; mso-line-height-rule: exactly; line-height: 140%; margin: 0 auto 16px auto; Margin: 0 auto 16px auto; text-align: center;">
										<img src="<?php echo esc_url( CF7Msb_PLUGIN_IMG_URL . '/icon-check.png' ); ?>" width="70" alt="Success" style="outline: none; text-decoration: none; max-width: 100%; clear: both; -ms-interpolation-mode: bicubic; display: block; margin: 0 auto 0 auto; Margin: 0 auto 0 auto; width: 50px;">
									</p>
									<p class="text-extra-large text-center congrats" style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; color: #444; font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; font-weight: normal; padding: 0; mso-line-height-rule: exactly; line-height: 140%; font-size: 20px; text-align: center; margin: 0 0 20px 0; Margin: 0 0 20px 0;">
										Daily Spam Blocking Report 
									</p>
									<p class="text-large" style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; color: #444; font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; font-weight: normal; padding: 0; text-align: left; mso-line-height-rule: exactly; line-height: 140%; margin: 0 0 15px 0; Margin: 0 0 15px 0; font-size: 16px;">
										Thank you for using the WordPress Spam Blocker. Below are the daily spam blocking report. 
									</p>
									
									<?php echo $report_html; ?>
									
									<p style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; color: #444; font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; font-weight: normal; padding: 0; text-align: left; font-size: 14px; mso-line-height-rule: exactly; line-height: 140%; margin: 0 0 15px 0; Margin: 0 0 15px 0;">
										Developers @ WordPress Spam Blocker
									</p>
								</div>
							</td>
						</tr>
						<!-- Aside -->
						<?php if ( 1 ) : ?>
							<tr style="padding: 0; vertical-align: top; text-align: left;">
								<td align="left" valign="top" class="aside upsell-mi" style="word-wrap: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; mso-table-lspace: 0pt; mso-table-rspace: 0pt; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; color: #444; font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; font-weight: normal; margin: 0; Margin: 0; font-size: 14px; mso-line-height-rule: exactly; line-height: 140%; background-color: #f8f8f8; border-top: 1px solid #dddddd; border-right: 1px solid #dddddd; border-bottom: 1px solid #dddddd; border-left: 1px solid #dddddd; text-align: center !important; padding: 30px 75px 25px 75px;">
									<h6 style="padding: 0; color: #444444; word-wrap: normal; font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; font-weight: bold; mso-line-height-rule: exactly; line-height: 130%; font-size: 18px; text-align: center; margin: 0 0 15px 0; Margin: 0 0 15px 0;">
										Unlock More Features with Premium Version
									</h6>
									<p class="text-large" style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; color: #444; font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; font-weight: normal; padding: 0; mso-line-height-rule: exactly; line-height: 140%; margin: 0 0 15px 0; Margin: 0 0 15px 0; font-size: 16px; text-align: center;">
										Unlimited Wordlist for Blocking<br>
										IP Address Block and Allow List<br>
										Adjust SPAM Error Message<br>
										Reporting Email Control<br>
										and much more...
									</p>
									<center style="width: 100%;">
										<table class="button large expanded orange" style="border-collapse: collapse; border-spacing: 0; padding: 0; vertical-align: top; text-align: left; mso-table-lspace: 0pt; mso-table-rspace: 0pt; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; color: #e27730; width: 100% !important;">
											<tr style="padding: 0; vertical-align: top; text-align: left;">
												<td class="button-inner" style="word-wrap: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; mso-table-lspace: 0pt; mso-table-rspace: 0pt; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; color: #444; font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; font-weight: normal; margin: 0; Margin: 0; text-align: left; font-size: 14px; mso-line-height-rule: exactly; line-height: 100%; padding: 20px 0 20px 0;">
													<table style="border-collapse: collapse; border-spacing: 0; padding: 0; vertical-align: top; text-align: left; mso-table-lspace: 0pt; mso-table-rspace: 0pt; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; width: 100% !important;">
														<tr style="padding: 0; vertical-align: top; text-align: left;">
															<td style="word-wrap: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; mso-table-lspace: 0pt; mso-table-rspace: 0pt; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; font-weight: normal; padding: 0; margin: 0; Margin: 0; font-size: 14px; text-align: center; color: #ffffff; background: #e27730; border: 1px solid #c45e1b; border-bottom: 3px solid #c45e1b; mso-line-height-rule: exactly; line-height: 100%;">
																<a href="<?php echo CF7Msb_CC_URL; ?>" style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; margin: 0; Margin: 0; font-family: Helvetica, Arial, sans-serif; font-weight: bold; color: #ffffff; text-decoration: none; display: inline-block; border: 0 solid #c45e1b; mso-line-height-rule: exactly; line-height: 100%; padding: 14px 20px 12px 20px; font-size: 20px; text-align: center; width: 100%; padding-left: 0; padding-right: 0;">
																	Upgrade to WordPress Spam Blocker Pro
																</a>
															</td>
														</tr>
													</table>
												</td>
											</tr>
										</table>
									</center>
								</td>
							</tr>
						<?php endif; ?>
					</table>
				</td>
			</tr>
		</table>
		</body>
		</html>

		<?php
		$message = ob_get_clean();

		return $message;
	}
	
	/**
	 * Set the HTML content type for a test email.
	 *
	 * @since 1.4.0
	 *
	 * @return string
	 */
	public static function set_test_html_content_type() {
		return 'text/html';
	}
	
	public function get_spam_report_html() {
		
		$spam_threshold = 1; 
		$send_mail = false; 
		
		$formidable = get_option('cf7msb_formidable_daily', []);
		$cf7 = get_option('cf7msb_cf7_daily', []);
		$wpforms = get_option('cf7msb_wpforms_daily', []);
		
		$total = 0; 
		
		$output = ''; 
		
		$cf7_report = ''; 
		foreach($cf7 as $date=>$data) {
			foreach($data as $form_id=>$spam_count) {
				
				if($spam_count > $spam_threshold) {
					$send_mail = true; 
				}
				
				$cf7_report .= '<tr>'; 
					$cf7_report .= '<td style="border:1px solid #ccc; padding:0.5rem">'; 
						$cf7_report .= 'Contact Form 7 Form'; 						
					$cf7_report .= '</td>'; 
					$cf7_report .= '<td style="border:1px solid #ccc; padding:0.5rem">'; 
						$cf7_report .= get_the_title($form_id); 
					$cf7_report .= '</td>'; 
					$cf7_report .= '<td style="border:1px solid #ccc; padding:0.5rem">'; 
						$cf7_report .= $spam_count; 
					$cf7_report .= '</td>'; 
				$cf7_report .= '</tr>'; 
			}
		}
		
		$formidable_report = ''; 
		foreach($formidable as $date=>$data) {
			foreach($data as $form_id=>$spam_count) {	

				if($spam_count > $spam_threshold) {
					$send_mail = true; 
				}
				
				$formidable_report .= '<tr>'; 
					$formidable_report .= '<td style="border:1px solid #ccc; padding:0.5rem">'; 
						$formidable_report .= 'Formidable Form'; 						
					$formidable_report .= '</td>'; 
					$formidable_report .= '<td style="border:1px solid #ccc; padding:0.5rem">'; 
						$formidable_report .= get_the_title($form_id); 				
					$formidable_report .= '</td>'; 
					$formidable_report .= '<td style="border:1px solid #ccc; padding:0.5rem">'; 
						$formidable_report .= $spam_count; 
					$formidable_report .= '</td>'; 
				$formidable_report .= '</tr>'; 
			}
		}
		
		$wpforms_report = ''; 
		foreach($wpforms as $date=>$data) {
			foreach($data as $form_id=>$spam_count) {
				
				if($spam_count > $spam_threshold) {
					$send_mail = true; 
				}
				
				$wpforms_report .= '<tr>'; 
					$wpforms_report .= '<td style="border:1px solid #ccc; padding:0.5rem">'; 
						$wpforms_report .= 'WPForms Form'; 						
					$wpforms_report .= '</td>'; 
					$wpforms_report .= '<td style="border:1px solid #ccc; padding:0.5rem">'; 
						$wpforms_report .= get_the_title($form_id); 
					$wpforms_report .= '</td>'; 
					$wpforms_report .= '<td style="border:1px solid #ccc; padding:0.5rem">'; 
						$wpforms_report .= $spam_count; 
					$wpforms_report .= '</td>'; 
				$wpforms_report .= '</tr>'; 
			}
		}
		
		
		$output .= '<table style="margin:30px 0;">';
			$output .= '<tr>';
				$output .= '<th style="border:1px solid #ccc;">Plugin Name</th>';
				$output .= '<th style="border:1px solid #ccc;">Form Title</th>';
				$output .= '<th style="border:1px solid #ccc;">SPAM Count Blocked</th>';				
			$output .= '</tr>';
			
			$output .= $cf7_report; 
			$output .= $wpforms_report; 
			$output .= $formidable_report; 
			
		$output .= '</table>';
		
		return ['send_mail'=>$send_mail, 'report_html'=>$output]; 
	}
	
}

$cf7msb_email = new CF7Msb_EMAIL(); 