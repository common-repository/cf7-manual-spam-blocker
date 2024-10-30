<?php

include 'class.helpers.php'; 
include 'class.updates.php'; 
include 'class.email.php'; 
include 'class.wpforms.php'; 
include 'class.formidable-forms.php'; 

class CF7Msb_Form_Panels {
	
	protected static $instance;
	
	function __construct() {
		if(is_admin()) {
			add_filter('wpcf7_editor_panels', array($this, 'editor_panels'));
			add_action('wpcf7_after_save', array($this, 'save_form'));
		}
		else {
			// Get Saved Settings Array
		    $cf7msb_settings = get_option('cf7msb_settings', cf7msb_settings_defaults());

		    // Add Filter for non-english-character
			if($cf7msb_settings['block_non_english']==1) {
                add_filter('wpcf7_spam', array($this, 'filter_wpcf7_spam'), 99, 1);
            }

			// Add filters for filterable fields
			$tags = array('email*', 'email', 'tel', 'tel*', 'text', 'text*', 'textarea', 'textarea*', 'url', 'url*');
			foreach($tags as $tag) {
                add_filter( 'wpcf7_validate_'.$tag, array($this, 'validate_tags'), 20, 2);
            }
		}
	}

	/*
	 * Add editor_panel to CF7 Edit Page
	 */
	public function editor_panels($panels) {
		$new_settings_tab = array(
			'cf7msb_panel' => array(
				'title' => __('Block Spam', 'cf7-manual-spam-blocker'),
				'callback' => array(&$this, 'panel_form'),
			)
		);
		
		$panels = array_merge($panels, $new_settings_tab);
		
		return $panels;
	}

	/*
	 * Hook our options to save while saving the Contact Form
	 */
	public function save_form($form) {
		//print_r($_POST); wp_die(); 

		$cf7msb_forms = get_option('cf7msb_forms', array()); 

		$cf7msb_conditions = $_POST['cf7msb_conditions'];
		//wp_die(var_dump($cf7msb_conditions));
		
		$tags_scanned = $this->scanned_form_tags($form);
		foreach($tags_scanned as $type=>$tags) {
			foreach($tags as $tag) {
				$tagName = $tag->name;

				$values = $this->process_values($cf7msb_conditions[$tagName]['value']);
				$values = array_unique($values);
				$values = array_slice($values, 0, 5);
				$cf7msb_conditions[$tagName]['value'] = implode(', ', $values);

                // Hint: Force the same error message in free version
				$cf7msb_conditions[$tagName]['message'] =  CF7Msb_Helpers::settings_defaults('global_error_message_for_field');
			}
		}

		$cf7msb_forms[$form->id()] = array(
			'conditions' => $cf7msb_conditions,
            'apply_block_list' => $_POST['cf7msb_apply_block_list'],
            'apply_email_list' => $_POST['cf7msb_apply_email_list'],
			'spam_counter' => !empty($cf7msb_forms[$form->id()]['spam_counter']) ? $cf7msb_forms[$form->id()]['spam_counter'] : 0
        );

		update_option('cf7msb_forms',  $cf7msb_forms);
	}
	
	/*
	 * Gets Scanned Form Tags from CF7 class
	 */
	public function scanned_form_tags($form) {		
		$tags_scanned = $form->scan_form_tags();
		$valid_tags = array('free'=>array(), 'pro'=>array()); 
		
		foreach($tags_scanned as $tag) {
			if(in_array($tag->basetype, array('email', 'url', 'text', 'textarea', 'tel'))) {
				$valid_tags['free'][] = $tag; 
			}
		}
		
		return $valid_tags; 
	}

	/*
	 * Blocking conditions
	 */
	public function get_conditions($return='array', $selected='', $tag_type='') {		
		
		// $cond = array(
			// '=' => ucwords($tag_type).' Equals to',			
			// 'contains' => ucwords($tag_type).' Contains',
		// );
		
		$cond = array(
			'=' => ' Equals to',			
			'contains' => ' Contains',
		);
		
		$options = '<option value="">Choose Condition</option>'; 
		foreach($cond as $k=>$v) {
			$select = (!empty($selected) AND $k==$selected) ? 'selected' : '';
			$disabled = ($k=='') ? 'disabled' : '';
			$options .= '<option value="'.$k.'" '.$select. ' ' . $disabled.'>'.$v.'</option>';			
		}
		
		if($return=='array') {
			return $cond; 
		}
		else return $options; 
	}

	/*
	 * Create Blocking Criteria Form in Panel
	 */
	function panel_form($form, $panel="") {
		$cf7msb_forms = get_option('cf7msb_forms', array()); 
		$cf7msb_settings = get_option('cf7msb_settings', cf7msb_settings_defaults()); 

		if(!isset($cf7msb_forms[$form->id()])) {
			$cf7msb_forms[$form->id()] = array(
				'conditions' => array(), 
				'apply_block_list' => 'checked',
				'apply_email_list' => 'checked',
				'spam_counter' => 0,
			); 
		}

		$cf7msb_conditions = $cf7msb_forms[$form->id()]['conditions'];
        $cf7msb_apply_block_list = $cf7msb_forms[$form->id()]['apply_block_list'];
        $cf7msb_apply_email_list = $cf7msb_forms[$form->id()]['apply_email_list'];
		$error_visibility = ($cf7msb_settings['show_errors']) ? '' : ' (Not visible to user)'; 		

		$tags_scanned = $this->scanned_form_tags($form);

		$fields = ''; 
		$notes = [
			'You can add upto <strong>5 blocking words</strong> for each field.',
			'If you have updated the form fields, please save the form again.',
			'To block the Automatic SPAM, use the Honeypot field <strong>[cf7msb-honeypot].</strong>'
		]; 
		?>
		<div id="wpcf7-cf7msb" class="contact-form-editor-cf7msb cf7msg_conditions">
			
			<style>
				.cf7msg_conditions table,.cf7msg_conditions textarea { width: 100%; }
				.get-pro-wrap { direction: ltr; margin: 10px 0;}
				.get-pro {display: block; padding: 15px; border: 1px solid #ddd; background-color: #fff;}
				span.dashicons.dashicons-star-filled { color: #e7c201;}
			</style>
			<a href="https://codecanyon.net/item/contact-form-7-manual-spam-blocker/20605470?ref=OmAkSols" target="_blank"><img style="width:100%;" src="<?php echo CF7Msb_PLUGIN_IMG_URL ?>/Cover-800x225.png"></a>
			<div class="get-pro-wrap">
	            <div class="get-pro">
					<ol>
						<?php foreach($notes as $note) { ?>
							<li><?php echo _e($note, 'cf7-manual-spam-blocker'); ?></li>
						<?php } ?>
					</ol>
	            	
	            </div>
	        </div>
			<?php
            $tr_style = 'style="border:1px solid #ccc;"';
			$fields .= '<fieldset>';
			$fields .= '<table class="cf7msb-block-spam-table form-table"><tbody>';
				$fields .= "<tr {$tr_style}>";
					$fields .= '<th style="font-size: 20px; text-align:center;">CF7 Field</th>';
					$fields .= '<th style="font-size: 20px; text-align:center;">Block List and Error Message</th>';
				$fields .= '</tr>';
				$show = true;
				foreach($tags_scanned as $type=>$tags) {
					foreach($tags as $tag) {
						$tagName = $tag->name;
						if(!isset($tagName) OR empty($tagName))
							continue; 
						
						$legend = '['.$tagName.']';
						$legend_title = '<span style="font-weight:bold;">'.$legend.'</span>';
						
						$default_value = isset($cf7msb_conditions[$tagName]['value']) ? $cf7msb_conditions[$tagName]['value'] : '';
						$default_message = isset($cf7msb_conditions[$tagName]['message']) ? $cf7msb_conditions[$tagName]['message'] : 'SPAM is blocked';
						$selected_condition = isset($cf7msb_conditions[$tagName]['condition']) ? $cf7msb_conditions[$tagName]['condition'] : '';

						$conditions = $this->get_conditions('options', $selected_condition, $tag->basetype);

						$style = '';
						if($type == 'pro') {
							$style = 'style="background-color: #fffff2"';
							if($show) {
								$fields .= '<tr>';
									$fields .= '<td colspan="2" style="font-size: 18px; font-weight:bold; background-color: #fffff2">';
										$fields .= 'Also Available in PRO';
									$fields .= '</td>';
								$fields .= '</tr>';
								$show = false;
							}
						}
						$fields .= '<tr '.$style.'>';
							$fields .= '<td style="text-align:left;">';
							
								$fields .= '<b>Block If </b>';
								$fields .= $legend_title;
								$fields .= '<br><select name="cf7msb_conditions['.$tagName.'][condition]" value="'.$selected_condition.'" style="margin-top:5px;">'.$conditions.'</select>';
								
								// Disable - Field Type and DashIcons
								$fields .= '<div class="cf7msb-field-container">';
                                    $fields .= '<span class="cf7msb-field-type">Type: '.$tag->basetype;
                                        switch($tag->basetype) {
                                            case 'email':
                                            case 'email*':
                                                $fields .= '<i class="dashicons dashicons-email-alt" style="padding-left:5px"></i>';
                                                break;
                                            case 'tel':
                                            case 'tel*':
                                                $fields .= '<i class="dashicons dashicons-phone" style="padding-left:5px"></i>';
                                                break;
                                            case 'text':
                                            case 'text*':
                                                $fields .= '<i class="dashicons dashicons-editor-spellcheck" style="padding-left:5px"></i>';
                                                break;
                                            case 'textarea':
                                            case 'textarea*':
                                                $fields .= '<i class="dashicons dashicons-editor-justify" style="padding-left:5px"></i>';
                                                break;
                                            case 'url':
                                            case 'url*':
                                                $fields .= '<i class="dashicons dashicons-admin-links" style="padding-left:5px"></i>';
                                                break;
                                        }
                                    $fields .= '</span>';
							    $fields .= '</div>';
							$fields .= '</td>';

							$fields .= '<td>';
								$fields .= '<fieldset>';
									
									$fields .= '<div style="display:block;margin-bottom: 5px;">';
										$fields .= '<b>Wordlist </b><small><b>(Enter comma separated terms or One entry per line)</b></small>';
										$fields .= '<textarea name="cf7msb_conditions['.$tagName.'][value]" rows="4" id="cf7msb-value-'.$tagName.'">'.$default_value.'</textarea>';
									$fields .= '</div>';
									$fields .= '<div style="display:block">';
										if($cf7msb_settings['show_errors']) {
											$fields .= '<b>Error Message</b> <span style="">(Editable in <a style="color:red;" href="'.CF7Msb_CC_URL.'" target="_blank">Premium Version</a>)</span>';
											$fields .= '<br/><input readonly name="cf7msb_conditions['.$tagName.'][message]" type="text" value="'.$default_message.'" size="70" class="large-text code">';
										}
										else {
											$fields .= '<b>Error Message</b> (not visible to users)<br/><input disabled name="cf7msb_conditions['.$tagName.'][message]" type="text" value="'.$default_message.'" size="70" class="large-text code">';
										} 
									$fields .= '</div>';
									$fields .= '<input name="cf7msb_conditions['.$tagName.'][type]" type="hidden" value="'.$tag->basetype.'">';
									$fields .= '<input name="cf7msb_conditions['.$tagName.'][tag]" type="hidden" value="'.$tag->type.'">';
								$fields .= '</fieldset>';
							$fields .= '</td>';						
						$fields .= '</tr>';	
					}		
				}
				$fields .= '<tr style="background-color: #fffff2">';
					$fields .= '<td>';
						$fields .= '<strong>Choose Global Blocklists</strong>';
					$fields .= '</td>';
					$fields .= '<td>';
            $fields .= '<input type="checkbox" name="cf7msb_apply_block_list" value="checked" '.$cf7msb_apply_block_list.' />Global Words Blocklist&nbsp;';
            $fields .= '<input type="checkbox" name="cf7msb_apply_email_list" value="checked" '.$cf7msb_apply_email_list.' />Global Email Blocklist';
					$fields .= '</td>';
				$fields .= '</tr>';	
			$fields .= '</tbody></table></fieldset>';
			echo $fields; ?>
			<div class="get-pro-wrap">
	            <div class="get-pro">
	            	<span class="dashicons dashicons-warning"></span>
	               	<strong>To enable unlimited wordlist and editable spam messages, please checkout <span class="dashicons dashicons-star-filled"></span><a href="<?php echo CF7Msb_CC_URL; ?>" target="_blank">Wordpress SPAM Blocker Pro</a><span class="dashicons dashicons-star-filled"></span>.</strong>
	            </div>
	        </div>		
			<?php //echo 'Post: '; print_r($post); ?>
		</div> <?php
	}

	/*
	 * Filter values as an array and save as imploded string
	 */
	function process_values($values=array()) {
		
		if(!is_array($values)) {
			if(strstr($values, "\n") !== false) {
				$values = nl2br($values);
				if(strpos($values,'<br />')) {
					$values = explode('<br />',$values);
				}
				else $values = (array)$values;
			}
			elseif(strpos($values, ',') !== -1) {
				$values = explode(',', $values);
			}
		}
		if(is_array($values)) {
			$values = array_map('trim', $values);
			$values = implode(',', $values);

			if(strpos($values, ',') !== -1) {
				$values = explode(',', $values);
			}	
		}
		$values = array_map('trim', $values);
		$values = array_map('strtolower', $values);
		
		return $values; 
	}

	/*
	 * WPCF7 Filter: Validate all input fields
	 */
	function validate_tags( $result, $tag ) {

		$tag = new WPCF7_FormTag( $tag );
		$invalid = false; $value = ''; $test = array(); 

		$filter = explode('_', current_filter());
		$filterFor = end($filter);
		
		$wpcf7 = WPCF7_ContactForm::get_current();
		$form_id = $wpcf7->id();

		$cf7msb_settings = get_option('cf7msb_settings', cf7msb_settings_defaults());  
		$cf7msb_forms = get_option('cf7msb_forms', array()); 

		if(!isset($cf7msb_forms[$form_id])) {
			$cf7msb_forms[$form_id] = array(
				'conditions' => array(), 
				'apply_block_list' => 'checked',
				'cf7msb_apply_email_list' => 'checked',
				'spam_counter' => 0,
			); 
		}

        $cf7msb_conditions = $cf7msb_forms[$form_id]['conditions'];
        $cf7msb_apply_block_list = $cf7msb_forms[$form_id]['apply_block_list'];
        $cf7msb_apply_email_list = $cf7msb_forms[$form_id]['apply_email_list'];
        $spam_counter = $cf7msb_forms[$form_id]['spam_counter'];

        // Hint: New Filters introduced: 1.8.0
        $cf7msb_block_list = apply_filters('cf7msb_block_list', $cf7msb_settings['block_list'], $form_id);
        $cf7msb_email_list = apply_filters('cf7msb_email_list', $cf7msb_settings['email_list'], $form_id);

        // Break out if conditions are not an array or no conditions
		if(!$invalid && is_array($cf7msb_conditions) AND count($cf7msb_conditions)) {
			foreach($cf7msb_conditions as $key=>$cond) {
				if(''!=$cond['condition'] AND $cond['tag']==$filterFor) {	
					if ( $key == $tag->name ) {
						
						$useTag = $tag->name; 
						$input = isset($_POST[$useTag]) ? sanitize_text_field($_POST[$useTag]) : '';

						$blocked_values = explode(', ', $cond['value']);
						$blocked_values = array_map('trim', $blocked_values);
						$error_message = isset($cond['message']) ? $cond['message']: $cf7msb_settings['global_error_message'];

						switch($cond['condition']) {
							case '=' :
								if(in_array(strtolower($input), $blocked_values)) {	
									$spam_counter++;
						            $invalid = true;
								}		
								break; 
							case 'contains' :
								foreach($blocked_values as $block_value) { 							
									if(strtolower($input) == strtolower($block_value) OR strpos(strtolower($input),strtolower($block_value))!==FALSE) {	
										$invalid = true;
										$spam_counter++;
										break; 
									}						
								}
								break; 
						}
					} 
				}		
			}
		}

		// Break out if app_block_list option for this form if not checked
        if(! $invalid && $cf7msb_apply_block_list == 'checked') {
            if(is_array($cf7msb_block_list) AND count($cf7msb_block_list)) {
                $useTag = $tag->name;
                $input = isset($_POST[$useTag]) ? $_POST[$useTag] : '';

                $error_message = ($cf7msb_settings['show_errors']) ? $cf7msb_settings['global_error_message'] : '';

                foreach($cf7msb_block_list as $block_value) {
                    if(strpos(strtolower($input),$block_value)!==FALSE) {
                        $spam_counter++;
                        $invalid = true;
                        break;
                    }
                }
            }
        }

        // Break out if apply_email option for this form if not checked
        if(! $invalid && $cf7msb_apply_email_list == 'checked') {
            if(is_array($cf7msb_email_list) AND count($cf7msb_email_list)) {

                // Apply Email list only if tag type is email or email*
                if(in_array($tag->basetype, ['email', 'email*'])) {
                    $useTag = $tag->name;
                    $input = isset($_POST[$useTag]) ? $_POST[$useTag] : '';

                    $error_message = ($cf7msb_settings['show_errors']) ? $cf7msb_settings['global_error_message'] : '';

                    foreach($cf7msb_email_list as $block_value) {
                        if(strpos(strtolower($input),$block_value)!==FALSE) {
                            $spam_counter++;
                            $invalid = true;
                            break;
                        }
                    }
                }
            }
        }

		// Spam Detected: Show Error Message, or Mark as SPAM
		if($invalid) {
			add_filter('wpcf7_spam', '__return_true');
			
			if($cf7msb_settings['show_errors'])
				$result->invalidate($tag, $error_message); //.print_r($test,true) );	
			
			
			$cf7msb_forms[$form_id]['spam_counter'] = $spam_counter;
			update_option('cf7msb_forms', $cf7msb_forms);
			
			CF7Msb_OPTIONS::update_counter_daily('cf7', $form_id); 
		}
		
		return $result;
	}

	/*
	 * WPCF7 Filter: for SPAM marking
	 * Currently used for non_english characters
	 */
	function filter_wpcf7_spam($spam) {
		if($spam) {
			return $spam;
		}
		
		foreach($_POST as $key=>$val) {
			
			/* 
				Ignore all posted data that starts with "_"
				assuming that these are default WPCF7 Hidden Form Fields
			*/
			if(CF7Msb_Helpers::startsWith($key, '_')) 
				continue; 
			
			if(! CF7Msb_Helpers::is_english($val)) {
				$spam = true;
				$cf7msb_forms = get_option('cf7msb_forms', array()); 
				$form_id = absint($_POST['_wpcf7']); 
				
				if(!isset($cf7msb_forms[$form_id])) {
					$cf7msb_forms[$form_id] = array(
						'conditions' => array(), 
						'apply_block_list' => 'checked',
						'cf7msb_apply_email_list' => 'checked',
						'spam_counter' => 0,
					); 
				}
				
				$cf7msb_forms[$form_id]['spam_counter']++; 
				update_option('cf7msb_forms', $cf7msb_forms);
				
				CF7Msb_OPTIONS::update_counter_daily('cf7', $form_id); 
				
				// Leaving a spam log.
				$submission = WPCF7_Submission::get_instance();
			 
				$submission->add_spam_log( array(
				  'agent' => 'cf7_manual_spam_blocker',
				  'reason' => "Non-English characters detected in " . $key . " field.",
				) );
				
				break;
			}
		}
		
		return $spam; 
	}
	
}

$cf7_panel = new CF7Msb_Form_Panels();

add_action('admin_notices', 'cf7msb_show_notice');
function cf7msb_show_notice() {
	if(isset($_GET['page']) AND ($_GET['page'] == 'wpcf7') AND isset($_GET['action']) AND ($_GET['action'] == 'edit') AND isset($_GET['alert']) AND $_GET['alert'] ) {
		echo '<div class="notice notice-info">
			<h2 style="margin:0.5em 0;">'.CF7Msb_PLUGIN_TITLE.'</h2>
			<p>'.__( 'Go to Block Spam tab and add the input values to block.', 'cf7-manual-spam-blocker' ).'
		</div>';
	}
}

add_action('wpcf7_admin_misc_pub_section', 'cf7msb_show_spam_counter', 10, 1);
function cf7msb_show_spam_counter($post_id) {
	$cf7msb_forms = get_option('cf7msb_forms', array());
	$spam_counter = isset($cf7msb_forms[$post_id]['spam_counter']) ? $cf7msb_forms[$post_id]['spam_counter'] : 0;
	echo '<center><span style="margin: 10px" class="button-secondary" title="'. $spam_counter .' SPAM blocked"><i style="padding:3px 0px" class="dashicons dashicons-dismiss"></i> <strong>'. $spam_counter .' SPAM blocked</strong></span></center>';
}

function cf7msb_settings_defaults() {
	return CF7Msb_Helpers::settings_defaults();
}

function cf7msb_compare_lists($input_string, $list) {	
	if(is_array($list) AND count($list)) {
		foreach($list as $block_value) {
			if(strpos($input_string, $block_value) !== FALSE) {
				return true; 
			}
		}
	}
	
	return false; 
}