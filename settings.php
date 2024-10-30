<?php

class CF7Msb_OPTIONS {

	public static function get_option( $name, $default = false ) {
		$option = get_option( 'cf7msb_settings' );

		if ( false === $option ) {
			return $default;
		}

		if ( isset( $option[$name] ) ) {
			return $option[$name];
		} else {
			return $default;
		}
	}

	public static function update_option( $name, $value ) {
		$option = get_option( 'cf7msb_settings' );
		$option = ( false === $option ) ? array() : (array) $option;
		$option = array_merge( $option, array( $name => $value ) );
		update_option( 'cf7msb_settings', $option );
	}
	
	public static function get_settings($form_plugin='', $form_id='') {
		$cf7msb_settings = get_option('cf7msb_settings', cf7msb_settings_defaults());
		
		// Hint: New Filters introduced: 1.8.0
		foreach($cf7msb_settings as $key=>$value) {
			$filter_name = 'cf7msb_'.$key; 
			$cf7msb_settings[$key] = apply_filters($filter_name, $value, $form_plugin, $form_id);
		}
        
		return $cf7msb_settings; 		
	}
	
	public static function update_counter($plugin_name, $form_id) {
		
		$option_name = 'cf7msb_'.$plugin_name; 
		$existing = get_option($option_name, []); 
		
		if(isset($existing[$form_id]) && absint($existing[$form_id])) {
			$existing[$form_id]++; 
		}
		else $existing[$form_id] = 1; 
		
		update_option($option_name, $existing); 
	}
	
	public static function update_counter_daily($plugin_name, $form_id) {
		
		$option_name = 'cf7msb_'.$plugin_name.'_daily'; 
		$existing = get_option($option_name, []); 
		$current_date = current_time('Y-m-d'); 
		
		if(!isset($existing[$current_date])) {
			
			// Check if any older data is available
			if(sizeof($existing)) {
				$cf7msb_last_report_email = get_option('cf7msb_last_report_email', '');
				$existing_keys = array_keys($existing); 
				
				// if last report emailis less than the last date in existing
				if($cf7msb_last_report_email < $existing_keys[0]) {				
					$cf7msb_email = new CF7Msb_EMAIL(); 
					$sent = $cf7msb_email->send_report(); 
				}
			}
			
			$existing = []; 
			$existing[$current_date] = []; 
		}
		
		if(isset($existing[$current_date][$form_id]) && absint($existing[$current_date][$form_id])) {
			$existing[$current_date][$form_id]++; 
		}
		else $existing[$current_date][$form_id] = 1; 
		
		update_option($option_name, $existing); 
		
		CF7Msb_OPTIONS::update_counter($plugin_name, $form_id); 
	}
}