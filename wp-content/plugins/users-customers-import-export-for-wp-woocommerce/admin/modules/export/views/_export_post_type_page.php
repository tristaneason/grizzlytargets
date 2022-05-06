<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wt_iew_export_main">
	<p><?php echo $step_info['description']; ?></p>
	<div class="wt_iew_warn wt_iew_post_type_wrn" style="display:none;">
		<?php _e('Please select a post type');?>
	</div>
	<table class="form-table wt-iew-form-table">
		<tr>
			<th><label><?php _e('Select a post type to export'); ?></label></th>
			<td>
				<select name="wt_iew_export_post_type">
					<option value="">-- <?php _e('Select post type'); ?> --</option>
					<?php
					foreach($post_types as $key=>$value)
					{
						?>
					<option value="<?php echo $key;?>" <?php selected($key, $key) ?> <?php echo ($item_type==$key ? 'selected' : '');?>><?php echo $value;?></option>
						<?php
					}
					?>
				</select>
			</td>
			<td></td>
		</tr>
	</table>
		<br/>
	<?php 
	$wt_iew_post_types = array(
		'product' => array(
			'message' => __('The <b>Product Import Export for WooCommerce Add-On</b> is required to export WooCommerce Products.'),
			'link' => admin_url('plugin-install.php?tab=plugin-information&plugin=product-import-export-for-woo')
		),
		'product_review' => array(
			'message' => __('The <b>Product Import Export for WooCommerce Add-On</b> is required to export WooCommerce Product reviews.'),
			'link' => admin_url('plugin-install.php?tab=plugin-information&plugin=product-import-export-for-woo')
		),
		'product_categories' => array(
			'message' => __('The <b>Product Import Export for WooCommerce Add-On</b> is required to export WooCommerce Product categories.'),
			'link' => admin_url('plugin-install.php?tab=plugin-information&plugin=product-import-export-for-woo')
		),
		'product_tags' => array(
			'message' => __('The <b>Product Import Export for WooCommerce Add-On</b> is required to export WooCommerce Product tags.'),
			'link' => admin_url('plugin-install.php?tab=plugin-information&plugin=product-import-export-for-woo')
		),
		'order' => array(
			'message' => __('The <b>Order Export & Order Import for WooCommerce Add-On</b> is required to export WooCommerce Orders.'),
			'link' => admin_url('plugin-install.php?tab=plugin-information&plugin=order-import-export-for-woocommerce')
		),
		'coupon' => array(
			'message' => __('The <b>Order Export & Order Import for WooCommerce Add-On</b> is required to export WooCommerce Coupons.'),
			'link' => admin_url('plugin-install.php?tab=plugin-information&plugin=order-import-export-for-woocommerce')
		)
	);
	foreach ($wt_iew_post_types as $wt_iew_post_type => $wt_iew_post_type_detail) { ?>
			
	<div class="wt_iew_free_addon wt_iew_free_addon_warn <?php echo 'wt_iew_type_'.$wt_iew_post_type; ?>" style="display:none">
		<p><?php echo $wt_iew_post_type_detail['message']; ?></p>
		<a target="_blank" href="<?php echo $wt_iew_post_type_detail['link']; ?>"><?php _e( 'Install now for free' ); ?></a>
	</div>
	
	<?php
	}
	?>
</div>