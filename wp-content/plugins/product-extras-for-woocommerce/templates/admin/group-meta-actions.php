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

<div class="pewc-actions pewc-group-meta-actions">
	<span class="table-panel collapse" title="<?php _e( 'Collapse / Expand', 'pewc' ); ?>"><span class="collapse-text"><?php _e( 'Collapse', 'pewc' ); ?></span><span class="expand-text"><?php _e( 'Expand', 'pewc' ); ?></span></span>
	<span class="table-panel duplicate" title="<?php _e( 'Duplicate', 'pewc' ); ?>"><?php _e( 'Copy', 'pewc' ); ?></span>
	<span class="table-panel remove" title="<?php _e( 'Delete', 'pewc' ); ?>"><?php _e( 'Remove', 'pewc' ); ?></span>
</div>
