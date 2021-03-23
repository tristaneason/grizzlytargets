<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$tab = isset($_GET['tab'])?$_GET['tab']:'index';
global $MWQDC_LB;
 MW_QBO_Desktop_Admin::is_trial_version_check();
?>
<nav class="mw-qbo-sync-grey">
	<div class="nav-wrapper">
		<a class="brand-logo left" href="javascript:void(0)">
			<img src="<?php echo plugins_url( 'myworks-quickbooks-desktop-sync/admin/image/mwd-logo.png' ) ?>">
		</a>
		<ul class="hide-on-med-and-down right">

			<li class="cust-icon <?php if($tab=='customer') echo 'active' ?>">
				<a href="<?php echo admin_url('admin.php?page=mw-qbo-desktop-map&tab=customer') ?>">Customer</a>
			</li>
			
			<li class="pro-icon <?php if($tab=='product') echo 'active' ?>">
				<a href="<?php echo admin_url('admin.php?page=mw-qbo-desktop-map&tab=product') ?>">Product</a>
			</li>
			
			<li class="pay-icon <?php if($tab=='paymentmethod') echo 'active' ?>">
				<a href="<?php echo admin_url('admin.php?page=mw-qbo-desktop-map&tab=paymentmethod') ?>">Payment Method</a>
			</li>
			
			<?php if(!$MWQDC_LB->option_checked('mw_wc_qbo_desk_odr_tax_as_li')):?>
			<li class="tax-icon <?php if($tab=='tax-class') echo 'active' ?>">
				<a href="<?php echo admin_url('admin.php?page=mw-qbo-desktop-map&tab=tax-class') ?>">Tax Rate</a>
			</li>
			<?php endif;?>
			
			<li class="cou-icon <?php if($tab=='coupon-code') echo 'active' ?>">
				<a href="<?php echo admin_url('admin.php?page=mw-qbo-desktop-map&tab=coupon-code') ?>">Coupon Code</a>
			</li>
			
			<li class="ship-icon <?php if($tab=='shipping-method') echo 'active' ?>">
				<a href="<?php echo admin_url('admin.php?page=mw-qbo-desktop-map&tab=shipping-method') ?>">Shipping Method</a>
			</li>
			
			<?php if($MWQDC_LB->is_plugin_active('myworks-quickbooks-desktop-custom-field-mapping') && $MWQDC_LB->check_sh_cfm_hash()):?>
			<li class="cf-icon <?php if($tab=='custom-fields') echo 'active' ?>">
				<a href="<?php echo admin_url('admin.php?page=mw-qbo-desktop-map&tab=custom-fields') ?>">Custom Fields</a>
			</li>
			<?php endif;?>

		</ul>
	</div>
</nav>
<?php echo $MWQDC_LB->get_admin_get_extra_css_js();?>
<?php echo $MWQDC_LB->get_checkbox_switch_css_js();?>

<?php require_once plugin_dir_path( __FILE__ ) . 'mw-qbo-desktop-admin-menu-guidelines.php' ?>