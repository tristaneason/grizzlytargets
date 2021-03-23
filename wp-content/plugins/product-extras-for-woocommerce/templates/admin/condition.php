<?php
/**
 * The markup for a conditional row, i.e. one condition
 *
 * @package WooCommerce Product Add-Ons Ultimate
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<?php $style = 'style="display: none;"';
if( ! empty( $item['condition_field'] ) ) {
	$style = 'style="display: block;"';
} ?>
<div class="product-extra-conditional-row product-extra-action-match-row" <?php echo $style; ?>>

	<div class="product-extra-field-half">
		<?php $actions = pewc_get_actions();
		$action = '';
		if( isset( $item['condition_action'] ) ) {
			$action = $item['condition_action'];
		}
		if( ! empty( $actions ) ) { ?>
			<select class="pewc-condition-action" name="_product_extra_groups_<?php echo esc_attr( $group_id ); ?>_<?php echo esc_attr( $item_key ); ?>[condition_action]">
			<?php foreach( $actions as $key=>$value ) {
				$selected = selected( $action, $key, false );
				echo '<option ' . $selected . ' value="' . esc_attr( $key ) . '">' . esc_html( $value ) . '</option>';
			} ?>
			</select>
		<?php } ?>
	</div>

	<div class="product-extra-field-half">
		<?php $matches = pewc_get_matches();
		$match = '';
		if( isset( $item['condition_match'] ) ) {
			$match = $item['condition_match'];
		}
		if( ! empty( $matches ) ) { ?>
			<select class="pewc-condition-condition" name="_product_extra_groups_<?php echo esc_attr( $group_id ); ?>_<?php echo esc_attr( $item_key ); ?>[condition_match]">
			<?php foreach( $matches as $key=>$value ) {
				$selected = selected( $match, $key, false );
				echo '<option ' . $selected . ' value="' . esc_attr( $key ) . '">' . esc_html( $value ) . '</option>';
			} ?>
			</select>
		<?php } ?>
	</div>
</div>

<?php
if( ! empty( $item['condition_field'] ) ) {

	$condition_count = 0;

	foreach( $item['condition_field'] as $condition ) { ?>

		<div class="product-extra-conditional-row product-extra-conditional-rule" data-condition-count="<?php echo esc_attr( $condition_count ); ?>">

			<div class="product-extra-field-third">

				<?php
				$is_ajax = pewc_enable_ajax_load_addons();
				$fields = pewc_get_all_fields( $group, $is_ajax, $post_id );

				$id = 'pewc_group_' . $group_id . '_' . $item_key;
				unset( $fields[$id] );
				$field = '';

				if( isset( $item['condition_field'][$condition_count] ) ) {
					$field = $item['condition_field'][$condition_count];
				}

				// Get the field type of the selected field
				$cond_group_id = pewc_get_group_id( $field );
				$cond_field_id = pewc_get_field_id( $field );
				// $condition_field = $field;

				$condition_field_type = '';
				if( $field == 'cost' ) {
					$condition_field_type = 'cost';
				} else if( $field == 'quantity' ) {
					$condition_field_type = 'quantity';
				} else if( ! empty( $groups[$cond_group_id]['items'][$cond_field_id]['field_type'] ) ) {
					// Pre 3.0
					$condition_field_type = $groups[$cond_group_id]['items'][$cond_field_id]['field_type'];
				} else {
					// 3.0+
					$condition_field_type = get_post_meta( $cond_field_id, 'field_type', true );
				}

				if( ! empty( $fields ) ) { ?>
					<select class="pewc-condition-field pewc-condition-select" name="_product_extra_groups_<?php echo esc_attr( $group_id ); ?>_<?php echo esc_attr( $item_key ); ?>[condition_field][<?php echo esc_attr( $condition_count ); ?>]" id="condition_field_<?php echo esc_attr( $group_id ); ?>_<?php echo esc_attr( $item_key ); ?>_<?php echo esc_attr( $condition_count ); ?>" data-group-id="<?php echo esc_attr( $group_id ); ?>" data-item-id="<?php echo esc_attr( $item_key ); ?>" data-condition-id="<?php echo esc_attr( $condition_count ); ?>" data-field-type="<?php echo $condition_field_type; ?>" data-value="<?php echo $field; ?>">
					<?php foreach( $fields as $key=>$value ) {
						$selected = selected( $field, $key, false );
						echo '<option ' . $selected . ' value="' . esc_attr( $key ) . '">' . esc_html( $value ) . '</option>';
					} ?>
					</select>
				<?php } ?>
			</div>

			<div class="product-extra-field-sixth">
				<?php $class = "pewc-condition-rule pewc-condition-select";
				$rules = pewc_get_rules();
				$allow_multiple = get_post_meta( $cond_field_id, 'allow_multiple', true );
				if( $condition_field_type == 'products' && isset( $item['products_layout'] ) && ( $item['products_layout'] == 'checkboxes' || $item['products_layout'] == 'column' ) ) {
					$allow_multiple = true;
				}
				if( ( $condition_field_type == 'image_swatch' && $allow_multiple ) || $condition_field_type == 'checkbox_group' || ( $condition_field_type == 'products' && $allow_multiple ) ) {
					$class .= ' pewc-has-multiple';
				}
				$rule = 'not-selected';
				if( isset( $item['condition_rule'][$condition_count] ) ) {
					$rule = $item['condition_rule'][$condition_count];
				} ?>
				<select class="<?php echo $class; ?>" name="_product_extra_groups_<?php echo esc_attr( $group_id ); ?>_<?php echo esc_attr( $item_key ); ?>[condition_rule][<?php echo esc_attr( $condition_count ); ?>]" id="condition_rule_<?php echo esc_attr( $group_id ); ?>_<?php echo esc_attr( $item_key ); ?>_<?php echo esc_attr( $condition_count ); ?>" data-group-id="<?php echo esc_attr( $group_id ); ?>" data-item-id="<?php echo esc_attr( $item_key ); ?>" data-condition-id="<?php echo esc_attr( $condition_count ); ?>" data-rule="<?php echo esc_attr( $rule ); ?>">
					<?php
					foreach( $rules as $key=>$value ) {
						$selected = selected( $rule, $key, false );
						echo '<option ' . $selected . ' value="' . esc_attr( $key ) . '">' . esc_html( $value ) . '</option>';
					} ?>
				</select>
			</div>

			<div class="product-extra-field-half product-extra-field-last pewc-condition-value-field">
				<?php $value = '';
				if( isset( $item['condition_value'][$condition_count] ) ) {
					$value = $item['condition_value'][$condition_count];
				}

				if( $condition_field_type == 'text' ) { ?>
					<input class="pewc-condition-value pewc-condition-set-value" type="text" name="_product_extra_groups_<?php echo esc_attr( $group_id ); ?>_<?php echo esc_attr( $item_key ); ?>[condition_value][<?php echo esc_attr( $condition_count ); ?>]" id="condition_value_<?php echo esc_attr( $group_id ); ?>_<?php echo esc_attr( $item_key ); ?>_<?php echo esc_attr( $condition_count ); ?>" data-group-id="<?php echo esc_attr( $group_id ); ?>" data-item-id="<?php echo esc_attr( $item_key ); ?>" data-condition-id="<?php echo esc_attr( $condition_count ); ?>" value="<?php echo esc_attr( $value ); ?>">
				<?php } else if( $condition_field_type == 'number' || $condition_field_type == 'cost' || $condition_field_type == 'quantity' || $condition_field_type == 'calculation' || $condition_field_type == 'upload' ) { ?>
					<input class="pewc-condition-value pewc-condition-set-value" type="number" step="<?php echo apply_filters( 'pewc_condition_value_step', 1, $item_key ); ?>" name="_product_extra_groups_<?php echo esc_attr( $group_id ); ?>_<?php echo esc_attr( $item_key ); ?>[condition_value][<?php echo esc_attr( $condition_count ); ?>]" id="condition_value_<?php echo esc_attr( $group_id ); ?>_<?php echo esc_attr( $item_key ); ?>_<?php echo esc_attr( $condition_count ); ?>" data-group-id="<?php echo esc_attr( $group_id ); ?>" data-item-id="<?php echo esc_attr( $item_key ); ?>" data-condition-id="<?php echo esc_attr( $condition_count ); ?>" value="<?php echo esc_attr( $value ); ?>">
				<?php } else if( $condition_field_type == 'select' || $condition_field_type == 'select-box' || $condition_field_type == 'radio' || $condition_field_type == 'image_swatch' || $condition_field_type == 'products' || $condition_field_type == 'checkbox_group' ) { ?>
					<select class="pewc-condition-value pewc-condition-set-value" name="_product_extra_groups_<?php echo esc_attr( $group_id ); ?>_<?php echo esc_attr( $item_key ); ?>[condition_value][<?php echo esc_attr( $condition_count ); ?>]" id="condition_value_<?php echo esc_attr( $group_id ); ?>_<?php echo esc_attr( $item_key ); ?>_<?php echo esc_attr( $condition_count ); ?>" data-group-id="<?php echo esc_attr( $group_id ); ?>" data-item-id="<?php echo esc_attr( $item_key ); ?>" data-condition-id="<?php echo esc_attr( $condition_count ); ?>">
						<?php // Populate the select field
						if( $condition_field_type == 'products' ) {
							$field_options = false;
							if( ! pewc_has_migrated() && ! empty( $groups[$cond_group_id]['items'][$cond_field_id]['child_products'] ) ) {
								// Pre 3.0
								$field_options = $groups[$cond_group_id]['items'][$cond_field_id]['child_products'];
							} else {
								// 3.0+
								$field_options = get_post_meta( $cond_field_id, 'child_products', true );
							}

							if( $field_options ) {
								foreach( $field_options as $option ) {
									$selected = selected( $value, $option, false ); ?>
									<option <?php echo $selected; ?> value="<?php echo esc_attr( $option ); ?>"><?php echo esc_attr( $option ); ?></option>
								<?php }
							}
						} else {
							$field_options = array( '' );
							if( ! pewc_has_migrated() && ! empty( $groups[$cond_group_id]['items'][$cond_field_id]['field_options'] ) ) {
								// Pre 3.0
								$field_options = $groups[$cond_group_id]['items'][$cond_field_id]['field_options'];
							} else {
								// 3.0+
								$field_options = get_post_meta( $cond_field_id, 'field_options', true );
							}

							if( $field_options ) {
								$selected = selected( $value, '', false ); ?>
								<option <?php echo $selected; ?> value=""></option>
								<?php
								foreach( $field_options as $option ) {
									$selected = selected( $value, $option['value'], false ); ?>
									<option <?php echo $selected; ?> value="<?php echo esc_attr( $option['value'] ); ?>"><?php echo esc_attr( $option['value'] ); ?></option>
								<?php }
							}
						} ?>
					</select>
				<?php } else if( $condition_field_type == 'checkbox' ) { ?>
					<span class="pewc-checked-placeholder"><?php _e( 'Checked', 'pewc' ); ?></span>
					<input class="pewc-condition-value pewc-condition-set-value" type="hidden" name="_product_extra_groups_<?php echo esc_attr( $group_id ); ?>_<?php echo esc_attr( $item_key ); ?>[condition_value][<?php echo esc_attr( $condition_count ); ?>]" id="condition_value_<?php echo esc_attr( $group_id ); ?>_<?php echo esc_attr( $item_key ); ?>_<?php echo esc_attr( $condition_count ); ?>" data-group-id="<?php echo esc_attr( $group_id ); ?>" data-item-id="<?php echo esc_attr( $item_key ); ?>" data-condition-id="<?php echo esc_attr( $condition_count ); ?>" value="__checked__">
				<?php } ?>

				<span class="remove-condition pewc-action"><?php _e( 'Remove', 'pewc' ); ?></span>

			</div>

		</div><!-- .product-extra-conditional-row -->
	<?php $condition_count++;
	}
}
?>
<p><a href="#" class="button add_new_condition"><?php _e( 'Add Condition', 'pewc' ); ?></a></p>
