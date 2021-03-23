<?php

namespace Objectiv\Plugins\Checkout\Compatibility\Themes;

use Objectiv\Plugins\Checkout\Compatibility\Base;

class TMOrganik extends Base {
	public function is_available() {
		return class_exists( '\\Insight_Functions' );
	}

	public function run() {
		add_action( 'wp_head', array( $this, 'shim_headroom' ) );
	}

	public function shim_headroom() {
		?>
		<script type="text/javascript">
			jQuery(document).ready( function() {
				jQuery.fn.headroom = function () {};
			} );
		</script>
		<?php
	}
}
