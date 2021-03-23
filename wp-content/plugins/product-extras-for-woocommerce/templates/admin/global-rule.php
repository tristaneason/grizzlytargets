<?php
/**
 * The markup for a global rule
 *
 * @package WooCommerce Product Add-Ons Ultimate
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<div class="pewc-global-rules-wrapper">
	<?php
	// printf(
	// 	'<h3>%s</h3>',
	// 	__( 'Rules', 'pewc' )
	// ); ?>
	<div class="pewc-rule-instruction-wrapper">
		<?php	_e( 'Display this group if:', 'pewc' ); ?>
		<span>
			<select class="pewc-rule-field" name="_product_extra_groups_<?php echo esc_attr( $group_key ); ?>[global_rules][operator]" id="_product_extra_groups_<?php echo esc_attr( $group_key ); ?>[global_rules][operator]">';
				<?php
				$operator = pewc_get_group_operator( $group_key, $group ); ?>
				<option <?php selected( $operator, 'all' ); ?> value="all"><?php _e( 'All the selected rules are met', 'pewc' ); ?></option>
				<option <?php selected( $operator, 'any' ); ?> value="any"><?php _e( 'Any of the selected rules are met', 'pewc' ); ?></option>
			}
			</select>
		</span>
	</div>
	<div class="product-extra-global-rules">

		<?php $all_rules = pewc_get_group_rules();
		// The values for this group
		$group_rule_values = pewc_get_global_rules( $group_key, $group );
		$products = pewc_get_all_product_ids();
		$product_titles = pewc_get_all_product_titles( $products );
		if( ! empty( $all_rules ) ) { ?>
			<table class="widefat wp-list-table product-extra-global-rule-row">
				<thead>
					<tr>
						<td id="cb"></td>
						<th scope="col" id="name"><?php _e( 'Rule', 'pewc' ); ?></th>
						<th scope="col" id="includes"><?php _e( 'With', 'pewc' ); ?></th>
					</tr>
				</thead>
				<tbody id="the-list">
					<?php foreach( $all_rules as $rule ) { ?>
						<?php $checked = isset( $group_rule_values[$rule["id"]]['is_selected'] ) ? 'checked="checked"' : ''; ?>
						<?php $class = isset( $group_rule_values[$rule["id"]]['is_selected'] ) ? 'class="active"' : ''; ?>
						<tr <?php echo $class; ?>>
							<td>
								<input class="pewc-rule-field" type="checkbox" <?php echo $checked; ?> name="_product_extra_groups_<?php echo esc_attr( $group_key ); ?>[global_rules][<?php echo esc_attr( $rule['id'] ); ?>][is_selected]" id="_product_extra_groups_<?php echo esc_attr( $group_key ); ?>[global_rules][<?php echo esc_attr( $rule['id'] ); ?>][is_selected]">
							</td>
							<td>
								<label for="_product_extra_groups_<?php echo esc_attr( $group_key ); ?>[global_rules][<?php echo esc_attr( $rule['id'] ); ?>][is_selected]"><?php echo esc_html( $rule['title'] ); ?></label>
							</td>
							<td>
								<?php if( isset( $rule['callback'] ) && function_exists( $rule['callback'] ) ) {
									$select = call_user_func( $rule['callback'], $group_key, $group, $rule, $products, $product_titles );
									echo $select;
								} ?>
							</td>
					<?php } ?>
				</tbody>
			</table>
		<?php
		} ?>
	</div>
</div>
