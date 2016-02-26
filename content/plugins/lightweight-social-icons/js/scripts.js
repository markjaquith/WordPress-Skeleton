jQuery(document).ready(function($){
	function lsi_updatePlaceholders(){
		$('#widgets-right .choose-icon').each(function(){
			jQuery(this).change(function() {
				var select = jQuery(this);
							
				if ( jQuery(this).attr('value') == 'phone' ) {
					jQuery(this).next('input').attr('placeholder',lsiPlaceholder.phone);
				} else if ( jQuery(this).attr('value') == 'email' ) {
					jQuery(this).next().attr('placeholder',lsiPlaceholder.email);
				} else if ( jQuery(this).attr('value') == 'skype' ) {
					jQuery(this).next().attr('placeholder',lsiPlaceholder.username);
				}else if ( jQuery(this).attr('value') == '' ) {
					jQuery(this).next().attr('placeholder','');
				} else {
					jQuery(this).next().attr('placeholder','http://');
				}
			});
		}); 
	}
	lsi_updatePlaceholders();   
	$(document).ajaxSuccess(function(e, xhr, settings) {

		if(settings.data.search('action=save-widget') != -1 ) {  
			lsi_updatePlaceholders();       
		}
	});
});

// jQuery(document).ready(function($) {

	// $('.social-icon-fields .lsi-container').css('display','none');
    // $('.social-icon-fields .lsi-container').slice(0, 10).css('display','block'); // select the first ten
    // $('#load').click(function(e){ // click event for load more
        // e.preventDefault();
		// alert();
        // $('.social-icon-fields .lsi-container:hidden').slice(0, 10).show(); // select next 10 hidden divs and show them
        // if($('.social-icon-fields .lsi-container:hidden').length == 0){ // check if any hidden divs still exist
            // alert('No more divs'); // alert if there are none left
        // }
    // });
// });

// jQuery(document).on('DOMNodeInserted', '.widget-top', function () {
      // alert(jQuery('.widget-title', this).text());
// });