<?php

namespace Objectiv\Plugins\Checkout\Compatibility\Plugins;

use Objectiv\Plugins\Checkout\Compatibility\Base;

class MailChimpforWooCommerce extends Base {
	function is_available() {
		return class_exists( '\\MailChimp_Newsletter' );
	}

	function run() {
		$this->replace_mailchimp_checkbox();
	}

	function replace_mailchimp_checkbox() {
		global $wp_filter;

		$service = new \MailChimp_Newsletter();

		// adding the ability to render the checkbox on another screen of the checkout page.
		$render_on = $service->getOption( 'mailchimp_checkbox_action', 'woocommerce_after_checkout_billing_form' );

		if ( stripos( $render_on, 'cfw_' ) === false ) {
			return;
		}

		$existing_hooks = $wp_filter[ $render_on ];

		if ( $existing_hooks[10] ) {
			foreach ( $existing_hooks[10] as $key => $callback ) {
				if ( false !== stripos( $key, 'applyNewsletterField' ) ) {
					global $MailChimp_Newsletter;

					$MailChimp_Newsletter = $callback['function'][0];
				}
			}
		}

		if ( ! empty( $MailChimp_Newsletter ) ) {
			remove_action( $render_on, array($MailChimp_Newsletter, 'applyNewsletterField'), 10 );

			add_action( $render_on, array($this, 'add_checkbox') );
		}
	}

	function add_checkbox() {
		global $MailChimp_Newsletter;
		?>
		<div id="mailchimp_for_woocommerce">
			<?php echo $MailChimp_Newsletter->applyNewsletterField( null ); ?>
		</div>
		<?php
	}
}