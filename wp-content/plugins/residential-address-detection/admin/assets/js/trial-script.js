jQuery(document).ready(function () {
    jQuery(".notice-dismiss-residential-php").each(function(i, e) {
        if (i > 0) {
            jQuery(this).remove();
        }
    });
    
     jQuery("#residential-del").on('click', function (){
        var data = {action: 'en_woo_addons_hide_residential_message'};
         jQuery.ajax({
            type: "POST",
            url: ajaxurl,
            data: data,
            success: function (response)
            {
               
                jQuery('.notice-dismiss-residential').remove();
                console.log(response);
            },
            error: function () {
                console.log('error');
            }
        });
    });
});

