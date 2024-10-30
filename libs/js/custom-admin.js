jQuery(document).ready(function() { // wait for page to finish loading

	//Notice dismissable
	jQuery('.cf7msb-dismissable').click( function(e) {
		
		e.preventDefault();
		$btnClicked = jQuery(this); 
		$parent = jQuery(this).parent(); 
		$parentBox = jQuery(this).closest('.notice'); 
		
		$parentBox.hide(); 
		
		jQuery.post(
			ajaxurl,
			{
				action : 'cf7msb_notice_dismissable',
				dataBtn : $btnClicked.attr('data-btn'),
			},
			function( response ) {				
				if( response.success === true ) {
					
				}
				else {
					
				}				
			} 
		);
	});

	//Update CF7Msb Settings
	jQuery('.cf7msb-update-settings').click(function(e) {
		
		e.preventDefault();
		btnClicked = jQuery(this);
		parentForm = btnClicked.parents('form');
		formFields = parentForm.serialize();

		spinner = jQuery('.fa-spinner');
		resultAlert = jQuery('.cf7msb-result');
		resultAlert.removeClass('alert-success alert-danger').html('');
		spinner.removeClass('d-none').addClass('fa-spin');
		jQuery.post(
			ajaxurl,
			{
				action : 'cf7msb_update_settings',
				fields : formFields,
			},
			function( response ) {
				spinner.removeClass('fa-spin').addClass('d-none');
				if( response.success === true ) {
					if(response.data.reason !== 'undefined') {
						resultAlert.addClass('alert-success').removeClass('d-none').html(response.data.reason);
					}
					if(response.data.reload !== 'undefined') {
						location.reload();
					}
				}
				else {
					if(response.data.reason !== 'undefined') {
						resultAlert.addClass('alert-danger').html(response.data.reason);
					}
				}				
			} 
		);
	});

	//Help and Support form submit button
	jQuery('.cf7msb-submit-btn').click( function(e) {
		
		e.preventDefault();
		$btnClicked = jQuery(this); 
		$parentForm = jQuery(this).closest('form'); 
		$loader = $parentForm.find('.cf7msb-loader'); 
		$importResult = $parentForm.find('.result-area'); 
		
		//$btnClicked.addClass('animate'); 
		$loader.css({'visibility':'visible'}); //slideDown(); 		
		$importResult.html('').removeClass('error').removeClass('success').slideUp(); 
		$btnClicked.addClass('disable');
		
		formFields = $parentForm.serialize(); 

		jQuery.post(
			ajaxurl,
			{
				action : 'action_cf7msb_contact_support',
				fields : formFields,
			},
			function( response ) {				
				if( response.success === true ) {					
					$importResult.addClass('notice notice-success').html(response.data.reason);
					if(response.data.reload)
						setTimeout(function() {location.reload();}, 1000);		

					$parentForm[0].reset(); 
				}
				else {
					$importResult.addClass('error').html(response.data.reason);
					if(response.data.reason.indexOf("exists")==0) {
					}						
				}
				$importResult.slideDown();
				$loader.css({'visibility':'hidden'}) //.slideUp(); 
			} 
		);
	});
	
	jQuery('.cf7msb-import-words').on('click', function(e) {

		btnClicked = jQuery(this);
		
		alert('Only Available in Pro version.');
		
	});
	
	jQuery(function() {
		let header = jQuery(".cf7msb-layout__header");
		jQuery(window).scroll(function() {
			let scroll = jQuery(window).scrollTop();

			if (scroll >= 25) {
				header.addClass("is-scrolled");
			} else {
				header.removeClass("is-scrolled");
			}
		});
	});

});