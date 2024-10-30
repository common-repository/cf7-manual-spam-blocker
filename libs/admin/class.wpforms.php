<?php

class CF7Msb_WPFORMS {
	
	function __construct() {
		add_action( 'wpforms_process', [ $this, 'wpforms_process'], 10, 3 );
	}
	
	/**
	 * Action that fires during form entry processing after initial field validation.
	 *
	 * @link   https://wpforms.com/developers/wpforms_process/
	 *
	 * @param  array  $fields    Sanitized entry field. values/properties.
	 * @param  array  $entry     Original $_POST global.
	 * @param  array  $form_data Form data and settings.
	 *
	 */
	function wpforms_process( $fields, $entry, $form_data ) {
			
		$invalid = false; 
		$form_id = $form_data['id']; 
		
		$settings = CF7Msb_OPTIONS::get_settings('wpforms', $form_id); 
		
		// check the field ID 4 to see if it's empty and if it is, run the error    
		foreach($fields as $field){
			$input_string = $field['value']; 
			
			if($settings['block_non_english'] && ! CF7Msb_Helpers::is_english($input_string)) {
				$invalid = true; 
				// Add to global errors. This will stop form entry from being saved to the database.
				// Uncomment the line below if you need to display the error above the form.
				wpforms()->process->errors[ $form_data['id'] ]['header'] = esc_html__( $settings['global_error_message'], 'cf7-manual-spam-blocker' );    
				
				// Check the field ID 4 and show error message at the top of form and under the specific field
				 if($settings['show_errors'])
					wpforms()->process->errors[ $form_data['id'] ] [ $field['id'] ] = esc_html__( $settings['global_error_message_for_field'], 'cf7-manual-spam-blocker' );    
					
				break;
			}
			
			// Break out if app_block_list option for this form if not checked
			if(1) {				
				$is_blocked = cf7msb_compare_lists($input_string, $settings['block_list']); 
				if($is_blocked) {														
					$invalid = true; 
					wpforms()->process->errors[ $form_data['id'] ]['header'] = esc_html__( $settings['global_error_message'], 'cf7-manual-spam-blocker' );    
					
					// Show error on field
					// ToDo: name type field is a special field, and error message is not displayed properly
					if($settings['show_errors']) {
						if($field['type']=='name') {
							wpforms()->process->errors[ $form_data['id'] ] [ $field['id'] ] = esc_html__( $settings['global_error_message_for_field'], 'cf7-manual-spam-blocker' );    
							wpforms()->process->errors[ $form_data['id'] ] [ $field['id'] . '-middle' ] = esc_html__( $settings['global_error_message_for_field'], 'cf7-manual-spam-blocker' );    
							wpforms()->process->errors[ $form_data['id'] ] [ $field['id'] . '-last' ] = esc_html__( $settings['global_error_message_for_field'], 'cf7-manual-spam-blocker' );    
						}
						else {
							wpforms()->process->errors[ $form_data['id'] ] [ $field['id'] ] = esc_html__( $settings['global_error_message_for_field'], 'cf7-manual-spam-blocker' );    
						}
					}
						
					
					break; 
				}				
			}
			
			// Break out if app_email_list option for this form if not checked
			if($field['type']=='email') {
				$is_blocked = cf7msb_compare_lists($input_string, $settings['email_list']); 
				
				if($is_blocked) {	
					$invalid = true; 				
					wpforms()->process->errors[ $form_data['id'] ]['header'] = esc_html__( $settings['global_error_message'], 'cf7-manual-spam-blocker' );    
					
					// Show error on field
					if($settings['show_errors'])
						wpforms()->process->errors[ $form_data['id'] ] [ $field['id'] ] = esc_html__( $settings['global_error_message_for_field'], 'cf7-manual-spam-blocker' );    
					
					break; 
				}				
			}
		}
		
		if($invalid) {
			CF7Msb_OPTIONS::update_counter_daily('wpforms', $form_id); 
		}
	}
		
}

$cf7msb_wpforms = new CF7Msb_WPFORMS(); 