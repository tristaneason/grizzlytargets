<?php

 $addon = isset($_REQUEST['addon']) ? sanitize_text_field($_REQUEST['addon']) : '';
 $auto_res_status = isset($_REQUEST['autoRes_status']) ? sanitize_text_field($_REQUEST['autoRes_status']) : '';
 $auto_res_message = isset($_REQUEST['autoRes_message']) ? sanitize_text_field($_REQUEST['autoRes_message']) : '';
 
 /* For Standard Box Sizes */
 if($addon == 'autoResidential') {
     if($auto_res_message != '') {
        update_option('en_residential_message', $auto_res_message);
        update_option('en_residential_message_status', $auto_res_status);
     }
 }