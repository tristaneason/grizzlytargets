const ajaxUrl                      = hubwooi18n.ajaxUrl;
const hubwooSecurity               = hubwooi18n.hubwooSecurity;

jQuery( document ).ready(function($){
	let href = '';
	//deactivation screen
 	// Deactivate Modal Open.
	jQuery('#deactivate-makewebbetter-hubspot-for-woocommerce').on('click', function(evt) {
		evt.preventDefault();
		href = jQuery(this).attr( 'href' );
		jQuery('.mwb-g-modal__cover').addClass('show-g_modal_cover');
		jQuery('.mwb-g-modal__message').addClass('show-g_modal_message');
	});

	// Deactivate Modal close.
	jQuery('.mwb-w-modal__cover, .mwb-g-modal__close').on('click', function() {
		jQuery('.mwb-g-modal__cover').removeClass('show-g_modal_cover');
		jQuery('.mwb-g-modal__message').removeClass('show-g_modal_message');

		if ( href.length > 0 ) {
			window.location.replace( href );
		}
	});

	jQuery(document).on(
		'click',
		'.hubwoo-hide-rev-notice',
		async function() {
			const response = await jQuery.ajax(
				{
					type : 'POST',
					url  : ajaxUrl,
					data : {
						action : 'hubwoo_hide_rev_notice',
						hubwooSecurity,
					},
					dataType : 'json',
				}
			);
			
			if( true == response.status ) {
				jQuery('.hubwoo-review-notice-wrapper').hide();
			}
		}
	);
});	