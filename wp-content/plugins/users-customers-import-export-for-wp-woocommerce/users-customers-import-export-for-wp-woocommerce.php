<?php

/*
  Plugin Name: WordPress Users & WooCommerce Customers Import Export
  Plugin URI: https://wordpress.org/plugins/users-customers-import-export-for-wp-woocommerce/
  Description: Export and Import User/Customers details From and To your WordPress/WooCommerce.
  Author: WebToffee
  Author URI: https://www.webtoffee.com/product/wordpress-users-woocommerce-customers-import-export/
  Version: 2.0.8
  WC tested up to: 5.1.0
  Text Domain: users-customers-import-export-for-wp-woocommerce
  License: GPLv3
  License URI: https://www.gnu.org/licenses/gpl-3.0.html
 */



if ( !defined( 'ABSPATH' ) || !is_admin() ) {
	return;
}


// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define ( 'WT_U_IEW_PLUGIN_BASENAME', plugin_basename(__FILE__) );
define ( 'WT_U_IEW_PLUGIN_PATH', plugin_dir_path(__FILE__) );
define ( 'WT_U_IEW_PLUGIN_URL', plugin_dir_url(__FILE__));
define ( 'WT_U_IEW_PLUGIN_FILENAME', __FILE__);
if ( ! defined( 'WT_IEW_PLUGIN_ID_BASIC' ) ) {
    define ( 'WT_IEW_PLUGIN_ID_BASIC', 'wt_import_export_for_woo_basic');
}
define ( 'WT_U_IEW_PLUGIN_NAME','User Import Export for WordPress/WooCommerce');
define ( 'WT_U_IEW_PLUGIN_DESCRIPTION','Import and Export User From and To your WordPress/WooCommerce Store.');

if ( ! defined( 'WT_IEW_DEBUG_BASIC' ) ) {
    define ( 'WT_IEW_DEBUG_BASIC', false );
}
/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'WT_U_IEW_VERSION', '2.0.8' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wt-import-export-for-woo-activator.php
 */
function activate_wt_import_export_for_woo_basic_user() {
//        if(is_plugin_active('wt-import-export-for-woo-user/wt-import-export-for-woo-user.php')){           
//            deactivate_plugins( basename( __FILE__ ) );
//            wp_die(
//                    '<p>'.__("Is everything fine? You already have the Premium version installed in your website. For any issues, kindly raise a ticket via <a target='_blank' href='https://www.webtoffee.com/support/'>support</a>")
//                    . '</p> <a href="' . admin_url( 'plugins.php' ) . '">' . __( 'go back') . '</a>'
//            );
//        }    
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wt-import-export-for-woo-activator.php';
	Wt_Import_Export_For_Woo_Basic_Activator_User::activate();
}



/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wt-import-export-for-woo-deactivator.php
 */
function deactivate_wt_import_export_for_woo_basic_user() {
        
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wt-import-export-for-woo-deactivator.php';
	Wt_Import_Export_For_Woo_Basic_Deactivator_User::deactivate();
}

register_activation_hook( __FILE__, 'activate_wt_import_export_for_woo_basic_user' );
register_deactivation_hook( __FILE__, 'deactivate_wt_import_export_for_woo_basic_user' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wt-import-export-for-woo.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wt_import_export_for_woo_basic_user() {
    
    if ( ! defined( 'WT_IEW_BASIC_STARTED' ) ) {
        define ( 'WT_IEW_BASIC_STARTED', 1);
	$plugin = new Wt_Import_Export_For_Woo_Basic();
	$plugin->run();                
    }        
}
/** this added for a temporary when a plugin update with the option upload zip file. need to remove this after some version release */
 if(!get_option('wt_u_iew_is_active'))
 {   
     update_option('wt_user_show_legecy_menu',1);
     activate_wt_import_export_for_woo_basic_user();
 }

if(get_option('wt_u_iew_is_active'))
{        
    run_wt_import_export_for_woo_basic_user();        
}

/* Plugin page links */
add_filter('plugin_action_links_'.plugin_basename(__FILE__), 'wt_uiew_plugin_action_links_basic_user');

function wt_uiew_plugin_action_links_basic_user($links)
{
	if(defined('WT_IEW_PLUGIN_ID_BASIC')) /* main plugin is available */
	{
		$links[] = '<a href="'.admin_url('admin.php?page='.WT_IEW_PLUGIN_ID_BASIC).'">'.__('Settings').'</a>';
	}

	$links[] = '<a href="https://www.webtoffee.com/user-import-export-plugin-wordpress-user-guide/" target="_blank">'.__('Documentation').'</a>';
        $links[] = '<a target="_blank" href="https://wordpress.org/support/plugin/users-customers-import-export-for-wp-woocommerce/">' . __('Support') . '</a>';
        $links[] = '<a target="_blank" href="https://www.webtoffee.com/product/wordpress-users-woocommerce-customers-import-export/?utm_source=free_plugin_listing&utm_medium=user_imp_exp_basic&utm_campaign=User_Import_Export&utm_content='.WT_U_IEW_VERSION.'" style="color:#3db634;">' . __('Premium Upgrade') . '</a>';        
        if (array_key_exists('deactivate', $links)) {
            $links['deactivate'] = str_replace('<a', '<a class="userimport-deactivate-link"', $links['deactivate']);
        }
	return $links;
}

/*
 *  Displays update information for a plugin. 
 */
function wt_users_customers_import_export_for_wp_woocommerce_update_message( $data, $response )
{
    if(isset( $data['upgrade_notice']))
    {
        add_action( 'admin_print_footer_scripts','wt_users_customers_imex_plugin_screen_update_js');
            $msg=str_replace(array('<p>','</p>'),array('<div>','</div>'),$data['upgrade_notice']);
            echo '<style type="text/css">
            #users-customers-import-export-for-wp-woocommerce-update .update-message p:last-child{ display:none;}     
            #users-customers-import-export-for-wp-woocommerce-update ul{ list-style:disc; margin-left:30px;}
            .wf-update-message{ padding-left:30px;}
            </style>
            <div class="update-message wf-update-message">'. wpautop($msg).'</div>';
    }
}
add_action( 'in_plugin_update_message-users-customers-import-export-for-wp-woocommerce/users-customers-import-export-for-wp-woocommerce.php', 'wt_users_customers_import_export_for_wp_woocommerce_update_message', 10, 2 );

if(!function_exists('wt_users_customers_imex_plugin_screen_update_js'))
{
    function wt_users_customers_imex_plugin_screen_update_js()
    {
        ?>
        <script>
            ( function( $ ){
                var update_dv=$( '#users-customers-import-export-for-wp-woocommerce-update');
                update_dv.find('.wf-update-message').next('p').remove();
                update_dv.find('a.update-link:eq(0)').click(function(){
                    $('.wf-update-message').remove();
                });
            })( jQuery );
        </script>
        <?php
    }
}
// uninstall feedback catch
include_once plugin_dir_path( __FILE__ ) . 'includes/class-wt-userimport-uninstall-feedback.php';



// add dismissble banner for legecy menu
include_once plugin_dir_path( __FILE__ ) . 'includes/class-wt-legecy-menu-moved.php';
$user_egecy_menu = new wt_legecy_menu_moved('user');
$user_egecy_menu->plugin_title = "User Import Export";
$user_egecy_menu->old_menu = "Users > User Import Export";
$user_egecy_menu->banner_message = sprintf(__("We have introduced a new main menu %sWebToffee Import Export(basic)%s for the %s plugin. Click the button below or dismiss this banner to remove the old menu from %s."),'<b>','</b>', $user_egecy_menu->plugin_title, $user_egecy_menu->old_menu);
$user_egecy_menu->old_menu_params = array(array('parent_slug'=>'users.php', 'menu_title'=>'User Import Export', 'capability'=>'list_users'),
                            array('parent_slug'=>'woocommerce', 'menu_title'=>'Customer Import Export', 'capability'=>'manage_woocommerce')
                            );

include_once 'user_import_export_review_request.php';
