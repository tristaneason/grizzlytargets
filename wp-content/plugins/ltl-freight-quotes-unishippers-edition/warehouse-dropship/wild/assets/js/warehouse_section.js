    jQuery(document).ready(function() {
        
        jQuery("._hazardousmaterials").closest('p').addClass('_hazardousmaterials');
        jQuery("._en_insurance_fee").closest('p').addClass('_en_insurance_fee');
        
        jQuery(".disabled_me").on('click' , function(){
            return false;
        });
        
        jQuery("#instore-pickup-address , #local-delivery-address , #local-delivery-fee").keydown(function (e) {
            // Allow: backspace, delete, tab, escape, enter and .
            if (jQuery.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
                 // Allow: Ctrl+A, Command+A
                (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) || 
                 // Allow: home, end, left, right, down, up
                (e.keyCode >= 35 && e.keyCode <= 40)) {
                     // let it happen, don't do anything
                     return;
            }
            // Ensure that it is a number and stop the keypress
            if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                e.preventDefault();
            }
        });
    });

    /**
     * Round values
     * @param {type} el
     * @returns {undefined}
     */
    if(typeof validate_delivery_fee != 'function')
    {
        function validate_delivery_fee(el)
        { 
            var v = parseFloat(el.value); 
            el.value = (isNaN(v)) ? '' : v.toFixed(2); 
        }
    }
    
    /**
     * Round values
     * @param {type} el
     * @returns {undefined}
     */
    if(typeof get_parameter_by_name != 'function')
    {
        function get_parameter_by_name(name) {
            name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
            var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
                results = regex.exec(location.search);
            return results == null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
        }
    }
    
    /**
    * Validate Input String 
    */
    if(typeof validateString != 'function')
    {
        function validateString( string )
        {
           if( string == '' )
               return 'empty';
           else
               return true;

        }
    }

    /**
     * Eniture Validation Form JS
     */
    if(typeof enWdValidateInput != 'function')
    {
        function enWdValidateInput( form_id )
        {   
            var has_err = true;
            jQuery( form_id+" input[type='text']" ).each( function(){
                var input      = jQuery( this ).val(); 
                var response   = validateString( input );
                var errorText  = jQuery( this ).attr( 'title' );
                var optional   = jQuery( this ).data( 'optional' );

                var errorElement = jQuery( this ).parent().find( '.en_wd_err' );
                jQuery(errorElement).html( '' );

                optional       = ( optional === undefined ) ? 0 : 1;
                errorText      = ( errorText != undefined ) ? errorText : '';

                if( ( optional == 0 ) && ( response  == false ||  response  == 'empty' ) ) 
                {
                    errorText   = ( response  == 'empty' ) ? errorText+' is required.':'Invalid input.';
                    jQuery( errorElement ).html( errorText ); 
                }
                has_err = ( response != true && optional == 0 )? false : has_err; 
            });
            return has_err;
        }
    }
    
    /**
     * 
     * @param {type} evt
     * @return {Boolean}
     */
    if(typeof instorePickupDeliveryValidateNum != 'function')
    {
        function instorePickupDeliveryValidateNum(e)
        {
            var val = this.value;
            var re = /^([0-9]+[\.]?[0-9]?[0-9]?|[0-9]+)$/g;
            var re1 = /^([0-9]+[\.]?[0-9]?[0-9]?|[0-9]+)/g;
            if (re.test(val)) {
                //do something here

            } else {
                val = re1.exec(val);
                if (val) {
                    this.value = val[0];
                } else {
                    this.value = "";
                }
            }
        }
    }
    
    /**
     * Check input field length
     * @param {type} len
     * @param {type} ele
     * @return {Boolean}
     */
    if(typeof checkLength != 'function')
    {
        function checkLength(len,ele)
        {
            var fieldLength = ele.value.length;
            if(fieldLength <= len){
              return true;
            }
            else
            {
              var str = ele.value;
              str = str.substring(0, str.length - 1);
              ele.value = str;
            }
        }
    }
        
        
    /**
     * Will remove all falsy values: undefined, null, 0, false, NaN and "" (empty string)
     * @param {type} actual
     * @return {Array|cleanArray.newArray}
     */
    if(typeof cleanArray != 'function')
    {
        function cleanArray(actual) 
        {
            var newArray = new Array();
            for (var i = 0; i < actual.length; i++) {
                if (actual[i]) {
                  newArray.push(actual[i].replace('×', ''));
                }
            }
            return newArray;
        }
    }
    
    if(typeof reset_warehouse_popup_form != 'function')
    {
        function reset_warehouse_popup_form()
        {
            jQuery('#add_warehouses').find("input[type='text']").val("");
            jQuery('#add_warehouses').find("input[type='checkbox']").prop('checked',false);
            jQuery('#instore-pickup-zipmatch .tag-i, #local-delivery-zipmatch .tag-i').trigger('click');
        }
    }
    
    /**
     * get tags input zip codes
     * @return {String|instorePickupLocalDeliveryZipmatch.zipmatch}
     */    
    if(typeof instorePickupLocalDeliveryZipmatch != 'function')
    {
        function instorePickupLocalDeliveryZipmatch(zipId)
        {
            var allZipString = jQuery(zipId).children().text();
            var allZipArray = allZipString.split(" ");
            var newZipArr = cleanArray(allZipArray); 
            var zips = newZipArr.toString();
            return zips;
        }
    }

    /**
     * instore pickup input val
     * @return {instorePickupInputVal.instorePickup}
     */  
    if(typeof instorePickupInputVal != 'function')
    {
        function instorePickupInputVal(formID)
        {
            var deliveryZipId = formID + ' #instore-pickup-zipmatch';
            var pickupZipmatch = instorePickupLocalDeliveryZipmatch(deliveryZipId);
            var instorePickup = {
                'enable_instore'        : jQuery(formID +' #enable-instore-pickup').is(':checked'),
                'address_miles_instore' : jQuery(formID +' #instore-pickup-address').val(),
                'zipmatch_instore'      : pickupZipmatch,
                'desc_instore'          : jQuery(formID +' #instore-pickup-desc').val()
            };

            return instorePickup;
        }
    }
        
    /**
     * local delivery input val
     * @return {instorePickupInputVal.instorePickup}
     */    
    if(typeof localDeliveryInputVal != 'function')
    {
        function localDeliveryInputVal(formID)
        {
            var deliveyZipId = formID +' #local-delivery-zipmatch';
            var deliveryZipMatch = instorePickupLocalDeliveryZipmatch(deliveyZipId);
            var localDelivery = {
                'enable_delivery'        : jQuery(formID +' #enable-local-delivery').is(':checked'),
                'address_miles_delivery' : jQuery(formID +' #local-delivery-address').val(),
                'zipmatch_delivery'      : deliveryZipMatch,
                'desc_delivery'          : jQuery(formID +' #local-delivery-desc').val(),
                'fee_delivery'           : jQuery(formID +' #local-delivery-fee').val(),
                'supppress_delivery'     : jQuery(formID +' #suppress-local-delivery').is(':checked')
            };

            return localDelivery;
        }
    }
       
    
    /**
     * merge all objects of warehouse
     * @param {type} arrObj
     * @return {unresolved}
     */    
    if(typeof mergeWarehouseSectionObjects != 'function')
    {
        function mergeWarehouseSectionObjects(arrObj)
        {
            var resultObject = arrObj.reduce(function(result, currentObject) {
                for(var key in currentObject) {
                    if (currentObject.hasOwnProperty(key)) {
                        result[key] = currentObject[key];
                    }
                }
                return result;
            }, {});

            return resultObject;
        }
    }
        
    /**
     * 
     * @param {type} data
     * @return {undefined}
     */    
   if(typeof load_inside_pikup_and_local_delivery_data != 'function')
    {
        function load_inside_pikup_and_local_delivery_data(data, formID)
        {
            jQuery(''+formID +' #instore-pickup-zipmatch .tag-i, '+formID +' #local-delivery-zipmatch .tag-i').trigger('click');

            ( data[0].enable_store_pickup == 1 ) ? jQuery( formID +' #enable-instore-pickup' ).prop('checked', true):'';
            ( data[0].enable_local_delivery == 1 ) ? jQuery( formID +' #enable-local-delivery' ).prop('checked', true):'';
            ( data[0].suppress_local_delivery == 1 )? jQuery( formID +' #suppress-local-delivery' ).prop('checked', true):'';

            jQuery( formID +' #instore-pickup-desc' ).val( data[0].checkout_desc_store_pickup );
            jQuery( formID +' #local-delivery-desc' ).val( data[0].checkout_desc_local_delivery );
            jQuery( formID +' #local-delivery-fee' ).val( data[0].fee_local_delivery );

            var storePickupZipArray = data[0].match_postal_store_pickup.split(',');

            jQuery.each(storePickupZipArray , function(index , value){
            jQuery(formID +" #instore-pickup-zipmatch .type-zone").val(value).focusout();
            });

            var localDeliveryZipArray = data[0].match_postal_local_delivery.split(',');
            jQuery.each(localDeliveryZipArray , function(index , value){
            jQuery(formID +" #local-delivery-zipmatch .type-zone").val(value).focusout();
            });

            jQuery( formID +' #instore-pickup-address' ).val( data[0].miles_store_pickup );
            jQuery( formID +' #local-delivery-address' ).val( data[0].miles_local_delivery );

        }
    }
    
    if(typeof en_wd_check_postal_length != 'function')
    {
        function en_wd_check_postal_length(html_id)
        {
            return jQuery(html_id).children().text().length == 0 ? true : false;
        }
    }
    

    /**
     * Warehouse Section Script Start
     * @returns {Boolean}
     */
    if(typeof en_wd_save_warehouse != 'function')
    {
        function en_wd_save_warehouse()
        {
            var validate = enWdValidateInput("#add_warehouses");
            
            var enable_local_delivery = jQuery('#add_warehouses #enable-local-delivery').is(':checked');
            var enable_instore_pickup = jQuery('#add_warehouses #enable-instore-pickup').is(':checked');

            switch(true)
            {
                case (validate == false):
                    jQuery( '.content' ).delay( 200 ).animate({scrollTop: 0 }, 300);
                    return false;
                
                case (enable_instore_pickup && 
                            ( jQuery("#add_warehouses #instore-pickup-address").val().length == 0 && 
                            en_wd_check_postal_length('#add_warehouses #instore-pickup-zipmatch') )):
                    jQuery('.wrng_instore').show('slow');
                    jQuery( '.content' ).delay( 200 ).animate({scrollTop: 0 }, 300);
                    setTimeout(function () {
                        jQuery('.wrng_instore').hide('slow');
                    }, 5000);
                    return false;
                    
                case (enable_local_delivery && 
                            ( jQuery("#add_warehouses #local-delivery-address").val().length == 0 && 
                            en_wd_check_postal_length('#add_warehouses #local-delivery-zipmatch') )):
                    jQuery('.wrng_local').show('slow');
                    jQuery( '.content' ).delay( 200 ).animate({scrollTop: 0 }, 300);
                    setTimeout(function () {
                        jQuery('.wrng_local').hide('slow');
                    }, 5000);
                    return false;
                    
                case (enable_local_delivery && jQuery("#add_warehouses #local-delivery-fee").val().length <= 0):
                    jQuery("#add_warehouses #local-delivery-fee").parent().find(".en_wd_err").html("Local delivery fee is required.");
                    return false;
            }
            

            var city = jQuery('#en_wd_origin_city').val();
            var tab = get_parameter_by_name('tab');
            
            var postForm = {
                'action': 'en_wd_save_warehouse',
                'origin_id': jQuery('#edit_form_id').val(),
                'origin_city': city,
                'origin_state': jQuery('#en_wd_origin_state').val(),
                'origin_zip': jQuery('#en_wd_origin_zip').val(),
                'origin_country': jQuery('#en_wd_origin_country').val(),
                'location': jQuery('#en_wd_location').val(),
                'tab': tab,
            };

            var form_id = "#add_warehouses";
            var arrObj = [postForm, instorePickupInputVal(form_id), localDeliveryInputVal(form_id)];
            var whData = mergeWarehouseSectionObjects(arrObj);
            postForm = whData;

            jQuery.ajax({
                type: 'POST',
                url: ajaxurl,
                data: postForm,
                dataType: 'json',
                beforeSend: function(){
                    jQuery('.save_warehouse_form').addClass('spinner_disable').val("Loading ..");
                },
                success: function (data)
                {
                    jQuery('.save_warehouse_form').removeClass('spinner_disable').val("Save");
                    var WarehpuseDataId = data.id;
                    if (data.insert_qry == 1) {
                        jQuery(".add_btn_warehouse").html(data.html);
                        jQuery('.warehouse_created').show('slow').delay(5000).hide('slow');
                        jQuery('.warehouse_updated').css('display', 'none');
                        jQuery('.warehouse_deleted').css('display', 'none');
                        jQuery('.dropship_deleted').css('display', 'none');
                        jQuery('.dropship_updated').css('display', 'none');
                        jQuery('.dropship_created').css('display', 'none');
                        window.location.href = jQuery('.close').attr('href');
                    } else if (data.update_qry == 1) {
                        jQuery('.warehouse_updated').show('slow').delay(5000).hide('slow');
                        jQuery('.warehouse_created').css('display', 'none');
                        jQuery('.warehouse_deleted').css('display', 'none');
                        jQuery('.dropship_deleted').css('display', 'none');
                        jQuery('.dropship_updated').css('display', 'none');
                        jQuery('.dropship_created').css('display', 'none');
                        window.location.href = jQuery('.close').attr('href');
                        jQuery('tr[id=row_' + WarehpuseDataId + ']').html('<td class="en_wd_warehouse_list_data">' + data.origin_city + '</td><td class="en_wd_warehouse_list_data">' + data.origin_state + '</td><td class="en_wd_warehouse_list_data">' + data.origin_zip + '</td><td class="en_wd_warehouse_list_data">' + data.origin_country + '</td><td class="en_wd_warehouse_list_data"><a href="javascript(0)" title="Edit" onclick="return en_wd_edit_warehouse(' + WarehpuseDataId + ')"><img src="' + script.pluginsUrl + '/ltl-freight-quotes-unishippers-edition/warehouse-dropship/wild/assets/images/edit.png"></a><a href="javascript(0)" title="Delete" onclick="return en_wd_delete_current_warehouse(' + WarehpuseDataId + ');"><img src="' + script.pluginsUrl + '/ltl-freight-quotes-unishippers-edition/warehouse-dropship/wild/assets/images/delete.png"></a></td>');
                    }
                    else {
                        jQuery('.already_exist').show('slow');
                        jQuery( '.content' ).delay( 200 ).animate({scrollTop: 0 }, 300);
                        setTimeout(function () {
                            jQuery('.already_exist').hide('slow');
                        }, 5000);
                    }
                },
            });
            return false;
        }
    }

    /**
     * Delete Warehouse
     * @param e
     * @returns {Boolean}
     */
    if(typeof en_wd_delete_current_warehouse != 'function')
    {
        function en_wd_delete_current_warehouse(e)
        {
            var tab = get_parameter_by_name('tab');
            var postForm = {
                'action': 'en_wd_delete_warehouse',
                'delete_id': e,
                'tab': tab,
            };
            jQuery.ajax({
                type: 'POST',
                url: ajaxurl,
                data: postForm,
                dataType: 'json',
                success: function (data)
                {
                    if (data.length > 1) {
                        jQuery(".add_btn_warehouse").html(data);
                        jQuery('#row_' + e).remove();
                        jQuery('.warehouse_deleted').show('slow').delay(5000).hide('slow');
                        jQuery('.warehouse_updated').css('display', 'none');
                        jQuery('.warehouse_created').css('display', 'none');
                        jQuery('.dropship_deleted').css('display', 'none');
                        jQuery('.dropship_updated').css('display', 'none');
                        jQuery('.dropship_created').css('display', 'none');
                    }
                },
            });
            return false;
        }
    }

    /**
     * Edit Warehouse
     * @param e
     * @returns {Boolean}
     */
    if(typeof en_wd_edit_warehouse != 'function')
    {
        function en_wd_edit_warehouse(e)
        {
            var postForm = {
                'action': 'en_wd_edit_warehouse',
                'edit_id': e,
            };
            jQuery.ajax({
                type: 'POST',
                url: ajaxurl,
                data: postForm,
                dataType: 'json',
                success: function (data)
                {
                    if (data[0]) {
                        jQuery('#add_warehouses')[0].reset();
                        jQuery('#edit_form_id').val(data[0].id);
                        jQuery('#en_wd_origin_zip').val(data[0].zip);
                        jQuery('.city_select').hide();
                        jQuery('#en_wd_origin_city').val(data[0].city);
                        jQuery('.city_input').show();
                        jQuery('#en_wd_origin_city').css('background', 'none');
                        jQuery('#en_wd_origin_state').val(data[0].state);
                        jQuery('#en_wd_origin_country').val(data[0].country);
                        jQuery('.en_wd_zip_validation_err').hide();
                        jQuery('.en_wd_city_validation_err').hide();
                        jQuery('.en_wd_state_validation_err').hide();
                        jQuery('.en_wd_country_validation_err').hide();
                        window.location.href = jQuery('.en_wd_add_warehouse_btn').attr('href');
                        jQuery('.already_exist').hide();
                        jQuery(".en_wd_err").html("");
                        setTimeout(function () {
                            if (jQuery('.en_wd_add_warehouse_popup').is(':visible')) {
                                jQuery('.en_wd_add_warehouse_input > input').eq(0).focus();
                            }
                        }, 100);

                        load_inside_pikup_and_local_delivery_data(data , "#add_warehouses");
                    }
                },
            });
            return false;
        }
    }

    /**
     * Dropship Section Script Start
     * @returns {Boolean}
     */
    if(typeof en_wd_save_dropship != 'function')
    {
        function en_wd_save_dropship()
        {

            var validate = enWdValidateInput("#add_dropships");
            
            var enable_local_delivery = jQuery('#add_dropships  #enable-local-delivery').is(':checked');
            var enable_instore_pickup = jQuery('#add_dropships  #enable-instore-pickup').is(':checked');

            switch(true)
            {
                case (validate == false):
                    jQuery( '.content' ).delay( 200 ).animate({scrollTop: 0 }, 300);
                    return false;
                
                case (enable_instore_pickup && 
                            ( jQuery("#add_dropships  #instore-pickup-address").val().length == 0 && 
                            en_wd_check_postal_length('#add_dropships  #instore-pickup-zipmatch') )):
                    jQuery('.wrng_instore').show('slow');
                    jQuery( '.content' ).delay( 200 ).animate({scrollTop: 0 }, 300);
                    setTimeout(function () {
                        jQuery('.wrng_instore').hide('slow');
                    }, 5000);
                    return false;
                    
                case (enable_local_delivery && 
                            ( jQuery("#add_dropships  #local-delivery-address").val().length == 0 && 
                            en_wd_check_postal_length('#add_dropships  #local-delivery-zipmatch') )):
                    jQuery('.wrng_local').show('slow');
                    jQuery( '.content' ).delay( 200 ).animate({scrollTop: 0 }, 300);
                    setTimeout(function () {
                        jQuery('.wrng_local').hide('slow');
                    }, 5000);
                    return false;
                    
                case (enable_local_delivery && jQuery("#add_dropships #local-delivery-fee").val().length <= 0):
                    jQuery("#add_dropships #local-delivery-fee").parent().find(".en_wd_err").html("Local delivery fee is required.");
                    return false;
            }

            var city = jQuery('#en_wd_dropship_city').val();
            var tab = get_parameter_by_name('tab');
            
            var postForm = {
                'action': 'en_wd_save_dropship',
                'dropship_id': jQuery('#edit_dropship_form_id').val(),
                'dropship_city': city,
                'nickname': jQuery('#en_wd_dropship_nickname').val(),
                'dropship_state': jQuery('#en_wd_dropship_state').val(),
                'dropship_zip': jQuery('#en_wd_dropship_zip').val(),
                'dropship_country': jQuery('#en_wd_dropship_country').val(),
                'location': jQuery('#en_wd_dropship_location').val(),
                'tab': tab,
            };

            var form_id = "#add_dropships";

            var arrObj = [postForm, instorePickupInputVal(form_id), localDeliveryInputVal(form_id)];
            var whData = mergeWarehouseSectionObjects(arrObj);

            postForm = whData;

            jQuery.ajax({
                type: 'POST',
                url: ajaxurl,
                data: postForm,
                dataType: 'json',
                beforeSend: function(){
                    jQuery('.save_warehouse_form').addClass('spinner_disable').val("Loading ..");
                },
                success: function (data)
                {
                    jQuery('.save_warehouse_form').removeClass('spinner_disable').val("Save");
                    var WarehpuseDataId = data.id;
                    if (data.insert_qry == 1) {
                        jQuery(".add_btn_dropship").html(data.html);
                        jQuery('.dropship_created').show('slow').delay(5000).hide('slow');
                        jQuery('.dropship_updated').css('display', 'none');
                        jQuery('.dropship_deleted').css('display', 'none');
                        jQuery('.warehouse_deleted').css('display', 'none');
                        jQuery('.warehouse_updated').css('display', 'none');
                        jQuery('.warehouse_created').css('display', 'none');
                        window.location.href = jQuery('.close').attr('href');
                    } else if (data.update_qry == 1) {
                        jQuery('.dropship_updated').show('slow').delay(5000).hide('slow');
                        jQuery('.dropship_created').css('display', 'none');
                        jQuery('.dropship_deleted').css('display', 'none');
                        jQuery('.warehouse_deleted').css('display', 'none');
                        jQuery('.warehouse_updated').css('display', 'none');
                        jQuery('.warehouse_created').css('display', 'none');
                        window.location.href = jQuery('.close').attr('href');
                        jQuery('tr[id=row_' + WarehpuseDataId + ']').html('<td class="en_wd_dropship_list_data">' + data.nickname + '</td><td class="en_wd_dropship_list_data">' + data.origin_city + '</td><td class="en_wd_dropship_list_data">' + data.origin_state + '</td><td class="en_wd_dropship_list_data">' + data.origin_zip + '</td><td class="en_wd_dropship_list_data">' + data.origin_country + '</td><td class="en_wd_dropship_list_data"><a href="javascript(0)" title="Edit" onclick="return en_wd_edit_dropship(' + WarehpuseDataId + ')"><img src="' + script.pluginsUrl + '/ltl-freight-quotes-unishippers-edition/warehouse-dropship/wild/assets/images/edit.png"></a><a href="javascript(0)" title="Delete" onclick="return en_wd_delete_current_dropship(' + WarehpuseDataId + ');"><img src="' + script.pluginsUrl + '/ltl-freight-quotes-unishippers-edition/warehouse-dropship/wild/assets/images/delete.png"></a></td>');
                    }
                    else {
                        jQuery('.already_exist').show('slow');
                        jQuery( '.content' ).delay( 200 ).animate({scrollTop: 0 }, 300);
                        setTimeout(function () {
                            jQuery('.already_exist').hide('slow');
                        }, 5000);
                    }
                },
            });
            return false;
        }
    }

    /**
     * Edit Dropship
     * @param e
     * @returns {Boolean}
     */
    if(typeof en_wd_edit_dropship != 'function')
    {
        function en_wd_edit_dropship(e)
        {
            var postForm = {
                'action': 'en_wd_edit_dropship',
                'dropship_edit_id': e,
            };
            jQuery.ajax({
                type: 'POST',
                url: ajaxurl,
                data: postForm,
                dataType: 'json',
                success: function (data)
                {
                    if (data[0]) {
                        jQuery('#add_dropships')[0].reset();
                        jQuery('#edit_dropship_form_id').val(data[0].id);
                        jQuery('#en_wd_dropship_zip').val(data[0].zip);
                        jQuery('.city_select').hide();
                        jQuery('#en_wd_dropship_nickname').val(data[0].nickname);
                        jQuery('#en_wd_dropship_city').val(data[0].city);
                        jQuery('.city_input').show();
                        jQuery('#en_wd_dropship_city').css('background', 'none');
                        jQuery('#en_wd_dropship_state').val(data[0].state);
                        jQuery('#en_wd_dropship_country').val(data[0].country);
                        jQuery('.en_wd_zip_validation_err').hide();
                        jQuery('.en_wd_city_validation_err').hide();
                        jQuery('.en_wd_state_validation_err').hide();
                        jQuery('.en_wd_country_validation_err').hide();
                        window.location.href = jQuery('.en_wd_add_dropship_btn').attr('href');
                        jQuery('.already_exist').hide();
                        jQuery(".en_wd_err").html("");
                        setTimeout(function () {
                            if (jQuery('.ds_popup').is(':visible')) {
                                jQuery('.ds_input > input').eq(0).focus();
                            }
                        }, 100);

                        load_inside_pikup_and_local_delivery_data(data , "#add_dropships");
                    }
                },
            });
            return false;
        }
    }

    /**
     * Delete Dropship Popup
     * @param e
     * @returns {Boolean}
     */
    if(typeof en_wd_delete_current_dropship != 'function')
    {
        function en_wd_delete_current_dropship(e)
        {
            var id = e;
            window.location.href = jQuery('.delete_dropship_btn').attr('href');
            jQuery('.cancel_delete').on('click', function () {
                window.location.href = jQuery('.cancel_delete').attr('href');
            });
            jQuery('.confirm_delete').on('click', function () {
                window.location.href = jQuery('.confirm_delete').attr('href');
                return en_wd_confirm_delete_dropship(id);
            });
            return false;
        }
    }

    /**
     * Delete Dropship
     * @param e
     * @returns {Boolean}
     */
    if(typeof en_wd_confirm_delete_dropship != 'function')
    {
        function en_wd_confirm_delete_dropship(e)
        {
            var tab = get_parameter_by_name('tab');
            var postForm = {
                'action': 'en_wd_delete_dropship',
                'dropship_delete_id': e,
                'tab': tab,
            };
            jQuery.ajax({
                type: 'POST',
                url: ajaxurl,
                data: postForm,
                dataType: 'json',
                beforeSend: function(){
                    jQuery('.confirm_delete').addClass('spinner_disable').text("Loading ..");
                },
                success: function (data)
                {
                    jQuery('.confirm_delete').removeClass('spinner_disable').text("OK");
                    if (data.length > 1) {
                        jQuery(".add_btn_dropship").html(data);
                        jQuery('#row_' + e).remove();
                        jQuery('.dropship_deleted').show('slow').delay(5000).hide('slow');
                        jQuery('.dropship_updated').css('display', 'none');
                        jQuery('.dropship_created').css('display', 'none');
                        jQuery('.warehouse_deleted').css('display', 'none');
                        jQuery('.warehouse_updated').css('display', 'none');
                        jQuery('.warehouse_created').css('display', 'none');
                    }
                },
            });
            return false;
        }
    }