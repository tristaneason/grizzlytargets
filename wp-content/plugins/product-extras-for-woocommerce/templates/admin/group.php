<?php
/**
 * The markup for a group
 *
 * @package WooCommerce Product Add-Ons Ultimate
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<div class="pewc-group-meta-table wc-metabox" data-group-id="<?php echo $group_id; ?>">
	<div class="form-row">
		<div class="product-extra-field-third">
			<label>
				<?php _e( 'Group Title', 'pewc' ); ?>
				<?php echo wc_help_tip( 'Enter a title for this group that will be displayed on the product page. Leave blank if you wish.', 'pewc' ); ?>
			</label>
		</div>
		<div class="product-extra-field-two-thirds-right">
			<input type="text" class="pewc-group-title" name="_product_extra_groups_<?php echo $group_id; ?>[meta][group_title]" value="<?php echo stripslashes( $group_title ); ?>">
		</div>
	</div>
	<div class="form-row">
		<div class="product-extra-field-third pewc-description">
			<?php $description = pewc_get_group_description( $group_id, $group, $has_migrated ); ?>
			<label>
				<?php _e( 'Group Description', 'pewc' ); ?>
				<?php echo wc_help_tip( 'An optional description for the group', 'pewc' ); ?>
			</label>
		</div>
		<div class="product-extra-field-two-thirds-right">
			<textarea class="pewc-group-description" name="_product_extra_groups_<?php echo $group_id; ?>[meta][group_description]"><?php echo esc_html( $description ); ?></textarea>
		</div>
	</div>
	<div class="form-row">
		<div class="product-extra-field-third">
			<?php $group_layout = pewc_get_group_layout( $group_id ); ?>
			<label>
				<?php _e( 'Group Layout', 'pewc' ); ?>
				<?php echo wc_help_tip( 'Choose how to display the fields in this group.', 'pewc' ); ?>
			</label>
		</div>
		<div class="product-extra-field-two-thirds-right">
			<select class="pewc-group-layout" name="_product_extra_groups_<?php echo $group_id; ?>[meta][group_layout]">
				<option <?php selected( $group_layout, 'ul', true ); ?> value="ul"><?php _e( 'Standard', 'pewc' ); ?></option>
				<option <?php selected( $group_layout, 'table', true ); ?> value="table"><?php _e( 'Table', 'pewc' ); ?></option>
				<option <?php selected( $group_layout, 'cols-2', true ); ?> value="cols-2"><?php _e( 'Two Columns', 'pewc' ); ?></option>
				<option <?php selected( $group_layout, 'cols-3', true ); ?> value="cols-3"><?php _e( 'Three Columns', 'pewc' ); ?></option>
			</select>
		</div>
	</div>
	<div class="form-row group-conditions-row">
		<div class="product-extra-field-third">
			<label>
				<?php _e( 'Group Conditions', 'pewc' ); ?>
			</label>
		</div>
		<div class="product-extra-field-two-thirds-right pewc-fields-conditionals">
			<?php include( PEWC_DIRNAME . '/templates/admin/views/group-condition.php' ); ?>
		</div>
	</div>
</div>
