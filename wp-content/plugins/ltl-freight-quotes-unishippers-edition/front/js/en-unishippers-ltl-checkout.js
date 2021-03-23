jQuery(function() {
    if(typeof trigger == "undefined"){
        var trigger = false;
        jQuery('#billing_address_1').addClass('billing_address1');
        jQuery('#billing_address_2').addClass('billing_address_2');
        jQuery('#shipping_address_1').addClass('shipping_address_1');
        jQuery('#shipping_address_2').addClass('shipping_address_2');
        jQuery('#shipping_city').addClass('shipping_city');
        jQuery('#billing_city').addClass('billing_city');
        jQuery('#billing_postcode').addClass('billing_postcode');
        jQuery('#shipping_postcode').addClass('shipping_postcode');

        jQuery('.billing_address1 , .billing_address_2 , \n\
                .shipping_address_1 , .shipping_address_2 , \n\
                .shipping_city , .billing_city , .billing_postcode , \n\
                .shipping_postcode').on('keydown' , function(event){
            if(trigger == false){
                event.stopImmediatePropagation();
            }
            trigger = false; 
        });

        jQuery('.billing_address1 , .billing_address_2 , \n\
                .shipping_address_1 , .shipping_address_2 , \n\
                .shipping_city , .billing_city , .billing_postcode , \n\
                .shipping_postcode').on('change' , function(event){
            trigger = true; 
            jQuery('.billing_address1').trigger('keydown');

        });
    }
});