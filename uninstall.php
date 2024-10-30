<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Hint: If pro version is yet not deleted, keep the settings
if(get_option('cf7msb') !=false)
	return; 


$options = [
	'cf7msb_version_lite', 'cf7msb_install_date', 'cf7msb_settings', 'cf7msb_review_notice', 'cf7msb_forms',
	'cf7msb_formidable', 'cf7msb_formidable_daily', 'cf7msb_wpforms', 'cf7msb_wpforms_daily', 'cf7msb_cf7', 'cf7msb_cf7_daily', 
	'cf7msb_last_report_email'
];

// For Pro:
$options[] = 'cf7msb_update_notices';
$options[] = 'cf7msb_plugin_message';
$options[] = 'cf7msb_update_url';
$options[] = 'cf7msb_is_activated';
$options[] = 'cf7msb_license_key';
$options[] = 'cf7msb_grant_access_by';
$options[] = 'cf7msb_grant_access_time';

foreach($options as $opt) {
    delete_option($opt);
}

?>