jQuery(document).ready(function(){
	
// Advance settings tab
	jQuery('.wf_general_advance_tab').next('table').hide();
	jQuery('.wf_general_advance_tab').click(function(event){
		event.stopImmediatePropagation();
		jQuery('.wf_general_advance_tab').toggleClass('wf_general_advance_tab_click');
		jQuery(this).next('table').toggle();
	});
});