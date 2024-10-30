<?php

class CF7Msb_Helpers {
	
	function __construct() {
	
	}

	public static function cf7msb_process_values($values=array()) {
	
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
		Check if Enlglish Strings
		Concept: strlen and mb_strlen with utf-8 should be equal
	*/
	public static function is_english($input) {
		return (strlen($input) == mb_strlen($input, 'utf-8'));
	}
	
	public static function startsWith($str, $char){
		return $str[0] === $char;
	}

	public static function settings_defaults($key='') {
		$settings = array(
			'show_errors'=>1, 
			'add_quick_links'=>1, 
			'block_non_english'=>1, 
			'global_error_message' =>'SPAM Detected!',
			'global_error_message_for_field' => 'SPAM is restricted',
			'block_list'=> array(), 
			'email_list'=> array()
		);
		
		if(isset($settings[$key])) {
			return $settings[$key]; 
		}
		
		return $settings; 
	}
}

$cf7msb_helpers = new CF7Msb_Helpers(); 