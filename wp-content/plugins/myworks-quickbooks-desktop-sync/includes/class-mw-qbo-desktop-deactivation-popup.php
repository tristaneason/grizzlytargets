<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


/**
 * WC_Admin_Pointers Class.
 */
class MW_Admin_Deactivation_Popup {

    /**
     * Constructor.
     */
    public function __construct() {
        add_action( 'admin_enqueue_scripts', array( $this, 'setup_popup_scripts' ) );
    }
    
    public function setup_popup_scripts() {

        if ( ! $this->is_plugins_screen() ) {
            return;
        }        

        wp_register_style('mw_wc_qbo_desk_deactivation_popup_css', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.css');
        wp_enqueue_style( 'mw_wc_qbo_desk_deactivation_popup_css' );
        wp_enqueue_script('mw_wc_qbo_desk_deactivation_popup_js', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.js',array('jquery'));

        add_action( 'admin_print_footer_scripts', array( $this,'mw_admin_deactivation_popup_footer') );
    }
    
    public function mw_admin_deactivation_popup_footer() {
        global $mw_wh_license_caps;
        $license_key = isset($mw_wh_license_caps['license_key']) ? $mw_wh_license_caps['license_key'] : '';
        $license_domain = isset($mw_wh_license_caps['license_domain']) ? $mw_wh_license_caps['license_domain'] : '';
        $license_email = isset($mw_wh_license_caps['license_email']) ? $mw_wh_license_caps['license_email'] : get_option( 'admin_email' );
    ?>
<style>
    form#mw-deactivation-form h1{
        color: #c32d1b;
    }
    form#mw-deactivation-form label{
        display: inline-block;
        padding-bottom: 10px;
    }
    form#mw-deactivation-form input[type="text"]{
        margin-bottom: 10px;
        border-radius: 3px;
        border: 1px solid #c32d1b;
        height: 30px;
    }
    form#mw-deactivation-form input[type="submit"]{

    background: #c32d1b;
    border: 0;
    font-size: 16px;
    line-height: 34px;
    padding: 0 30px;
    color: #fff;
    border-radius: 3px;

}
</style>    
<form id="mw-deactivation-form" method="post" action="https://forms.hubspot.com/uploads/form/v2/4333867/14e17026-f443-4d9e-a912-9558746c6634" class="modal">
    <?php
    wp_nonce_field( '_mw_deactivate_feedback_nonce' );
    ?>    
    <h1>Quick Feedback</h1>
    <p>If you have a moment, please share why you are deactivating this plugin:</p>
    <hr>

    <label for="deactivation_reason"><b>Deactivation Reason</b></label><br>
    <input type="radio" name="deactivation_reason" value="I no longer need the sync" required> I no longer need the sync<br>
    <input type="radio" name="deactivation_reason" value="I found a different sync"> I found a different sync<br>
    <input type="radio" name="deactivation_reason" value="I could not get the sync to work"> I couldn't get the sync to work<br>
    <input type="radio" name="deactivation_reason" value="It is a temporary deactivation"> It's a temporary deactivation<br>
    <input type="radio" name="deactivation_reason" value="Other"> Other<br><br>
    
    <input type="hidden" name="deactivation_domain" value="<?php echo $license_domain; ?>">
    
    <input type="hidden" name="deactivation_license_key" value="<?php echo $license_key; ?>">
    
    <input type="hidden" name="email" value="<?php echo $license_email; ?>">

    <input type="hidden" name="action" value="redirect_mw_deactivation_popup" />
    
    <input type="submit" value="Submit & Deactivate">
    <a id="mw-skip-deactivate" href="javascript:void()" style="float: right;">Skip & Deactivate</a>
</form>
<script type="text/javascript">
    /* <![CDATA[ */
    ( function($) {

        var deactivateLink = $('#the-list').find('[data-slug="woocommerce-sync-for-quickbooks-desktop-by-myworks-software"] span.deactivate a');

        $('#mw-skip-deactivate').attr('href',deactivateLink.attr('href'));

        deactivateLink.on('click', function (event) {
            event.preventDefault();

            $('#mw-deactivation-form').modal();
        });        

        $('#mw-deactivation-form').submit(function(e) {
            e.preventDefault(); // don't submit multiple times

            formData = $(this).serialize();

            $.post('<?php echo admin_url( 'admin-ajax.php' ); ?>', formData, function(response) {
                if(response) {
                    $.modal.close();
                    location.href = deactivateLink.attr('href');
                }
            });

        });     
    } )(jQuery);
    /* ]]> */
</script>
    <?php
    }

    /**
     * @since 2.3.0
     * @access private
     */
    private function is_plugins_screen() {
        return in_array( get_current_screen()->id, [ 'plugins', 'plugins-network' ] );
    }

}

new MW_Admin_Deactivation_Popup();