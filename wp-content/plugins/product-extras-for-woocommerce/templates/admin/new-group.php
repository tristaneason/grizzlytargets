<?php
/**
 * The markup for a new group
 *
 * @package WooCommerce Product Add-Ons Ultimate
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<div data-group-count="" id="group-" class="group-row new-group-row">
	<input type="hidden" class="pewc_group_id" name="">
	<div class="new-field-table field-table">
		<div class="wc-metabox">

			<div class="pewc-group-heading-wrap">
				<?php
				printf(
					'<h3 class="pewc-group-meta-heading">%s <span class="meta-item-id"></span>: <span class="pewc-display-title"></span></h3>',
					__( 'Group', 'pewc' )
				); ?>

				<?php include( PEWC_DIRNAME . '/templates/admin/group-meta-actions.php' ); ?>
			</div><!-- .pewc-group-heading-wrap -->
		</div><!-- .pewc-group-meta-table -->

		<?php do_action( 'pewc_after_new_group_title', false, false, false, false ); ?>

		<div class="pewc-all-fields-wrapper">
			<div class="pewc-group-meta-table wc-metabox">
				<div class="form-row">
					<div class="product-extra-field-third">
						<label>
							<?php _e( 'Group Title', 'pewc' ); ?>
							<?php echo wc_help_tip( 'Enter a title for this group that will be displayed on the product page. Leave blank if you wish.', 'pewc' ); ?>
						</label>
					</div>
					<div class="product-extra-field-two-thirds-right">
						<input type="text" class="pewc-group-title" name="" value="">
					</div>
				</div>
				<div class="form-row">
					<div class="product-extra-field-third pewc-description">
						<label>
							<?php _e( 'Group Description', 'pewc' ); ?>
							<?php echo wc_help_tip( 'An optional description for the group', 'pewc' ); ?>
						</label>
					</div>
					<div class="product-extra-field-two-thirds-right">
						<textarea class="pewc-group-description" name=""></textarea>
					</div>
				</div>
				<div class="form-row">
					<div class="product-extra-field-third">
						<label>
							<?php _e( 'Group Layout', 'pewc' ); ?>
							<?php echo wc_help_tip( 'Choose how to display the fields in this group.', 'pewc' ); ?>
						</label>
					</div>
					<div class="product-extra-field-two-thirds-right">
						<select class="pewc-group-layout" name="">
							<option value="ul"><?php _e( 'Standard', 'pewc' ); ?></option>
							<option value="table"><?php _e( 'Table', 'pewc' ); ?></option>
						</select>
					</div>
				</div>

			</div>
			<ul class="field-list">
			</ul>
			<p><a href="#" class="button add_new_field"><?php _e( 'Add Field', 'pewc' ); ?></a></p>
		</div><!-- .pewc-fields-wrapper -->
	</div>

</div><!-- .new-group-row -->
