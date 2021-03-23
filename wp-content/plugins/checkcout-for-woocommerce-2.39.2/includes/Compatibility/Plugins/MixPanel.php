<?php

namespace Objectiv\Plugins\Checkout\Compatibility\Plugins;

use Objectiv\Plugins\Checkout\Compatibility\Base;

class MixPanel extends Base {
	public function __construct() {
		parent::__construct();
	}

	public function is_available() {
		return function_exists( 'init_woocommerce_mixpanel' );
	}

	function run() {
		add_action( 'cfw_wp_head', array( $this, 'mixpanel_head' ), 31 ); // 31 is after init_block, which injects header scripts / styles
	}

	function mixpanel_head() {
		$all_integrations = WC()->integrations->get_integrations();
		$WC_Mixpanel      = isset( $all_integrations['mixpanel'] ) ? $all_integrations['mixpanel'] : null;

		if ( $WC_Mixpanel ) {
			$WC_Mixpanel->started_checkout();

			// Payment form
			$this->echo_payment_start_script( $WC_Mixpanel );
		}
	}

	function echo_payment_start_script( $WC_Mixpanel ) {
		ob_start();
		$WC_Mixpanel->started_payment();
		$script = ob_get_clean();
		?>
		<script type="text/javascript">
			jQuery(document).ready(function(){
				jQuery('#<?php echo apply_filters( 'cfw_template_tab_container_el', 'cfw-tab-container' ); ?>').bind('easytabs:after', function() {
					if ( jQuery('#<?php echo apply_filters( 'cfw_template_payment_method_el', 'cfw-payment-method' ); ?>').hasClass('active') ) {
						<?php echo preg_replace( '#<script[^>]*>([^<]+)</script>#', '$1', $script ); ?>
					}
				});
			});
		</script>
		<?php
	}
}
