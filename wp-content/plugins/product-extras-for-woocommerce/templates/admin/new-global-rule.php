<?php
/**
 * The markup for a new global rule
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
	// 	'<h4>%s</h4>',
	// 	__( 'Rules', 'pewc' )
	// ); ?>
	<?php printf(
		'<p>%s</p>',
		__( 'Display this group if:', 'pewc' )
	); ?>
	<div class="product-extra-global-rules">
		<p>
			<select class="pewc-rule-field" data-name="_product_extra_groups_GROUP_KEY[global_rules][operator]" id="_product_extra_groups_GROUP_KEY[global_rules][operator]">';
				<option value="all"><?php _e( 'All the rules are met', 'pewc' ); ?></option>
				<option value="any"><?php _e( 'Any of the rules are met', 'pewc' ); ?></option>
			}
			</select>
		</p>
		<?php $all_rules = pewc_get_group_rules();
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
						<tr>
							<td>
								<input class="pewc-rule-field" type="checkbox" name="_product_extra_groups_GROUP_KEY[global_rules][<?php echo esc_attr( $rule['id'] ); ?>][is_selected]" data-name="_product_extra_groups_GROUP_KEY[global_rules][<?php echo esc_attr( $rule['id'] ); ?>][is_selected]" id="_product_extra_groups_GROUP_KEY[global_rules][<?php echo esc_attr( $rule['id'] ); ?>][is_selected]">
							</td>
							<td>
								<label for="_product_extra_groups_GROUP_KEY[global_rules][<?php echo esc_attr( $rule['id'] ); ?>][is_selected]"><?php echo esc_html( $rule['title'] ); ?></label>
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
