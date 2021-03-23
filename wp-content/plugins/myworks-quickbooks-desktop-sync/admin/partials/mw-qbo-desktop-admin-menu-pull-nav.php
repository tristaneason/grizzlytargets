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
			<li class="pro-icon <?php if($tab=='inventory') echo 'active' ?>">
				<a href="<?php echo admin_url('admin.php?page=mw-qbo-desktop-pull&tab=inventory') ?>">Inventory Levels</a>
			</li>
			
			<li class="pro-icon <?php if($tab=='productprice') echo 'active' ?>">
				<a href="<?php echo admin_url('admin.php?page=mw-qbo-desktop-pull&tab=productprice') ?>">Pricing</a>
			</li>

		</ul>
	</div>
</nav>
