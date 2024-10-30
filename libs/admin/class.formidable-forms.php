<?php

class CF7Msb_FORMIDABLE {
	
	function __construct() {		
		add_filter('frm_validate_entry', [$this, 'frm_validate_entry'], 10, 2);
		add_filter('frm_validate_field_entry', [$this, 'frm_validate_field_entry'], 10, 4);		
	}
	
	function frm_validate_entry($errors, $values){
		
		$invalid = false; 
		$form_id = $values['form_id'];
		$settings = CF7Msb_OPTIONS::get_settings('formidable',  $form_id);
		
		foreach($_POST['item_meta'] as $key=>$val){
			
			$input_string = strtolower($val);
			
			// Apply Non-English Blocking
			if($settings['block_non_english'] && ! CF7Msb_Helpers::is_english($val)) {
				$invalid = true; 
				$errors['spam'] = esc_html__( $settings['global_error_message'], 'cf7-manual-spam-blocker' );    
				
				// Show error on field
				if($settings['show_errors'])
					$errors['field'. $key] = esc_html__( $settings['global_error_message_for_field'], 'cf7-manual-spam-blocker' );    
				
				break;
			}
			
			// Break out if app_block_list option for this form if not checked
			if(1) {				
				$is_blocked = cf7msb_compare_lists($input_string, $settings['block_list']); 
				if($is_blocked) {														
					$invalid = true; 
					$errors['spam'] = esc_html__( $settings['global_error_message'], 'cf7-manual-spam-blocker' );    
					
					if($settings['show_errors'])
						$errors['field'. $key] = esc_html__( $settings['global_error_message_for_field'], 'cf7-manual-spam-blocker' );    
				
					break; 
				}				
			}						
		}
		
		if($invalid) {
			CF7Msb_OPTIONS::update_counter_daily('formidable', $form_id); 
		}
		return $errors;
	}
	
	function frm_validate_field_entry($errors, $posted_field, $posted_value, $args) {
		
		if($posted_field->type == 'email') {
			
			$form_id = $posted_field->form_id; 
			$settings = CF7Msb_OPTIONS::get_settings('formidable',  $form_id);
			
			$input_string = strtolower($posted_value); 
			
			$is_blocked = cf7msb_compare_lists($input_string, $settings['email_list']); 
			
			if($is_blocked) {														
				$errors['spam'] = esc_html__( $settings['global_error_message'], 'cf7-manual-spam-blocker' );    
				
				CF7Msb_OPTIONS::update_counter_daily('formidable', $form_id); 
				
				// Show error on field
				if($settings['show_errors'])
					$errors['field'. $posted_field->id] = esc_html__( $settings['global_error_message_for_field'], 'cf7-manual-spam-blocker' );    
			}	
		}
		
		return $errors; 
	}
}

$cf7msb_formidable = new CF7Msb_FORMIDABLE(); 