(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */
	 
	$(function() {
		jQuery("#myworks_wc_qbo_sync_check_license_desk").submit(function(e){            
            e.preventDefault();
			var mw_wc_qbo_sync_license_desk = jQuery('#mw_wc_qbo_sync_license_desk').val();
			mw_wc_qbo_sync_license_desk = jQuery.trim(mw_wc_qbo_sync_license_desk);
			if(mw_wc_qbo_sync_license_desk==''){
				alert('Please enter license key');
				return false;
			}
            var data = {
                "action": "myworks_wc_qbo_sync_check_license_desk"
            };
            data = jQuery(this).serialize() + "&" + jQuery.param(data);
			jQuery('#mwqs_license_chk_loader').css('visibility','visible');
            jQuery.ajax({
               type: "POST",
               url: ajaxurl,
               data: data,
               cache:false,
               datatype: "json",
               success: function(data){
				   jQuery('#mwqs_license_chk_loader').css('visibility','hidden');
                   alert(data);
				   if(data=='License Activated'){
					   location.reload();
				   }                   
               },
			   error: function(data) {
					jQuery('#mwqs_license_chk_loader').css('visibility','hidden');
				    alert('Error');
			   }
             });
			 
        });
	})

})( jQuery );

function mw_qbo_sync_check_all_desk(checkbox,start_with){
	jQuery('input:checkbox').each(function(){		
		if(typeof(jQuery(this).attr('id'))!=='undefined' && jQuery(this).is(":not(:disabled)") && jQuery(this).attr('id').match("^"+start_with)){			
			if(checkbox.checked){				
				jQuery(this).attr('checked','checked');
			}else{
				jQuery(this).removeAttr('checked');
			}
		}		
	});
}

var mwQsPopUpWin_obj_Desk=0;
function popUpWindowDesk(URLStr,popUpWin, left, top, width, height){	
//Fixed Width Height
 width = 750;
 height = 480;

 left = (screen.width/2)-(width/2);
 top = (screen.height/2)-(height/2);
	
  if(mwQsPopUpWin_obj_Desk){
    //if(!mwQsPopUpWin_obj_Desk.closed) mwQsPopUpWin_obj_Desk.close();    
    if(mwQsPopUpWin_obj_Desk.name==popUpWin){
	 alert('Sync status window already opened');
     return false;
     }
  }
 mwQsPopUpWin_obj_Desk = open(URLStr, popUpWin, 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=yes,width='+width+',height='+height+',left='+left+', top='+top+',screenX='+left+',screenY='+top+'');
 return mwQsPopUpWin_obj_Desk;
}
