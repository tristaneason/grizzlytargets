<?php
if ( ! defined( 'ABSPATH' ) )
exit;
?>

<?php
global $MWQDC_LB;
$is_valid_page = false;
$page = (isset($_GET['page']))?$_GET['page']:'';
$tab = (isset($_GET['tab']))?$_GET['tab']:'';
$variation = (isset($_GET['variation']))?$_GET['variation']:'';
if(is_admin() && !empty($page)){	
	$is_valid_page = true;
	if(($page == 'mw-qbo-desktop-map' || $page == 'mw-qbo-desktop-push') && empty($tab)){
		$is_valid_page = false;
	}
}
?>

<?php if($is_valid_page):?>
<style>
	.wqam_ndc{
		padding: 20px 20px 50px 20px;
	}
	
	.wqam_tbl{
		width:360px;
	}
	
	.wqam_tbl td {
	  padding: 10px 0px 10px 0px;
	}
	.wqam_select{
		width:170px;
		float:none !important;
	}
</style>

<div class="container guide-bg-none mwqbd_gdh">
    <div class="guide-wrap">
        <div class="guide-outer">
            <div class="guidelines">
            <?php if($page == 'mw-qbo-desktop-map'){ ?>
             <div class="common-content">              
              <span id="mwqs_automap_products_msg"></span>
              <span id="mwqs_automap_products_msg_by_name"></span>
              
              <span id="mwqs_automap_variations_msg"></span>
			  <span id="mwqs_automap_variations_msg_by_name"></span>
              
              <span id="mwqs_automap_customers_msg"></span>
              <span id="mwqs_automap_customers_msg_by_name"></span>
             </div>
            <?php } ?>
                <div class="tab_prdct_sect">    
                    <ul>
                        <li><span class="toggle-btn">Guidelines <i class="fa fa-angle-down"></i></span></li>
                        <?php if($page == 'mw-qbo-desktop-map' && $tab == 'product'){ ?>
                        <li class="ab tab_one <?php if($variation != 1){ ?>active<?php } ?>" data-id="product_tab"><span><a href="<?php echo admin_url('admin.php?page=mw-qbo-desktop-map&tab=product');?>">Products</a></span></li>
                        <li class="ab tab_two <?php if($variation == 1){ ?>active<?php } ?>" data-id="variation_tab"><span><a href="<?php echo admin_url('admin.php?page=mw-qbo-desktop-map&tab=product&variation=1');?>">Variations</a></span></li>
                        <?php } ?>
                        <?php if($page == 'mw-qbo-desktop-push'){ ?>
                        <?php if($tab == 'product' || $tab == 'variation'){ ?>
                        <li class="ab tab_one <?php if($tab == 'product'){ ?>active<?php } ?>" data-id="product_tab"><span><a href="<?php echo admin_url('admin.php?page=mw-qbo-desktop-push&tab=product');?>">Products</a></span></li>
                        <li class="ab tab_two <?php if($tab == 'variation'){ ?>active<?php } ?>" data-id="variation_tab"><span><a href="<?php echo admin_url('admin.php?page=mw-qbo-desktop-push&tab=variation');?>">Variations</a></span></li>
                        <?php } ?>
                        <?php } ?>
                    </ul>
                
                    <div id="guide-target" >                        
                        <?php
                        switch ($page) {
                            case "mw-qbo-desktop-settings":
                                echo __mwqbd_settings_page_guide();
                                break;
                            case "mw-qbo-desktop-map":
                                echo __mwqbd_map_page_guide();
                                break;
                            case "mw-qbo-desktop-push":
                                echo __mwqbd_push_page_guide();
                                break;                          
                            default:
                                echo __mwqbd_default_guide();
                        }
                        ?>
                    </div>
                </div>
            </div>   
        </div>
        
        <div class="guide-dropdown-outer">
        
            <?php if($page == 'mw-qbo-desktop-map' && $tab == 'product'){ ?>
            <?php if($variation != 1){ ?>
			<?php if($html_section=false):?>
            <div class="aoutomated-outer g-d-o-btn">
                <div class="col col-m auto-map-btn">
                    <span  class="dropbtn col-m-btn">Automap Products <i class="fa fa-angle-down"></i></span>
                    <div class="dropdown-content">
                        <ul class="guide-accordion">
                            <?php wp_nonce_field( 'myworks_wc_qbo_sync_automap_products_desk', 'automap_products_desk' ); ?>
                            <li> 
                                <a id="mwqs_automap_products"><?php _e( 'By Sku', 'mw_wc_qbo_desk' );?></a>
                            </li>
                            <li>
                            <?php wp_nonce_field( 'myworks_wc_qbo_sync_automap_products_by_name_desk', 'automap_products_by_name_desk' ); ?>
                            <a id="mwqs_automap_products_by_name"><?php _e( 'By Name', 'mw_wc_qbo_desk' );?></a>
                            </li>
                        </ul>
                    </div>  
                </div>
            </div>
			<?php endif;?>
			
			<div class="aoutomated-outer g-d-o-btn">
				<div class="col col-m auto-map-btn">
					<span  class="dropbtn col-m-btn">Automap Products<i class="fa fa-angle-down"></i></span>
					<div class="dropdown-content wqam_ndc">
						<table class="wqam_tbl">
							<tr>
								<td width="50%"><?php _e( 'WooCommerce Field', 'mw_wc_qbo_desk' );?> :</td>
								<td>
									<?php wp_nonce_field( 'myworks_wc_qbo_sync_automap_products_desk_wf_qf', 'automap_products_desk_wf_qf' ); ?>
									<select class="wqam_select" id="pam_wf">
										<option value=""></option>
										<?php echo $MWQDC_LB->only_option('',$MWQDC_LB->get_n_pam_wf_list());?>
									</select>
								</td>
							</tr>
							
							<tr>
								<td><?php _e( 'QuickBooks Field', 'mw_wc_qbo_desk' );?> :</td>
								<td>
									<select class="wqam_select" id="pam_qf">
										<option value=""></option>
										<?php echo $MWQDC_LB->only_option('',$MWQDC_LB->get_n_pam_qf_list());?>
									</select>
								</td>
							</tr>
							
							<tr>
								<td colspan="2">
									<input type="checkbox" id="pam_moum_chk" value="true">
									&nbsp;
									<?php _e( 'Only apply to unmapped products', 'mw_wc_qbo_desk' );?>
								</td>
							</tr>
							
							<tr>								
								<td>								
								<button id="mwqs_automap_products_wf_qf">Automap</button>
								</td>
								<td>
									<span id="pam_wqf_e_msg"></span>
								</td>
							</tr>
							
							
						</table>						
					</div>
				</div>
			</div>
			
            <?php } ?>

            <?php if($variation == 1){ ?>
			<?php if($html_section=false):?>
            <div class="aoutomated-outer g-d-o-btn">
              <div class="col col-m auto-map-btn">
                <span  class="dropbtn col-m-btn">Automap Variations <i class="fa fa-angle-down"></i></span>
                  <div class="dropdown-content">
                    <ul class="guide-accordion">
                        <?php wp_nonce_field( 'myworks_wc_qbo_sync_automap_variations_desk', 'automap_variations_desk' ); ?>
                          <li> 
                              <a id="mwqs_automap_variations"><?php _e( 'By Sku', 'mw_wc_qbo_desk' );?></a>
                          </li>
						  
						  <li>
								<?php wp_nonce_field( 'myworks_wc_qbo_sync_automap_variations_by_name_desk', 'automap_variations_by_name_desk' ); ?>
                              <a id="mwqs_automap_variations_by_name"><?php _e( 'By Name', 'mw_wc_qbo_desk' );?></a>
                          </li>
                      </ul>
                  </div>  
              </div>
            </div>
			<?php endif;?>
			
			<div class="aoutomated-outer g-d-o-btn">
				<div class="col col-m auto-map-btn">
					<span  class="dropbtn col-m-btn">Automap Variations<i class="fa fa-angle-down"></i></span>
					<div class="dropdown-content wqam_ndc">
						<table class="wqam_tbl">
							<tr>
								<td width="50%"><?php _e( 'WooCommerce Field', 'mw_wc_qbo_desk' );?> :</td>
								<td>
									<?php wp_nonce_field( 'myworks_wc_qbo_sync_automap_variations_desk_wf_qf', 'automap_variations_desk_wf_qf' ); ?>
									<select class="wqam_select" id="vam_wf">
										<option value=""></option>
										<?php echo $MWQDC_LB->only_option('',$MWQDC_LB->get_n_vam_wf_list());?>
									</select>
								</td>
							</tr>
							
							<tr>
								<td><?php _e( 'QuickBooks Field', 'mw_wc_qbo_desk' );?> :</td>
								<td>
									<select class="wqam_select" id="vam_qf">
										<option value=""></option>
										<?php echo $MWQDC_LB->only_option('',$MWQDC_LB->get_n_vam_qf_list());?>
									</select>
								</td>
							</tr>
							
							<tr>
								<td colspan="2">
									<input type="checkbox" id="vam_moum_chk" value="true">
									&nbsp;
									<?php _e( 'Only apply to unmapped variations', 'mw_wc_qbo_desk' );?>
								</td>
							</tr>
							
							<tr>								
								<td>								
								<button id="mwqs_automap_variations_wf_qf">Automap</button>
								</td>
								<td>
									<span id="vam_wqf_e_msg"></span>
								</td>
							</tr>
							
							
						</table>						
					</div>
				</div>
			</div>
			
            <?php } ?>
            <?php } ?>

            <?php if($page == 'mw-qbo-desktop-map' && $tab == 'customer'){ ?>
			<?php if($html_section=false):?>
            <div class="aoutomated-outer g-d-o-btn">
                <div class="col col-m auto-map-btn">
                    <span  class="dropbtn col-m-btn">Automap Customers <i class="fa fa-angle-down"></i></span>
                    <div class="dropdown-content">
                        <ul class="guide-accordion">
                            <?php wp_nonce_field( 'myworks_wc_qbo_sync_automap_customers_desk', 'automap_customers_desk' ); ?>
                            <li> 
                                <a id="mwqs_automap_customers"><?php _e( 'By Email', 'mw_wc_qbo_desk' );?></a>
                            </li>
                            <li>
                            <?php wp_nonce_field( 'myworks_wc_qbo_sync_automap_customers_by_name_desk', 'automap_customers_by_name_desk' ); ?>
                            <a id="mwqs_automap_customers_by_name"><?php _e( 'By Name', 'mw_wc_qbo_desk' );?></a>
                            </li>
                        </ul>
                    </div>  
                </div>
            </div>
			<?php endif;?>
			
			<div class="aoutomated-outer g-d-o-btn">
				<div class="col col-m auto-map-btn">
					<span  class="dropbtn col-m-btn">Automap Customers<i class="fa fa-angle-down"></i></span>
					<div class="dropdown-content wqam_ndc">
						<table class="wqam_tbl">
							<tr>
								<td width="50%"><?php _e( 'WooCommerce Field', 'mw_wc_qbo_desk' );?> :</td>
								<td>
									<?php wp_nonce_field( 'myworks_wc_qbo_sync_automap_customers_desk_wf_qf', 'automap_customers_desk_wf_qf' ); ?>
									<select class="wqam_select" id="cam_wf">
										<option value=""></option>
										<?php echo $MWQDC_LB->only_option('',$MWQDC_LB->get_n_cam_wf_list());?>
									</select>
								</td>
							</tr>
							
							<tr>
								<td><?php _e( 'QuickBooks Field', 'mw_wc_qbo_desk' );?> :</td>
								<td>
									<select class="wqam_select" id="cam_qf">
										<option value=""></option>
										<?php echo $MWQDC_LB->only_option('',$MWQDC_LB->get_n_cam_qf_list());?>
									</select>
								</td>
							</tr>
							
							<tr>
								<td colspan="2">
									<input type="checkbox" id="cam_moum_chk" value="true">
									&nbsp;
									<?php _e( 'Only apply to unmapped customers', 'mw_wc_qbo_desk' );?>
								</td>
							</tr>
							
							<tr>								
								<td>								
								<button id="mwqs_automap_customers_wf_qf">Automap</button>
								</td>
								<td>
									<span id="cam_wqf_e_msg"></span>
								</td>
							</tr>
							
							
						</table>						
					</div>
				</div>
			 </div>
			 
            <?php } ?>

            <div class="guide-dropdown" style="position:static">
               <span class="dropbtn">Need Help?  <i class="fa fa-angle-down"></i></span>
               <div class="dropdown-content">
                  <?php
                  switch ($page) {
                      case "mw-qbo-desktop-settings":
                          echo __mwqbd_settings_page_help();
                          break;
                      case "mw-qbo-desktop-map":
                          echo __mwqbd_map_page_help();
                          break;
                      case "mw-qbo-desktop-push":
                          echo __mwqbd_push_page_help();
                          break;                          
                      default:
                          echo __mwqbd_default_help();
                  }
                  ?>
             </div>
          </div>
          
    
      </div>
    </div><!--guide-wrap-->
    
</div>


<script>
jQuery('.toggle-btn').click(function() {
    jQuery('#guide-target').slideToggle('fast');
});
jQuery(".toggle-btn").click(function(){
    jQuery(".toggle-btn").toggleClass("toggle-sub");
});

  
jQuery('.guide-accordion').find('li').click(function(){
	if(jQuery(this).hasClass('open')){			
		jQuery(this).find('.guide-submenu').slideUp();
		jQuery(this).removeClass('open');
	}else{
		jQuery('.guide-accordion').find('.guide-submenu').slideUp();
		jQuery('.guide-accordion').find('li').removeClass('open');
		jQuery(this).find('.guide-submenu').slideDown();
		jQuery(this).addClass('open');
	}
});

</script>
<?php endif;?>

<?php
function __mwqbd_settings_page_guide(){
  $tab = (isset($_GET['tab']))?$_GET['tab']:'';
  $HTML = '<div class="guide settings default" style="display: block;">
            All the dropdowns on this <strong>Default</strong> tab need to be selected to complete setup. Hover over the question marks on the right if you are unsure what a certain setting is, and make sure that the option you select makes sense based on your store settings.
            </div>
            <div class="guide settings order" style="display: none;">
            This section contains settings relevant to syncing WooCommerce Orders. All of these settings are optional and can be left as is for acceptable plugin operation. If you are unsure about a setting, hover over the question mark on the right for an explanation.
            </div>
            <div class="guide settings tax" style="display: none;">
            This section contains settings relevant to tax settings for syncing WooCommerce Orders. All of these settings must be set for acceptable plugin operation. Hover over the question marks on the right for an explanation of each setting.
          </div>
          <div class="guide settings map" style="display: none;">
            This section contains settings relevant to the mapping operations of our plugin. All of these settings are optional and can be left as is for acceptable plugin operation. Hover over the question marks on the right for an explanation of each setting.
            </div>
            <div class="guide settings pull" style="display: none;">
            This section contains settings relevant to the pull operations (pulling data from QuickBooks Desktop to WooCommerce) of our plugin. All of these settings are optional and can be left as is for acceptable plugin operation. Hover over the question marks on the right for an explanation of each setting.
            </div>
            <div class="guide settings sync" style="display: none;">
            This section contains settings relevant to the real time sync operations of our plugin. These settings are set to recommended defaults and can be left as is for acceptable plugin operation. Hover over the question marks on the right for an explanation of each setting.
          </div>
          <div class="guide settings misc" style="display: none;">
            This section contains miscellaneous settings that are already set to recommended defaults.
          </div>
          <script>
          jQuery(document).ready(function(e){
            jQuery("#mw_qbo_sybc_settings_tab_one").on("click",function(e){
              jQuery(".settings").hide();
              jQuery(".default").show();
            });
            jQuery("#mw_qbo_sybc_settings_tab_two").on("click",function(e){
              jQuery(".settings").hide();
              jQuery(".order").show();
            });
            jQuery("#mw_qbo_sybc_settings_tab_five").on("click",function(e){
              jQuery(".settings").hide();
              jQuery(".map").show();
            });
            jQuery("#mw_qbo_sybc_settings_tab_four").on("click",function(e){
              jQuery(".settings").hide();
              jQuery(".tax").show();
            });
            jQuery("#mw_qbo_sybc_settings_tab_six").on("click",function(e){
              jQuery(".settings").hide();
              jQuery(".pull").show();
            });
            jQuery("#mw_qbo_sybc_settings_tab_wh").on("click",function(e){
              jQuery(".settings").hide();
              jQuery(".sync").show();
            });
            jQuery("#mw_qbo_sybc_settings_tab_nine").on("click",function(e){
              jQuery(".settings").hide();
              jQuery(".misc").show();
            });
          })
          </script>
          ';
  return $HTML;
}

function __mwqbd_map_page_guide(){
  $tab = (isset($_GET['tab']))?$_GET['tab']:'';  
  if($tab=='customer'){
    $HTML = '<div class="guide">
            This page allows you to map existing WooCommerce customers to existing QuickBooks customers. Only customers that exist in both systems need to be mapped.
            </div>
            <div class="guide">
            When new customers are created in WooCommerce from this point forward, they will be automatically synced to QuickBooks Desktop AND mapped in this page.
            </div>';
  }elseif($tab=='payment-method'){
    $HTML = '<div class="guide">
            To map & configure WooCommerce payment gateways for when orders are synced to QuickBooks Desktop, turn the ‘Enable Payment Syncing’ switch on for each payment method you’d like to sync, and then select a label and a <strong>bank account</strong> for this payment method. This ensures that payments are deposited into the correct QuickBooks Desktop bank account when we sync orders over to QuickBooks Desktop.
            </div>
			<div class="guide">
			            Advanced options are available - click “Show Advanced Options” and hover over the question marks on the right for an explanation of each setting.
			            </div>';
  }elseif($tab=='product'){
    $HTML = '<div class="guide">
            This section allows you to map (or link) together products that exist in both systems. If a product exists in WooCommerce, but not in QuickBooks Desktop, you would not be able to map it here until you push it to QuickBooks in MyWorks Sync > Push > Products. (Pushing a product will also automatically map it here.)
            </div>
            <div class="guide">
            When new products are created in WooCommerce from this point forward, they will be automatically synced to QuickBooks Desktop AND mapped in this page - if you have the Product switch enabled in MyWorks Sync > Settings > Automatic Sync.
            </div>
            <div class="guide">
            We recommend you map as many of your products as you can. Mapping products ensures that orders are accurately synced over and inventory can be accurately synced.
            </div>
            <div class="guide">
            There is no need to map a parent variable product, since a parent variable product itself never actually gets ordered. Only its variations would need to get mapped - in the Variations tab on this page. Since QuickBooks Desktop does not directly support variations, variations in WooCommerce can be mapped to QuickBooks Desktop products. 
          </div>';
  }elseif($tab=='tax-class'){
    $HTML = '<div class="guide">
            This page allows you to map WooCommerce tax rules to existing QuickBooks Desktop tax rules.
            If a tax rule is not mapped, and an order is placed that includes that tax rule, an error will most likely occur.
            </div>
            <div class="guide">
            If you have more than 100 tax rules, we highly recommend considering an automated tax rule management system - such as <a href="https://docs.myworks.software/woocommerce-sync-for-quickbooks-desktop/compatibility-addons/avalara-avatax">Avalara</a>. This will greatly reduce the time you spend manually managing your tax rates in WooCommerce.
            </div>';
  }elseif($tab=='shipping-method'){
    $HTML = '<div class="guide">
            To map your WooCommerce Shipping Methods to QuickBooks shipping methods, choose a QuickBooks Desktop product in the dropdown in the right column and select it. You can also search for a product in the dropdown field. Then scroll to the bottom and click save.
            </div>';
  }else{
    $HTML = '<div class="guide">
            Need help on this? Please contact our support anytime! 
            </div>';
  }
  return $HTML;
}

function __mwqbd_push_page_guide(){
  $tab = (isset($_GET['tab']))?$_GET['tab']:'';
  if($tab=='customer'){
    $HTML = '<div class="guide">
            This section allows you to push customers from WooCommerce to QuickBooks Desktop. If a customer already exists in QuickBooks Desktop, you should map them in MyWorks Sync > Map > Customers.
            </div>
            <div class="guide">
            It isn\'t very common to push customers on this page, as if a customer does not exist in QuickBooks Desktop, our integration will automatically create them in QuickBooks Desktop the next time they place an order in WooCommerce. Hence, it is perfectly fine to leave customers unsynced on this page if they don\'t exist in QuickBooks Desktop.
            </div>';
  }elseif($tab=='invoice'){
    $HTML = '<div class="guide">
            This section allows you to push existing WooCommerce orders into QuickBooks Desktop. New WooCommerce orders will be automatically synced into QuickBooks Desktop. If an order already exists in QuickBooks Desktop, and you push it here - it will simply be updated in QuickBooks Desktop, never duplicated.
            </div>
            <div class="guide">
            If you have orders set to sync to QuickBooks Desktop as Invoices (in MyWorks Sync > Settings > Order), note that you must also push over the Payment after you push the Order over - as pushing orders on this page will push the invoice over to QuickBooks Desktop. You can push payments in MyWorks Sync > Push > Payments.
            </div>
            <div class="guide">
           We recommend you only push over orders that are completed or processing. If an order is pending payment or cancelled, for example - pushing it to QuickBooks Desktop will create it in QuickBooks Desktop as an actual order, which would be incorrect.
            </div>';
  }elseif($tab=='product'){
    $HTML = '<div class="guide">
            This section allows you to push products from WooCommerce to QuickBooks Desktop. If a product already exists in QuickBooks Desktop, you should map it - in MyWorks Sync > Map > Products.
            </div>
            <div class="guide">
           Products that have Manage Stock turned on in WooCommerce will be created in QuickBooks Desktop as Inventory Products when you push them. It is important to note that the inventory Start Date of these products in QuickBooks Desktop will be today\'s date (the day that you push them).
            </div>
            <div class="guide">
           If a product already exists in QuickBooks Desktop, and you simply want to update its inventory level in QuickBooks Desktop - then you should visit MyWorks Sync > Push > Inventory Levels.
            </div>';
	    }elseif($tab=='variation'){
	      $HTML = '<div class="guide">
	              This section allows you to push variations from WooCommerce to QuickBooks Desktop. If a product already exists in QuickBooks Desktop, you should map it - in MyWorks Sync > Map > Products.
	              </div>
	              <div class="guide">
	             Since QuickBooks Desktop does not directly support variations, variations in WooCommerce will be created in QuickBooks Desktop as products - and mapped together. 
	              </div>
	              <div class="guide">
	             Variations that have Manage Stock turned on in WooCommerce will be created in QuickBooks Desktop as Inventory Products when you push them. It is important to note that the inventory Start Date of these products in QuickBooks Desktop will be today\'s date (the day that you push them).
	              </div>
	              ';
  }elseif($tab=='inventory'){
    $HTML = '<div class="guide">
            This section allows you to push inventory levels from WooCommerce to QuickBooks Desktop for products that already exist in both systems AND are mapped in MyWorks Sync > Map > Products. Only mapped products will show up on this page. 
            </div>
            <div class="guide">
            If the intended product does not yet exist in QuickBooks Desktop, you must first push it over in MyWorks Sync > Push > Products, before pushing over inventory levels.
            </div>';
	    }elseif($tab=='category'){
	      $HTML = '<div class="guide">
	              This section allows you to push WooCommerce categories over to QuickBooks Desktop. This is totally optional - as categories in QuickBooks Desktop are purely organizational - and have no effect on orders or products in QuickBooks Desktop. 
	              </div>
	            ';
  }elseif($tab=='shipping-method'){
    $HTML = '<div class="guide">
            To map your WooCommerce Shipping Methods to QuickBooks shipping methods, choose a QuickBooks Desktop product in the dropdown in the right column and select it. You can also search for a product in the dropdown field. Then scroll to the bottom and click save.
            </div>';
  }
  elseif($tab=='payment'){
    $HTML = '<div class="guide">
            In this tab, you can select and push payments from <a target="_blank" href="https://myworks.software/integrations/sync-woocommerce-quickbooks-online" target="_blank" rel="nofollow noreferrer">﻿WooCommerce to QuickBooks Desktop﻿</a>. Choose the Filter option to search for a specific customer or date range, and use the dropdown on the top right to display 20 or more entries per page.
            </div>
            <div class="guide">
            Before pushing payments, you should check and verify the mappings in Map > Payment Methods are correct.
            </div>
            <div class="guide">
            Note that <b><a target="_blank" href="https://docs.myworks.software/woocommerce-sync-for-quickbooks-desktop/getting-started/push-pages#payment-push">an invoice must already exist in QuickBooks Desktop</b>, or it will error out. 
            </div>';
  }else{
    $HTML = '<div class="guide">
            Need help on this? Please contact our support anytime! 
            </div>';
  }
  return $HTML;
}

function __mwqbd_default_guide(){
  $tab = (isset($_GET['tab']))?$_GET['tab']:'';
  $HTML = '<div class="guide">
            Need help on this? Please contact our support anytime! 
            </div>';
  return $HTML;
}

function __mwqbd_map_page_help(){
  $tab = (isset($_GET['tab']))?$_GET['tab']:'';
  if($tab=='customer'){
    $HTML = '<ul id="guide-accordion" class="guide-accordion">
    <li>
      <div class="acco-link">Getting Started</div>
      <ul class="guide-submenu">
        <li><a target="_blank" href="https://docs.myworks.software/woocommerce-sync-for-quickbooks-desktop/getting-started/mapping">About Mapping</a></li>
        <li><a href="https://docs.myworks.software/woocommerce-sync-for-quickbooks-desktop/getting-started/mapping#before-you-map">Before you map</a></li>
        <li><a href="https://docs.myworks.software/woocommerce-sync-for-quickbooks-desktop/common-questions/what-is-the-auto-mapping-feature-and-how-can-i-use-it#customers">Auto-Mapping Customers</a></li>
        <li><a href="https://www.youtube.com/watch?v=KHECaScWVx4">Video Walkthrough</a></li>
      </ul>
    </li>
    <li>
      <div class="acco-link">Common Questions</div>
      <ul class="guide-submenu">
        <li><a href="https://docs.myworks.software/woocommerce-sync-for-quickbooks-desktop/common-questions/what-does-mapping-mean-and-why-do-i-have-to-map-my-customers-and-products">What does “Mapping” mean, and why do I have to Map my customers and products?</a></li>
        <li><a href="https://docs.myworks.software/woocommerce-sync-for-quickbooks-desktop/customers/what-is-the-auto-mapping-feature-and-how-can-i-use-it">What is the “Auto Mapping” feature, and how can I use it?</a></li>
        <li><a href="https://docs.myworks.software/woocommerce-sync-for-quickbooks-desktop/common-questions/how-are-mappings-handled-on-an-ongoing-basis">How are mappings handled on an ongoing basis?</a></li>
      </ul>
    </li>
    '.__mwqbd_need_help_common().'
  </ul>';
  }elseif($tab=='payment-method'){
    $HTML = '<ul id="guide-accordion" class="guide-accordion">
    <li>
      <div class="acco-link">Getting Started</div>
      <ul class="guide-submenu">
        <li><a target="_blank" href="https://docs.myworks.software/woocommerce-sync-for-quickbooks-desktop/getting-started/mapping#payment-gateway-mapping">Mapping payment gateways</a></li>
        <li><a target="_blank" href="https://www.youtube.com/watch?v=a4oSksFnOVs">Video Walkthrough</a></li>
      </ul>
    </li>
    <li>
      <div class="acco-link">Common Questions</div>
      <ul class="guide-submenu">
        <li><a target="_blank" href="https://docs.myworks.software/woocommerce-sync-for-quickbooks-desktop/common-questions/how-are-mappings-handled-on-an-ongoing-basis">How are mappings handled on an ongoing basis?</a></li>
        <li><a target="_blank" href="https://docs.myworks.software/woocommerce-sync-for-quickbooks-desktop/payments/transaction-fee-syncing">How can I handle transaction fees?</a></li>
        <li><a target="_blank" href="https://docs.myworks.software/woocommerce-sync-for-quickbooks-desktop/payments/handling-transaction-fees-with-paypal">How can I sync transaction fees with PayPal?</a></li>
        <li><a target="_blank" href="https://docs.myworks.software/woocommerce-sync-for-quickbooks-desktop/payments/batch-deposit-support-with-stripe">Do you have batch support?</a></li>
        <li><a target="_blank" href="https://docs.myworks.software/woocommerce-sync-for-quickbooks-desktop/payments/batch-support-for-pre-existing-orders">How can I handle batch support for existing orders?</a></li>
      </ul>
    </li>
    '.__mwqbd_need_help_common().'
  </ul>';
  }elseif($tab=='product'){
    $HTML = '<ul id="guide-accordion" class="guide-accordion">
    <li>
      <div class="acco-link">Getting Started</div>
      <ul class="guide-submenu">
        <li><a target="_blank" href="https://docs.myworks.software/woocommerce-sync-for-quickbooks-desktop/getting-started/mapping#product-mapping">Product Mapping</a></li>
        <li><a target="_blank" href="https://docs.myworks.software/woocommerce-sync-for-quickbooks-desktop/common-questions/what-is-the-auto-mapping-feature-and-how-can-i-use-it#products">Auto-Mapping Products</a></li>
        <li><a target="_blank" href="https://docs.myworks.software/woocommerce-sync-for-quickbooks-desktop/products/variation-support">Variation Support</a></li>
        <li><a target="_blank" href="https://docs.myworks.software/woocommerce-sync-for-quickbooks-desktop/products/bundled-product-support">Bundled Product Support</a></li>
        <li><a target="_blank" href="https://www.youtube.com/watch?v=md4x4EX5ZVU">Video Walkthrough</a></li>
      </ul>
    </li>
    <li>
      <div class="acco-link">Common Questions</div>
      <ul class="guide-submenu">
        <li><a target="_blank" href="https://docs.myworks.software/woocommerce-sync-for-quickbooks-desktop/customers/what-is-the-auto-mapping-feature-and-how-can-i-use-it#products">How can I use the auto-mapping feature?</a></li>
        <li><a target="_blank" href="https://docs.myworks.software/woocommerce-sync-for-quickbooks-desktop/common-questions/how-are-mappings-handled-on-an-ongoing-basis">How are mappings handled on an ongoing basis?</a></li>
        <li><a target="_blank" href="https://docs.myworks.software/woocommerce-sync-for-quickbooks-desktop/products/variation-support#mapping">How can I map my variations?</a></li>
        <li><a target="_blank" href="https://docs.myworks.software/woocommerce-sync-for-quickbooks-desktop/products/bundled-product-support#mapping">How can I map bundled products?</a></li>
      </ul>
    </li>
    '.__mwqbd_need_help_common().'
  </ul>';
  }elseif($tab=='tax-class'){
    $HTML = '<ul id="guide-accordion" class="guide-accordion">
    <li>
      <div class="acco-link">Getting Started</div>
      <ul class="guide-submenu">
        <li><a target="_blank" href="https://docs.myworks.software/woocommerce-sync-for-quickbooks-desktop/taxes/mapping-taxes-best-practices">Mapping Taxes - Best Practices</a></li>
        <li><a target="_blank" href="https://docs.myworks.software/woocommerce-sync-for-quickbooks-desktop/advanced-topics/mapping-taxes-best-practices#mapping-combined-tax-rates">Mapping Combined Tax Rates</a></li>
      </ul>
    </li>
    <li>
      <div class="acco-link">Common Questions</div>
      <ul class="guide-submenu">
        <li><a target="_blank" href="https://docs.myworks.software/woocommerce-sync-for-quickbooks-desktop/common-questions/how-are-mappings-handled-on-an-ongoing-basis">How are mappings handled on an ongoing basis?</a></li>
      </ul>
    </li>
    '.__mwqbd_need_help_common().'
  </ul>';
  }elseif($tab=='shipping-method'){
    $HTML = '<ul id="guide-accordion" class="guide-accordion">
    <li>
      <div class="acco-link">Getting Started</div>
      <ul class="guide-submenu">
        <li><a target="_blank" href="https://docs.myworks.software/woocommerce-sync-for-quickbooks-desktop/getting-started/mapping#shipping-method-mapping">Configuring shipping method mapping</a></li>
      </ul>
    </li>
    <li>
      <div class="acco-link">Common Questions</div>
      <ul class="guide-submenu">
         <li><a target="_blank" href="https://docs.myworks.software/woocommerce-sync-for-quickbooks-desktop/common-questions/how-are-mappings-handled-on-an-ongoing-basis">How are mappings handled on an ongoing basis?</a></li>
        <li><a target="_blank" href="https://docs.myworks.software/woocommerce-sync-for-quickbooks-desktop/shipping/shipping-as-a-line-item-or-a-subtotal-field">Shipping: As a line item or a subtotal field?</a></li>
      </ul>
    </li>
    '.__mwqbd_need_help_common().'
  </ul>';
  }else{
    $HTML = '<ul id="guide-accordion" class="guide-accordion">
    <li>
      <div class="acco-link">Need help on this?</div>
      <ul class="guide-submenu">
        <li><a href="#">Please contact our support anytime!</a></li>
        <li><a href="mailto:support@myworks.design">support@myworks.design</a></li>
        <li><a href="#">Expect reply within 24 business hours!</a></li>
      </ul>
    </li>
  </ul>';
  }
  return $HTML;
}

function __mwqbd_push_page_help(){
  $tab = (isset($_GET['tab']))?$_GET['tab']:'';
  if($tab=='customer'){
    $HTML = '<ul id="guide-accordion" class="guide-accordion">
    <li>
      <div class="acco-link">Getting Started</div>
      <ul class="guide-submenu">
        <li><a target="_blank" href="https://docs.myworks.software/woocommerce-sync-for-quickbooks-desktop/getting-started/push-pages">Intro to pushing</a></li>
        <li><a target="_blank" href="https://docs.myworks.software/woocommerce-sync-for-quickbooks-desktop/getting-started/push-pages#customers">Pushing customers</a></li>
        <li><a target="_blank" href="https://docs.myworks.software/woocommerce-sync-for-quickbooks-desktop/getting-started/mapping#customer-mapping">Mapping customers</a></li>
      </ul>
    </li>
    <li>
      <div class="acco-link">Common questions</div>
      <ul class="guide-submenu">
        <li><a target="_blank" href="https://docs.myworks.software/woocommerce-sync-for-quickbooks-desktop/common-questions/what-does-mapping-mean-and-why-do-i-have-to-map-my-customers-and-products">What does “Mapping” mean, and why do I have to Map my customers and products?</a></li>
      </ul>
    </li>
    '.__mwqbd_need_help_common().'
  </ul>';
  }elseif($tab=='invoice'){
    $HTML = '<ul id="guide-accordion" class="guide-accordion">
    <li>
      <div class="acco-link">Getting Started</div>
      <ul class="guide-submenu">
        <li><a target="_blank" href="https://docs.myworks.software/woocommerce-sync-for-quickbooks-desktop/getting-started/push-pages#order-push">How to push orders</a></li>
      </ul>
    </li>
    <li>
      <div class="acco-link">Troubleshooting</div>
      <ul class="guide-submenu">
         <li><a target="_blank" href="https://docs.myworks.software/woocommerce-sync-for-quickbooks-desktop/troubleshooting/error6520-missing-inventory-item-quantity-quantity-required-for-inventory-item">Error:6520 – Missing Inventory Item Quantity, Quantity required for inventory item</a></li>
        <li><a target="_blank" href="https://docs.myworks.software/woocommerce-sync-for-quickbooks-desktop/troubleshooting/cant-modify-a-transaction-before-you-started-tracking-quantity-on-hand">You can’t create or modify a transaction with a date that comes before you started tracking quantity on hand</a></li>
      </ul>
    </li>
    '.__mwqbd_need_help_common().'
  </ul>';
  }elseif($tab=='product'){
    $HTML = '<ul id="guide-accordion" class="guide-accordion">
    <li>
      <div class="acco-link">Getting Started</div>
      <ul class="guide-submenu">
        <li><a target="_blank" href="https://docs.myworks.software/woocommerce-sync-for-quickbooks-desktop/getting-started/push-pages#product-push">Pushing Products</a></li>
        <li><a target="_blank" href="https://docs.myworks.software/woocommerce-sync-for-quickbooks-desktop/getting-started/mapping#product-mapping">Mapping products</a></li>
      </ul>
    </li>
    <li>
      <div class="acco-link">Common Questions</div>
      <ul class="guide-submenu">
        <li><a target="_blank" href="https://docs.myworks.software/woocommerce-sync-for-quickbooks-desktop/products/variation-support">Do you support variations?</a></li>
        <li><a target="_blank" href="https://docs.myworks.software/woocommerce-sync-for-quickbooks-desktop/products/bundled-product-support">Do you support bundles?</a></li>
        <li><a target="_blank" href="https://docs.myworks.software/woocommerce-sync-for-quickbooks-desktop/inventory/how-can-i-control-if-products-are-synced-to-quickbooks-as-inventory-or-non-inventory">How can I control if products are synced to QuickBooks as Inventory or Non-Inventory?</a></li>
        <li><a target="_blank" href="https://docs.myworks.software/woocommerce-sync-for-quickbooks-desktop/common-questions/what-does-mapping-mean-and-why-do-i-have-to-map-my-customers-and-products">What does “Mapping” mean, and why do I have to Map my customers and products?</a></li>
      </ul>
    </li>
    <li>
      <div class="acco-link">Troubleshooting</div>
      <ul class="guide-submenu">
         <li><a target="_blank" href="https://docs.myworks.software/woocommerce-sync-for-quickbooks-desktop/troubleshooting/error6430-invalid-account-type-used-invalid-account-type-you-need-to-select-a-different-type-of-account-for-this-transaction">Invalid Account Type</a></li>
      </ul>
    </li>
    '.__mwqbd_need_help_common().'
  </ul>';
  }elseif($tab=='variation'){
    $HTML = '<ul id="guide-accordion" class="guide-accordion">
    <li>
      <div class="acco-link">How do I push a project</div>
      <ul class="guide-submenu">
        <li><a target="_blank" href="#">Topic under project</a></li>
        <li><a target="_blank" href="#">Topic under project</a></li>
        <li><a target="_blank" href="#">Topic under project</a></li>
      </ul>
    </li>
    <li>
      <div class="acco-link">How to submit a Entry</div>
      <ul class="guide-submenu">
        <li><a target="_blank" href="#">Topic under project</a></li>
        <li><a target="_blank" href="#">Topic under project</a></li>
        <li><a target="_blank" href="#">Topic under project</a></li>
      </ul>
    </li>
    <li>
      <div class="acco-link">How do I push a project</div>
      <ul class="guide-submenu">
         <li><a target="_blank" href="#">Topic under project</a></li>
        <li><a target="_blank" href="#">Topic under project</a></li>
        <li><a target="_blank" href="#">Topic under project</a></li>
      </ul>
    </li>
    '.__mwqbd_need_help_common().'
  </ul>';
  }elseif($tab=='inventory'){
    $HTML = '<ul id="guide-accordion" class="guide-accordion">
    <li>
      <div class="acco-link">Getting started</div>
      <ul class="guide-submenu">
        <li><a target="_blank" href="https://docs.myworks.software/woocommerce-sync-for-quickbooks-desktop/inventory/syncing-inventory-with-quickbooks-online">Syncing Inventory with QuickBooks Desktop</a></li>
        <li><a target="_blank" href="https://docs.myworks.software/woocommerce-sync-for-quickbooks-desktop/getting-started/push-pages#inventory-levels">Pushing inventory</a></li>
      </ul>
    </li>
    <li>
      <div class="acco-link">Common Questions</div>
      <ul class="guide-submenu">
        <li><a target="_blank" href="https://docs.myworks.software/woocommerce-sync-for-quickbooks-desktop/products/controlling-if-products-are-synced-to-quickbooks-as-inventory-or-non-inventory">How can I control if products are synced to QuickBooks as Inventory or Non-Inventory?</a></li>
      </ul>
    </li>
    '.__mwqbd_need_help_common().'
  </ul>';
  }elseif($tab=='category'){
    $HTML = '<ul id="guide-accordion" class="guide-accordion">
    <li>
      <div class="acco-link">How do I push a project</div>
      <ul class="guide-submenu">
        <li><a target="_blank" href="#">Topic under project</a></li>
        <li><a target="_blank" href="#">Topic under project</a></li>
        <li><a target="_blank" href="#">Topic under project</a></li>
      </ul>
    </li>
    <li>
      <div class="acco-link">How to submit a Entry</div>
      <ul class="guide-submenu">
        <li><a target="_blank" href="#">Topic under project</a></li>
        <li><a target="_blank" href="#">Topic under project</a></li>
        <li><a target="_blank" href="#">Topic under project</a></li>
      </ul>
    </li>
    <li>
      <div class="acco-link">How do I push a project</div>
      <ul class="guide-submenu">
         <li><a target="_blank" href="#">Topic under project</a></li>
        <li><a target="_blank" href="#">Topic under project</a></li>
        <li><a target="_blank" href="#">Topic under project</a></li>
      </ul>
    </li>
    '.__mwqbd_need_help_common().'
  </ul>';
  }elseif($tab=='shipping-method'){
    $HTML = '<ul id="guide-accordion" class="guide-accordion">
    <li>
      <div class="acco-link">How do I push a project</div>
      <ul class="guide-submenu">
        <li><a target="_blank" href="#">Topic under project</a></li>
        <li><a target="_blank" href="#">Topic under project</a></li>
        <li><a target="_blank" href="#">Topic under project</a></li>
      </ul>
    </li>
    <li>
      <div class="acco-link">How to submit a Entry</div>
      <ul class="guide-submenu">
        <li><a target="_blank" href="#">Topic under project</a></li>
        <li><a target="_blank" href="#">Topic under project</a></li>
        <li><a target="_blank" href="#">Topic under project</a></li>
      </ul>
    </li>
    <li>
      <div class="acco-link">How do I push a project</div>
      <ul class="guide-submenu">
         <li><a target="_blank" href="#">Topic under project</a></li>
        <li><a target="_blank" href="#">Topic under project</a></li>
        <li><a target="_blank" href="#">Topic under project</a></li>
      </ul>
    </li>
    '.__mwqbd_need_help_common().'
  </ul>';
  }elseif($tab=='payment'){
    $HTML = '<ul id="guide-accordion" class="guide-accordion">
    <li>
      <div class="acco-link">Getting Started</div>
      <ul class="guide-submenu">
        <li><a target="_blank" href="https://docs.myworks.software/woocommerce-sync-for-quickbooks-desktop/getting-started/push-pages#payments">Pushing payments</a></li>
        <li><a target="_blank" href="https://docs.myworks.software/woocommerce-sync-for-quickbooks-desktop/payments/transaction-fee-syncing">Transaction fee syncing</a></li>
      </ul>
    </li>
    <li>
      <div class="acco-link">Common Questions</div>
      <ul class="guide-submenu">
        <li><a target="_blank" href="https://docs.myworks.software/woocommerce-sync-for-quickbooks-desktop/payments/handling-non-immediate-payment-gateways-cod-wire-transfer">How can I Handle non-immediate payment gateways (COD, Wire Transfer)?</a></li>
        <li><a target="_blank" href="https://docs.myworks.software/woocommerce-sync-for-quickbooks-desktop/payments/batch-deposit-support-with-stripe">How can I handle batch support with stripe?</a></li>
      </ul>
    </li>
    '.__mwqbd_need_help_common().'
  </ul>';
  }else{
    $HTML = '<ul id="guide-accordion" class="guide-accordion">
    <li>
      <div class="acco-link">Need help on this?</div>
      <ul class="guide-submenu">
        <li><a href="#">Please contact our support anytime!</a></li>
        <li><a href="mailto:support@myworks.design">support@myworks.design</a></li>
        <li><a href="#">Expect reply within 24 business hours!</a></li>
      </ul>
    </li>
  </ul>';
  }
  return $HTML;
}

function __mwqbd_settings_page_help(){
  $tab = (isset($_GET['tab']))?$_GET['tab']:'';
  $HTML = '<ul id="guide-accordion" class="guide-accordion settings default" style="display: block;">
    <li>
      <div class="acco-link">Getting Started</div>
      <ul class="guide-submenu">
        <li><a target="_blank" href="https://docs.myworks.software/woocommerce-sync-for-quickbooks-desktop/getting-started/settings#default">Configuring default settings</a></li>
        <li><a target="_blank" href="https://www.youtube.com/watch?v=SypFTQslnFs">Video walkthrough</a></li>
      </ul>
    </li>
    <li>
      <div class="acco-link">Common Questions</div>
      <ul class="guide-submenu">
        <li><a target="_blank" href="https://docs.myworks.software/woocommerce-sync-for-quickbooks-desktop/getting-started/settings">What should my default product be?</a></li>
        <li><a target="_blank" href="https://docs.myworks.software/woocommerce-sync-for-quickbooks-desktop/shipping/shipping-as-a-line-item-or-a-subtotal-field">Shipping: As a line item or subtotal field?</a></li>
      </ul>
    </li>
    '.__mwqbd_need_help_common().'
  </ul>

  <ul id="guide-accordion" class="guide-accordion settings order" style="display: none;">
    <li>
      <div class="acco-link">Getting Started</div>
      <ul class="guide-submenu">
        <li><a target="_blank" href="https://docs.myworks.software/woocommerce-sync-for-quickbooks-desktop/getting-started/settings#order">Configuring order settings</a></li>
      </ul>
    </li>
    <li>
      <div class="acco-link">Common Questions</div>
      <ul class="guide-submenu">
        <li><a target="_blank" href="https://docs.myworks.software/woocommerce-sync-for-quickbooks-desktop/common-questions/should-i-push-woocommerce-orders-as-a-sales-receipt-or-an-invoice">Should I push WooCommerce Orders as Invoices or Sales Receipts?</a></li>
        <li><a target="_blank" href="https://docs.myworks.software/woocommerce-sync-for-quickbooks-desktop/orders/handling-syncing-for-orders-not-instantly-paid-cash-on-delivery-bacs-etc">How can I handle syncing for orders not instantly paid?</a></li>
        <li><a target="_blank" href="https://docs.myworks.software/woocommerce-sync-for-quickbooks-desktop/common-questions/how-are-woocommerce-orders-placed-by-guests-synced-to-quickbooks-online">How are WooCommerce Orders Placed by Guests Synced to QuickBooks?</a></li>
        <li><a target="_blank" href="https://docs.myworks.software/woocommerce-sync-for-quickbooks-desktop/common-questions/what-if-my-woocommerce-order-numbers-already-exist-in-quickbooks-online-as-different-orders">What if my WooCommerce order numbers already exist in QuickBooks Desktop – as different orders?</a></li>
      </ul>
    </li>
    <li>
      <div class="acco-link">Troubleshooting</div>
      <ul class="guide-submenu">
         <li><a target="_blank" href="https://docs.myworks.software/woocommerce-sync-for-quickbooks-desktop/common-questions/what-if-my-woocommerce-order-numbers-already-exist-in-quickbooks-online-as-different-orders">Handling duplicate order numbers between WooCommerce and QuickBooks</a></li>
      </ul>
    </li>
    '.__mwqbd_need_help_common().'
  </ul>

  <ul id="guide-accordion" class="guide-accordion settings tax" style="display: none;">
    <li>
      <div class="acco-link">Getting Started</div>
      <ul class="guide-submenu">
        <li><a target="_blank" href="https://docs.myworks.software/woocommerce-sync-for-quickbooks-desktop/getting-started/settings#tax">Configuring taxes</a></li>
      </ul>
    </li>
    <li>
      <div class="acco-link">Common Questions</div>
      <ul class="guide-submenu">
        <li><a target="_blank" href="https://docs.myworks.software/woocommerce-sync-for-quickbooks-desktop/compatibility-addons/avalara-avatax">Are you compatible with Avalara AvaTax?</a></li>
      </ul>
    </li>
    <li>
      <div class="acco-link">Additional Resources</div>
      <ul class="guide-submenu">
         <li><a target="_blank" href="https://docs.myworks.software/woocommerce-sync-for-quickbooks-desktop/taxes/mapping-taxes-best-practices">Mapping Taxes</a></li>
      </ul>
    </li>
    '.__mwqbd_need_help_common().'
  </ul>

  <ul id="guide-accordion" class="guide-accordion settings map" style="display: none;">
    <li>
      <div class="acco-link">Getting Started</div>
      <ul class="guide-submenu">
        <li><a target="_blank" href="https://docs.myworks.software/woocommerce-sync-for-quickbooks-desktop/getting-started/settings#mapping">Configuring mapping settings</a></li>
      </ul>
    </li>
    <li>
      <div class="acco-link">Common Questions</div>
      <ul class="guide-submenu">
        <li><a target="_blank" href="https://docs.myworks.software/woocommerce-sync-for-quickbooks-desktop/common-questions/how-are-mappings-handled-on-an-ongoing-basis">How are mappings handled on an ongoing basis?</a></li>
        <li><a target="_blank" href="https://docs.myworks.software/woocommerce-sync-for-quickbooks-desktop/common-questions/what-does-mapping-mean-and-why-do-i-have-to-map-my-customers-and-products">What does “Mapping” mean, and why do I have to Map my customers and products?</a></li>
      </ul>
    </li>
    '.__mwqbd_need_help_common().'
  </ul>

  <ul id="guide-accordion" class="guide-accordion settings pull" style="display: none;">
    <li>
      <div class="acco-link">Getting Started</div>
      <ul class="guide-submenu">
        <li><a target="_blank" href="https://docs.myworks.software/woocommerce-sync-for-quickbooks-desktop/getting-started/settings#pull">Configuring pull settings</a></li>
      </ul>
    </li>
    '.__mwqbd_need_help_common().'
  </ul>

  <ul id="guide-accordion" class="guide-accordion settings sync" style="display: none;">
    <li>
      <div class="acco-link">Getting Started</div>
      <ul class="guide-submenu">
        <li><a target="_blank" href="https://docs.myworks.software/woocommerce-sync-for-quickbooks-desktop/getting-started/how-does-syncing-work-with-our-plugin">About syncing</a></li>
        <li><a target="_blank" href="https://docs.myworks.software/woocommerce-sync-for-quickbooks-desktop/getting-started/settings#automatic-sync">Configuring automatic sync settings</a></li>
        <li><a target="_blank" href="https://docs.myworks.software/woocommerce-sync-for-quickbooks-desktop/getting-started/getting-started-with-queue-syncing">About queue sync</a></li>
      </ul>
    </li>
    <li>
      <div class="acco-link">Common Questions</div>
      <ul class="guide-submenu">
        <li><a target="_blank" href="https://docs.myworks.software/woocommerce-sync-for-quickbooks-desktop/getting-started/getting-started-with-queue-syncing#turning-on-queue-syncing">How can I enable queue sync?</a></li>
      </ul>
    </li>
    '.__mwqbd_need_help_common().'
  </ul>

  <ul id="guide-accordion" class="guide-accordion settings misc" style="display: none;">
    <li>
      <div class="acco-link">Getting Started</div>
      <ul class="guide-submenu">
        <li><a target="_blank" href="https://docs.myworks.software/woocommerce-sync-for-quickbooks-desktop/getting-started/settings#miscellaneous">Configuring miscellaneous settings</a></li>
      </ul>
    </li>
    '.__mwqbd_need_help_common().'
  </ul>';
  return $HTML;
}

function __mwqbd_default_help(){
 $tab = (isset($_GET['tab']))?$_GET['tab']:'';
  $HTML = '<ul id="guide-accordion" class="guide-accordion">
    <li>
      <div class="acco-link">Need help on this?</div>
      <ul class="guide-submenu">
        <li><a href="#">Please contact our support anytime!</a></li>
        <li><a href="mailto:support@myworks.design">support@myworks.design</a></li>
        <li><a href="#">Expect reply within 24 business hours!</a></li>
      </ul>
    </li>
  </ul>';
  return $HTML;
}

function __mwqbd_need_help_common(){
  $tab = (isset($_GET['tab']))?$_GET['tab']:'';
  $HTML = '<li>
      <div class="acco-link">Still need help?</div>
      <ul class="guide-submenu">
      <li><a target="_blank" href="https://docs.myworks.software/woocommerce-sync-for-quickbooks-desktop">View our documentation</a></li>
      <li><a target="_blank" href="https://myworks.design/account/submitticket.php?step=2&deptid=2">Open a support ticket</a></li>
         <li><a target="_blank" href="http://slack.myworks.design">Join our Slack channel</a></li>
        <li><a target="_blank" href="https://docs.myworks.software/woocommerce-sync-for-quickbooks-desktop">Live chat with us</a></li>
      </ul>
    </li>';
    return $HTML;
}
?>
<div class="dont-delete"></div>