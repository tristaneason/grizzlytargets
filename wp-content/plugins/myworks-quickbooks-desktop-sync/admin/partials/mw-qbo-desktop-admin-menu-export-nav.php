<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$tab = isset($_GET['tab'])?$_GET['tab']:'customer';
?>
<nav class="mw-qbo-sync-grey">
	<div class="nav-wrapper">
		<a class="brand-logo left" href="javascript:void(0)">
			<img src="<?php echo plugins_url( 'myworks-quickbooks-desktop-sync/admin/image/mwd-logo.png' ) ?>">
		</a>
		<ul class="hide-on-med-and-down right">

			<li class="cust-icon <?php if($tab=='customer') echo 'active' ?>">
				<a href="<?php echo admin_url('admin.php?page=mw-qbo-desktop-export&tab=customer') ?>">Customer</a>
			</li>
			
			<li class="ord-icon <?php if($tab=='order') echo 'active' ?>">
				<a href="<?php echo admin_url('admin.php?page=mw-qbo-desktop-export&tab=order') ?>">Order</a>
			</li>
			
			<li class="pay-icon <?php if($tab=='payment') echo 'active' ?>">
				<a href="<?php echo admin_url('admin.php?page=mw-qbo-desktop-export&tab=payment') ?>">Payment</a>
			</li>

		</ul>
	</div>
</nav>
