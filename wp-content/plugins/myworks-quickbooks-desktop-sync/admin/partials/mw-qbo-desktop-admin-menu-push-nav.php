<?php
if ( ! defined( 'ABSPATH' ) ) exit;
global $MWQDC_LB;
$tab = isset($_GET['tab'])?$_GET['tab']:'index';
 MW_QBO_Desktop_Admin::is_trial_version_check();
?>
<nav class="mw-qbo-sync-grey">
	<div class="nav-wrapper">
		<a class="brand-logo left" href="javascript:void(0)">
			<img src="<?php echo plugins_url( 'myworks-quickbooks-desktop-sync/admin/image/mwd-logo.png' ) ?>">
		</a>
		<ul class="hide-on-med-and-down right">

			<li class="cust-icon <?php if($tab=='customer') echo 'active' ?>">
				<a href="<?php echo admin_url('admin.php?page=mw-qbo-desktop-push&tab=customer') ?>">Customer</a>
			</li>
			
			<li class="ord-icon <?php if($tab=='order') echo 'active' ?>">
				<a href="<?php echo admin_url('admin.php?page=mw-qbo-desktop-push&tab=order') ?>">Order</a>
			</li>
			
			<li class="pay-icon <?php if($tab=='refund') echo 'active' ?>">
				<a href="<?php echo admin_url('admin.php?page=mw-qbo-desktop-push&tab=refund') ?>">Refund</a>
			</li>
			
			<?php if(!$MWQDC_LB->option_checked('mw_wc_qbo_desk_order_as_sales_receipt')):// && !$MWQDC_LB->option_checked('mw_wc_qbo_desk_order_as_sales_order')?>
			<li class="pay-icon <?php if($tab=='payment') echo 'active' ?>">
				<a href="<?php echo admin_url('admin.php?page=mw-qbo-desktop-push&tab=payment') ?>">Payment</a>
			</li>
			<?php endif;?>
			
			<li class="pro-icon <?php if($tab=='product') echo 'active' ?>">
				<a href="<?php echo admin_url('admin.php?page=mw-qbo-desktop-push&tab=product') ?>">Product</a>
			</li>
			
			<li class="pro-icon <?php if($tab=='inventory') echo 'active' ?>">
				<a href="<?php echo admin_url('admin.php?page=mw-qbo-desktop-push&tab=inventory') ?>">Inventory Levels</a>
			</li>

		</ul>
	</div>
</nav>
