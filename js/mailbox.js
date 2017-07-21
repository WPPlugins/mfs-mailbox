/**
 * Mailbox Integration specific js
 *
 * @package Mailbox 
 * @author  Mindfiresolutions
 */

jQuery(document).ready(function($){
    
    	/* Check and uncheck all check boxes */
	jQuery('#check-all').click (function () {
        
		/* If this is checked then all Checkboxes 
		 * should be checked otherwise unchecked 
		 */
		if (jQuery(this).is (':checked')) {
			jQuery('.check-mail').attr('checked', true);
               jQuery('input[name="mail-ids"]').val('all');
		} else {
			jQuery('.check-mail').attr('checked', false);
		}
	});
     
	/* on clicking of individual checkbox for trashing */
	jQuery('.check-mail').click (function () {
          
		if (jQuery('#check-all').is (':checked')) {
			jQuery('#check-all').attr('checked', false);
			var trash_ids = new Array();
			trash_ids = jQuery('#all-mail-ids').val().split(',');
               
               /* this will check the index of the current element if its present */
			if( -1 >= (jQuery.inArray(jQuery(this).val(),trash_ids)) ) {
                    /* do nothing */
			} else {
                    /* this will remove the current element from the array if its there */
				trash_ids.splice( jQuery.inArray(jQuery(this).val(),trash_ids),1 );
                    
                    /* this is for imploding array with delemeter ',' */
				trash_ids.join(',');
                    
                    /* the value will be added to the hidden field for trash/delete functionality */
				jQuery('input[name="mail-ids"]').val(trash_ids);
			}
			
			/* this will check the index of the current element if its present */
			if( -1 >= (jQuery.inArray('all',trash_ids)) ) {
                    /* do nothing */
			} else {
                    /* this will remove the current element from the array if its there */
				trash_ids.splice( jQuery.inArray('all',trash_ids),1 );
                    
                    /* this is for imploding array with delemeter ',' */
				trash_ids.join(',');
                    
                    /* the value will be added to the hidden field for trash/delete functionality */
				jQuery('input[name="mail-ids"]').val(trash_ids);
			}
		}
             
		/* If this is checked then the corresponding value will be stored in the hidden field */
		if (jQuery(this).is (':checked')) {
               /* the checkbox value will be added to the hidden field value for trash/delete purpose */
			if(jQuery('input[name="mail-ids"]').val() == '') {
				jQuery('input[name="mail-ids"]').val(jQuery(this).val());
			} else {
				jQuery('input[name="mail-ids"]').val(jQuery('input[name="mail-ids"]').val() +',' + jQuery(this).val());
			}
               
		} else {
			var trash_ids = new Array();
			trash_ids = jQuery('input[name="mail-ids"]').val().split(',');
               
               /* this will check the index of the current element if its present */
			if( -1 >= (jQuery.inArray(jQuery(this).val(),trash_ids)) ) {
                    /* do nothing */
			} else {
                    /* this will remove the current element from the array if its there */
				trash_ids.splice( jQuery.inArray(jQuery(this).val(),trash_ids),1 );
                    
                    /* this is for imploding array with delemeter ',' */
				trash_ids.join(',');
                    
                    /* the value will be added to the hidden field for trash/delete functionality */
				jQuery('input[name="mail-ids"]').val(trash_ids);
			}
		}
	});
	
    /* this is for validating compose form */
    jQuery('#compose-submit').click (function () {
          
		if( '' == jQuery('#mail-subject').val() ) {
			return confirm("Do you want to send Message without having subject");
		}

		if( '' == jQuery('#mail-message').val() ) {
		   return confirm("Do you want to send Message without having message body");
		}

		return true;
     });
	
	jQuery('#compose-draft').click (function () {
		if( '' == jQuery('#mail-subject').val() ) {
			return confirm("Do you want to save Message without having subject");
		}

		if( '' == jQuery('#mail-message').val() ) {
		   return confirm("Do you want to save Message without having message body");
		}

		return true;
	});
	
	jQuery('#wp-content-editor-tools').remove();
})