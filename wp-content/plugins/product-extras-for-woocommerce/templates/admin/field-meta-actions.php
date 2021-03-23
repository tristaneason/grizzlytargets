<?php
/**
 * The markup for group actions
 *
 * @package WooCommerce Product Add-Ons Ultimate
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<div class="pewc-actions pewc-field-actions">
	<span class="sort-handle" title="<?php _e( 'Drag', 'pewc' ); ?>"><span class="dashicons dashicons-menu"></span></span>
	<span class="collapse-field" title="<?php _e( 'Collapse / Expand', 'pewc' ); ?>"><span class="dashicons dashicons-arrow-down"></span><span class="dashicons dashicons-arrow-up"></span></span>
	<span class="duplicate" title="<?php _e( 'Duplicate', 'pewc' ); ?>"><?php _e( 'Copy', 'pewc' ); ?></span>
	<span class="remove-field" title="<?php _e( 'Delete', 'pewc' ); ?>"><?php _e( 'Remove', 'pewc' ); ?></span>
</div>
