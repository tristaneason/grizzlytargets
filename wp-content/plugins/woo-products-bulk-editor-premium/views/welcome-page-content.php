<?php
$instance = vgse_wc_products();
?>
<p><?php _e('Thank you for installing our plugin.', $instance->textname); ?></p>

<?php
$steps = array();
if (!function_exists('WC')) {
	$steps['install_dependencies_wc'] = '<p>' . sprintf(__('Install the plugin: WooCommerce. <a href="%s" target="_blank" class="button install-plugin-trigger">Click here</a>. This is a WooCommerce extension.<br/>Reload the page after you install the plugin.', $instance->textname), $this->get_plugin_install_url('woocommerce')) . '</p>';
} else {
	$steps['open_editor'] = '<p>' . sprintf(__('You can open the Products Bulk Editor Now:  <a href="%s" class="button">Click here</a>', $instance->textname), VGSE()->helpers->get_editor_url($instance->post_type)) . '</p>';
}

include VGSE_DIR . '/views/free-extensions-for-welcome.php';
$steps['free_extensions'] = $free_extensions_html;

$steps = apply_filters('vg_sheet_editor/woocommerce_products/welcome_steps', $steps);

if (!empty($steps)) {
	echo '<ol class="steps">';
	foreach ($steps as $key => $step_content) { if(empty($step_content)){continue;}
		?>
		<li><?php echo $step_content; ?></li>		
		<?php
	}

	echo '</ol>';
}	