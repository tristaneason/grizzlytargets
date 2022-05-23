<?php
/**
 * Unishippers LTL Freightdesk online Template
 *
 * @package     Unishippers LTL Quotes
 * @author      Eniture-Technology
 */
if (!defined('ABSPATH')) {
    exit;
}


require_once('en-fdo.php');
$fdo_obj = new \EnUniShipfreightFdo();

$fdo_coupon_data = $fdo_obj->get_fdo_coupon_data();

?>

<div class="user_guide">
    <h2>Connect to FreightDesk Online.</h2>
    <p>
    FreightDesk Online (
        <a href="https://freightdesk.online/" target="_blank">freightdesk.online</a>
        ) is a cloud-based, multi-carrier shipping platform that allows its users to create and manage postal, parcel, and LTL freight shipments. 
    Connect your store to FreightDesk Online and virtually eliminate the need for data entry when shipping orders. (
        <a href="https://freightdesk.online/" target="_blank">Learn more</a>
        )
    </p>

    <?php

    if(empty($fdo_coupon_data)){
        ?>
            <div id="message" class="en-coupon-code-div woocommerce-message en-coupon-error">
                <p>
                    Sorry! we are unable to get a discounted coupon from freightdesk.online.
                    Please try later.
                </p>
            </div>
        <?php
    }else{
        if(!$fdo_coupon_data['fdo_user']){

            ?>
                <div id="message" class="en-coupon-code-div woocommerce-message en-coupon-notice">
                    <p>
                        <strong>Note!</strong> 
                        To establish a connection, you must have a freightdesk.online account. 
                        If you don’t have one, get freightdesk.online free for one year by using promo code <strong>[<?php echo $fdo_coupon_data['coupon'] ?>]</strong>. 
                        
                    </p>
                    <p>
                        Click <a href="<?php echo $fdo_coupon_data['register_url'] ?>" target="_blank">here</a> to register for freightdesk.online using the promo code now.
                        If you already connected then please click 
                        <a href="javascript:void(0)" onclick="en_unishippers_ltl_fdo_connection_status_refresh(this);">this link</a> 
                        to refresh the status.

                    </p>

                </div>
            <?php

        }else if($fdo_coupon_data['status']){
        ?>
            <div id="message" class="en-coupon-code-div woocommerce-message en-coupon-success">
                <p>
                    <strong>Congratulations!</strong> You have activated your Promo Code <strong>[<?php echo $fdo_coupon_data['coupon'] ?>]</strong><?php echo $fdo_coupon_data['fdo_company_text'] ?>. Now you can enjoy free shipments with FreightDesk Online for one year.
                </p>
            </div>
        <?php

        }else{
            ?>
            <div id="message" class="en-coupon-code-div woocommerce-message en-coupon-notice">
                <p>
                    Note! Get FreightDesk Online free for one year by using promo code <strong>[<?php echo $fdo_coupon_data['coupon'] ?>]</strong>.
                </p>
                <div class='en-coupon-btn-div'>
                    <button class="en_fdo_unishippers_ltl_apply_promo_btn button" data-coupon="<?php echo $fdo_coupon_data['coupon'] ?>"><?php _e( 'Apply Promo Code', 'woocommerce' ); ?></button>
                </div>
            </div>
            
        <?php
        }
        
    }

    ?>
