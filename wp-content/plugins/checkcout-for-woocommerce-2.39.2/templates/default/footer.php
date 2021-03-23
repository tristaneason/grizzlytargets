<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
?>
<footer id="cfw-footer">
    <div class="wrap">
        <div class="cfw-container cfw-column-12">
            <div class="cfw-footer-inner entry-footer">
	            <?php do_action( 'cfw_before_footer' ); ?>
                <?php if ( ! empty( $footer_text = Objectiv\Plugins\Checkout\Main::instance()->get_settings_manager()->get_setting('footer_text') ) ): ?>
                    <?php echo do_shortcode( $footer_text ); ?>
                <?php else: ?>
                    Copyright &copy; <?php echo date("Y"); ?>, <?php echo get_bloginfo('name'); ?>. All rights reserved.
                <?php endif; ?>
	            <?php do_action( 'cfw_after_footer' ); ?>
            </div>
        </div>
    </div>
</footer>