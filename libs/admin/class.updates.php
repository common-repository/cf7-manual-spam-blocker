<?php

class CF7Msb_UPDATES {
	
	function __construct() {
	
	}

	public static function update_for_2_0_0() {
		
		if(false === get_option('cf7msb_cf7')) {
			
			$cf7msb_forms = get_option('cf7msb_forms', array());
			$cf7msb_cf7 = []; 
			
			foreach($cf7msb_forms as $form_id=>$data) {
				$cf7msb_cf7[$form_id] = $data['spam_counter']; 
			}
			
			update_option('cf7msb_cf7', $cf7msb_cf7); 
		}
		
	}
}
