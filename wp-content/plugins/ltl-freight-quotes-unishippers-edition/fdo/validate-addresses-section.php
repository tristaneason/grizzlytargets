<?php
/**
 * Unishippers LTL User Guide Template
 *
 * @package     Unishippers LTL Quotes
 * @author      Eniture-Technology
 */
if (!defined('ABSPATH')) {
    exit;
}


require_once('en-va.php');
$va_obj = new \EnUniShipfreightVa();

$va_coupon_data = $va_obj->get_va_coupon_data();

?>

<div class="user_guide">
    <h2>Connect to Validate Addresses.</h2>
    <p>
    Validate Addresses (
        <a href="https://validate-addresses.com/" target="_blank">validate-addresses.com</a>
        ) is a cloud-based platform that verifies an order’s address details after the order is placed. It is also the most economical way. 
        You won’t be paying to validate an address every time someone enters the checkout process and then abandons the cart. 
        Connect your store to Validate Address and virtually eliminate to avoid spending your time validating addresses. (
        <a href="https://validate-addresses.com/" target="_blank">Learn more</a>
        )
    </p>

    <?php

    if(empty($va_coupon_data)){
        ?>
            <div id="message" class="en-coupon-code-div woocommerce-message en-coupon-error">
                <p>
                    Sorry! we are unable to get a discounted coupon from validate-addresses.com.
                    Please try later.
                </p>
            </div>
        <?php
    }else{
        if(!$va_coupon_data['va_user']){

            ?>
                <div id="message" class="en-coupon-code-div woocommerce-message en-coupon-notice">
                    <p>
                        <strong>Note!</strong> 
                        To establish a connection, you must have a validate addresses account. 
                        If you don’t have one, get validate addresses free for one year by using promo code <strong>[<?php echo $va_coupon_data['coupon'] ?>]</strong>. 
                        
                    </p>
                    <p>
                        Click <a href="<?php echo $va_coupon_data['register_url'] ?>" target="_blank">here</a> to register for validate addresses using the promo code now.
                        If you already connected then please click 
                        <a href="javascript:void(0)" onclick="en_unishippers_ltl_va_connection_status_refresh(this);">this link</a> 
                        to refresh the status.

                    </p>
                </div>
            <?php

        }else if($va_coupon_data['status']){

        ?>
            <div id="message" class="en-coupon-code-div woocommerce-message en-coupon-success">
                <p>
                    <strong>Congratulations!</strong> You have activated your Promo Code <strong>[<?php echo $va_coupon_data['coupon'] ?>]</strong><?php echo $va_coupon_data['va_company_text'] ?>. Now you can enjoy validate addresses free for one year.
                    
                </p>
            </div>
        <?php

        }else{
            ?>
            <div id="message" class="en-coupon-code-div woocommerce-message en-coupon-notice">
                <p>
                    <strong>Note!</strong> 
                    Get validate addresses free for one year by using promo code <strong>[<?php echo $va_coupon_data['coupon'] ?>]</strong>.
                </p>
                <div class='en-coupon-btn-div'>
                    <button class="en_va_unishippers_ltl_apply_promo_btn button" data-coupon="<?php echo $va_coupon_data['coupon'] ?>"><?php _e( 'Apply Promo Code', 'woocommerce' ); ?></button>
                </div>
            </div>
        <?php
        }
        
    }

    ?>
