<?php

namespace Objectiv\Plugins\Checkout\Core;

use Objectiv\Plugins\Checkout\Main;
use Objectiv\Plugins\Checkout\Stats\StatCollection;

/**
 * Class Admin
 *
 * @link objectiv.co
 * @since 1.0.0
 * @package Objectiv\Plugins\Checkout\Core
 * @author Clifton Griffin <clif@checkoutwc.com>
 */
class Admin {

	/**
	 * @since 1.0.0
	 * @access public
	 * @var object $plugin_instance The plugin instance
	 */
	public $plugin_instance;

	/**
     * @since 1.0.0
     * @access public
	 * @var object $tabs The tabs for the admin navigation
	 */
	public $tabs;

	/**
	 *
	 */
	private $_license_activations;

	/**
	 * Admin constructor.
	 *
     * @since 1.0.0
     * @access public
	 * @param Main $plugin
	 */
	public function __construct( $plugin ) {
		$this->plugin_instance = $plugin;
	}

	/**
	 * Initializes the admin backend
     *
     * @since 1.0.0
     * @access public
	 */
	public function run() {
		// Run this as early as we can to maximize integrations
		add_action(
			'plugins_loaded', function() {
			    // Adds the plugins hooks
			    $this->start();
		    }, 1
		);
	}

	public function start() {
		// Admin Menu
		add_action('admin_menu', array($this, 'admin_menu'), 100 );

		// Key Nag
		add_action('admin_menu', array($this, 'add_key_nag'), 11);

		// Enqueue Admin Scripts
		add_action( 'admin_enqueue_scripts', array($this, 'admin_scripts'), 1000 );

		// Admin notice
		add_action('admin_notices', array($this, 'add_notice_key_nag') );

		// Compatibility nag
		add_action('admin_notices', array($this, 'add_compatibility_nag') );

		// Welcome notice
		add_action('admin_notices', array($this, 'add_welcome_notice') );

		// Welcome redirect
		add_action( 'admin_init', array($this, 'welcome_screen_do_activation_redirect') );

		// Add settings link
		add_filter( 'plugin_action_links_' . plugin_basename( CFW_MAIN_FILE ), array( $this, 'add_action_links' ), 10, 1 );

		// Migrate settings
		add_action( 'admin_init', array( $this, 'maybe_migrate_settings' ) );

		// Show shipping phone on order editor
		add_action( 'woocommerce_admin_order_data_after_shipping_address', array( $this, 'shipping_phone_display_admin_order_meta' ), 10, 1 );
    }

	/**
	 * The main admin menu setup
     *
     * @since 1.0.0
     * @access public
	 */
	public function admin_menu() {
		// Initiate tab object
		$this->tabs = new \WP_Tabbed_Navigation('');

		do_action( 'cfw_admin_tabs', $this->tabs );

		add_options_page( __( 'Checkout for WooCommerce', 'checkout-wc' ), __( 'CheckoutWC', 'checkout-wc' ), 'manage_options', 'cfw-settings', array($this, 'admin_page') );

		// Setup tabs
        $this->tabs->add_tab( __( 'General', 'checkout-wc' ), menu_page_url('cfw-settings', false) );
		$this->tabs->add_tab( __( 'Template', 'checkout-wc' ), add_query_arg( array('subpage' => 'templates'), menu_page_url('cfw-settings', false) ) );
		$this->tabs->add_tab( __( 'Design', 'checkout-wc' ), add_query_arg( array('subpage' => 'design'), menu_page_url('cfw-settings', false) ) );

		if ( has_filter( 'cfw_admin_addon_tabs') ) {
			$this->tabs->add_tab( __( 'Addons', 'checkout-wc' ), add_query_arg( array('subpage' => 'addons'), menu_page_url('cfw-settings', false) ) );
        }

		$this->tabs->add_tab( __( 'License', 'checkout-wc' ), add_query_arg( array('subpage' => 'license'), menu_page_url('cfw-settings', false) ) );
		$this->tabs->add_tab( __( 'Support', 'checkout-wc' ), add_query_arg( array('subpage' => 'support'), menu_page_url('cfw-settings', false) ) );
		$this->tabs->add_tab( __( 'Recommended Plugins', 'checkout-wc' ), add_query_arg( array('subpage' => 'recommended_plugins'), menu_page_url('cfw-settings', false) ) );
	}

	/**
	 * The admin page wrap
     *
     * @since 1.0.0
     * @access public
	 */
	public function admin_page() {
	    // Get the current tab function
	    $current_tab_function = $this->get_current_tab() === false ? 'general_tab' : $this->get_current_tab() . "_tab";

	    // Get the object to call the added tab on
	    $callable = apply_filters('cfw_active_admin_settings_tab_function', array( $this, $current_tab_function), $current_tab_function );

	    $this->_license_activations = $this->plugin_instance->get_updater()->get_license_activation_limit();

		?>
        <script type="text/javascript">!function(e,t,n){function a(){var e=t.getElementsByTagName("script")[0],n=t.createElement("script");n.type="text/javascript",n.async=!0,n.src="https://beacon-v2.helpscout.net",e.parentNode.insertBefore(n,e)}if(e.Beacon=n=function(t,n,a){e.Beacon.readyQueue.push({method:t,options:n,data:a})},n.readyQueue=[],"complete"===t.readyState)return a();e.attachEvent?e.attachEvent("onload",a):e.addEventListener("load",a,!1)}(window,document,window.Beacon||function(){});</script>
        <script type="text/javascript">window.Beacon('init', '355a5a54-eb9d-4b64-ac5f-39c95644ad36')</script>
		<div class="wrap about-wrap" style="margin-left:2px;">

            <h1><?php _e('Checkout for WooCommerce', 'checkout-wc'); ?></h1>
            <p class="about-text"><?php _e( 'Checkout for WooCommerce provides a beautiful, conversion optimized checkout template for WooCommerce.' , 'checkout-wc' ); ?></p>
        </div>

        <div class="wrap">
            <?php $this->tabs->display_tabs(); ?>

            <?php is_callable( $callable ) ? call_user_func( $callable ) : null; ?>
		</div>
		<?php
	}

	/**
	 * The general tab
     *
     * @since 1.0.0
     * @access public
	 */
	public function general_tab() {
	    $stat_collection          = StatCollection::instance();
	    $thank_you_order_statuses = false === $this->plugin_instance->get_settings_manager()->get_setting( 'thank_you_order_statuses' ) ? array() : $this->plugin_instance->get_settings_manager()->get_setting( 'thank_you_order_statuses' );
	    ?>
        <form name="settings" id="mg_gwp" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
			<?php $this->plugin_instance->get_settings_manager()->the_nonce(); ?>
            <table class="form-table">
                <tbody>
                    <tr>
                        <th scope="row" valign="top">
                            <label for="<?php echo $this->plugin_instance->get_settings_manager()->get_field_name('enable'); ?>"><?php _e('Enable / Disable', 'checkout-wc'); ?></label>
                        </th>
                        <td>
                            <input type="hidden" name="<?php echo $this->plugin_instance->get_settings_manager()->get_field_name('enable'); ?>" value="no" />
                            <label><input type="checkbox" name="<?php echo $this->plugin_instance->get_settings_manager()->get_field_name('enable'); ?>" id="<?php echo $this->plugin_instance->get_settings_manager()->get_field_name('enable'); ?>" value="yes" <?php if ( $this->plugin_instance->get_settings_manager()->get_setting('enable') == "yes" ) echo "checked"; ?> /> <?php _e('Use Checkout for WooCommerce Template', 'checkout-wc'); ?></label>
                            <p><span class="description"><?php _e('Enable or disable Checkout for WooCommerce theme. (NOTE: Theme is always enabled for admin users.)', 'checkout-wc'); ?></span></p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row" valign="top">
                            <label for="<?php echo $this->plugin_instance->get_settings_manager()->get_field_name('header_scripts'); ?>"><?php _e('Header Scripts', 'checkout-wc'); ?></label>
                        </th>
                        <td>
		                    <?php wp_editor( stripslashes_deep( $this->plugin_instance->get_settings_manager()->get_setting('header_scripts') ), sanitize_title_with_dashes( $this->plugin_instance->get_settings_manager()->get_field_name('header_scripts') ), array('textarea_rows' => 6, 'quicktags' => false, 'media_buttons' => false, 'textarea_name' => $this->plugin_instance->get_settings_manager()->get_field_name('header_scripts'), 'tinymce' => false ) ); ?>
                            <p>
                                <span class="description">
				                    <?php _e('This code will output immediately before the closing <code>&lt;/head&gt;</code> tag in the document source.', 'checkout-wc'); ?>
                                </span>
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row" valign="top">
                            <label for="<?php echo $this->plugin_instance->get_settings_manager()->get_field_name('footer_scripts'); ?>"><?php _e('Footer Scripts', 'checkout-wc'); ?></label>
                        </th>
                        <td>
		                    <?php wp_editor( stripslashes_deep( $this->plugin_instance->get_settings_manager()->get_setting('footer_scripts') ), sanitize_title_with_dashes( $this->plugin_instance->get_settings_manager()->get_field_name('footer_scripts') ), array('textarea_rows' => 6, 'quicktags' => false, 'media_buttons' => false, 'textarea_name' => $this->plugin_instance->get_settings_manager()->get_field_name('footer_scripts'), 'tinymce' => false ) ); ?>
                            <p>
                                <span class="description">
				                    <?php _e('This code will output immediately before the closing <code>&lt;/body&gt;</code> tag in the document source.', 'checkout-wc'); ?>
                                </span>
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <?php
                            $tracking_field_name = $this->plugin_instance->get_settings_manager()->get_field_name('allow_tracking');
                            $tracking_value = $this->plugin_instance->get_settings_manager()->get_setting('allow_tracking');
                        ?>
                        <th scope="row" valign="top">
                            <label for="<?php echo $tracking_field_name; ?>"><?php _e('Enable Usage Tracking', 'checkout-wc'); ?></label>
                        </th>
                        <td>
                            <input type="hidden" name="<?php echo $tracking_field_name; ?>" value="0" />
                            <label for="<?php echo $tracking_field_name; ?>">
                                <input type="checkbox" name="<?php echo $tracking_field_name; ?>" id="<?php echo $tracking_field_name; ?>" value="1" <?php if ( $tracking_value == 1 ) echo "checked"; ?> />
								<?php _e('Allow Checkout for WooCommerce to track plugin usage?', 'checkout-wc'); ?>
                            </label>
                        </td>
                    </tr>

                    <?php if( false && defined('CFW_DEV_MODE') && CFW_DEV_MODE ): ?>
                    <tr>
                        <th scope="row" valign="top">
                            <label for="#cfw-stat-collection-testing"><?php _e('Stat Collection Data Viewer', 'checkout-wc'); ?></label>
                        </th>
                        <td>
							<?php
                                $stats = $this->plugin_instance->get_stat_collection();
							    $stats->setup_data();
                            ?>
                            <div>
                                <?php
								    d($stats->get_data());
                                ?>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <h3><?php _e( 'Premium Features', 'checkout-wc' ); ?></h3>

            <?php $this->maybe_display_upgrade_required_notice(); ?>

            <table class="form-table">
                <tbody>
                    <!--- Cart Editing -->
                    <tr>
                        <th scope="row" valign="top">
                            <label for="enable_cart_editing"><?php _e('Cart Editing', 'checkout-wc'); ?></label>
                        </th>
                        <td>
                            <input type="hidden" name="<?php echo $this->plugin_instance->get_settings_manager()->get_field_name('enable_cart_editing'); ?>" value="no" />
                            <label><input type="checkbox" name="<?php echo $this->plugin_instance->get_settings_manager()->get_field_name('enable_cart_editing'); ?>" id="enable_cart_editing" value="yes" <?php if ( $this->plugin_instance->get_settings_manager()->get_setting('enable_cart_editing') == "yes" ) echo "checked"; ?> <?php if ( $this->_license_activations < 5 ): ?>disabled="disabled"<?php endif; ?> /> <?php _e('Enable cart editing.', 'checkout-wc'); ?></label>
                            <p><span class="description"><?php _e('Enable or disable cart editing feature. Allows customer to remove or adjust quantity of cart items.', 'checkout-wc'); ?></span></p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row" valign="top">
                            <label for="<?php echo sanitize_title_with_dashes( $this->plugin_instance->get_settings_manager()->get_field_name('cart_edit_empty_cart_redirect') ); ?>"><?php _e('Cart Editing Empty Cart Redirect', 'checkout-wc'); ?></label>
                        </th>
                        <td>
                            <input type="text" size="40" id="cart_edit_empty_cart_redirect" value="<?php echo esc_attr( $this->plugin_instance->get_settings_manager()->get_setting('cart_edit_empty_cart_redirect') ); ?>" name="<?php echo $this->plugin_instance->get_settings_manager()->get_field_name('cart_edit_empty_cart_redirect'); ?>" />
                            <p>
                                    <span class="description">
                                        <?php _e('URL to redirect to when customer empties cart from checkout page.', 'checkout-wc'); ?><br />
                                        <?php _e('If left blank, customer will be redirected to the cart page.', 'checkout-wc'); ?>
                                    </span>
                            </p>
                        </td>
                    </tr>

                    <!-- Order Pay -->
                    <tr>
                        <th scope="row" valign="top">
                            <label for="enable_order_pay"><?php _e('Order Pay', 'checkout-wc'); ?></label>
                        </th>
                        <td>
                            <input type="hidden" name="<?php echo $this->plugin_instance->get_settings_manager()->get_field_name('enable_order_pay'); ?>" value="no" />
                            <label><input type="checkbox" name="<?php echo $this->plugin_instance->get_settings_manager()->get_field_name('enable_order_pay'); ?>" id="enable_order_pay" value="yes" <?php if ( $this->plugin_instance->get_settings_manager()->get_setting('enable_order_pay') == "yes" ) echo "checked"; ?> <?php if ( $this->_license_activations < 5 ): ?>disabled="disabled"<?php endif; ?> /> <?php _e('Enable support for order pay page.', 'checkout-wc'); ?></label>
                            <p><span class="description"><?php _e('Use checkout template for order pay page.', 'checkout-wc'); ?></span></p>
                        </td>
                    </tr>

                    <!-- Thank You Page -->
                    <tr>
                        <th scope="row" valign="top">
                            <label for="enable_thank_you_page"><?php _e('Thank You Page', 'checkout-wc'); ?></label>
                        </th>
                        <td>
                            <input type="hidden" name="<?php echo $this->plugin_instance->get_settings_manager()->get_field_name('enable_thank_you_page'); ?>" value="no" />
                            <label><input type="checkbox" name="<?php echo $this->plugin_instance->get_settings_manager()->get_field_name('enable_thank_you_page'); ?>" id="enable_thank_you_page" value="yes" <?php if ( $this->plugin_instance->get_settings_manager()->get_setting('enable_thank_you_page') == "yes" ) echo "checked"; ?> <?php if ( $this->_license_activations < 5 ): ?>disabled="disabled"<?php endif; ?> /> <?php _e('Enable support for thank you page.', 'checkout-wc'); ?></label>
                            <p><span class="description"><?php _e('Enable thank you page / order received template. This template is also used when viewing orders in My Account.', 'checkout-wc'); ?></span></p>
                        </td>
                    </tr>

                    <!--- Order Statuses -->
                    <tr>
                        <th scope="row" valign="top">
                            <label for="thank_you_order_statuses"><?php _e('Order Statuses', 'checkout-wc'); ?></label>
                        </th>
                        <td>
                            <input type="hidden" name="<?php echo $this->plugin_instance->get_settings_manager()->get_field_name('thank_you_order_statuses'); ?>" value="no" />
                            <label>
                                <select multiple class="wc-enhanced-select" name="<?php echo $this->plugin_instance->get_settings_manager()->get_field_name('thank_you_order_statuses'); ?>[]" id="thank_you_order_statuses" <?php if ( $this->_license_activations < 5 ): ?>disabled="disabled"<?php endif; ?>>
                                    <?php foreach( wc_get_order_statuses() as $key => $status ): ?>
                                        <option value="<?php echo esc_attr( $key ); ?>" <?php if ( in_array( $key, $thank_you_order_statuses ) ) echo 'selected'; ?>><?php echo esc_html( $status ); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </label>
                            <p><span class="description"><?php _e('The statuses to show on the thank you page.', 'checkout-wc'); ?></span></p>
                        </td>
                    </tr>

                    <!--- Map Embed -->
                    <tr>
                        <th scope="row" valign="top">
                            <label for="enable_map_embed"><?php _e('Map Embed', 'checkout-wc'); ?></label>
                        </th>
                        <td>
                            <input type="hidden" name="<?php echo $this->plugin_instance->get_settings_manager()->get_field_name('enable_map_embed'); ?>" value="no" />
                            <label><input type="checkbox" name="<?php echo $this->plugin_instance->get_settings_manager()->get_field_name('enable_map_embed'); ?>" id="enable_map_embed" value="yes" <?php if ( $this->plugin_instance->get_settings_manager()->get_setting('enable_map_embed') == "yes" ) echo "checked"; ?> <?php if ( $this->_license_activations < 5 ): ?>disabled="disabled"<?php endif; ?> /> <?php _e('Enable map embed.', 'checkout-wc'); ?></label>
                            <p><span class="description"><?php _e('Enable or disable map embed on thank you page. Requires Google API key.', 'checkout-wc'); ?></span></p>
                        </td>
                    </tr>

                    <!--- Address Autocomplete -->
                    <tr>
                        <th scope="row" valign="top">
                            <label for="enable_address_autocomplete"><?php _e('Address Autocomplete', 'checkout-wc'); ?></label>
                        </th>
                        <td>
                            <input type="hidden" name="<?php echo $this->plugin_instance->get_settings_manager()->get_field_name('enable_address_autocomplete'); ?>" value="no" />
                            <label><input type="checkbox" name="<?php echo $this->plugin_instance->get_settings_manager()->get_field_name('enable_address_autocomplete'); ?>" id="enable_address_autocomplete" value="yes" <?php if ( $this->plugin_instance->get_settings_manager()->get_setting('enable_address_autocomplete') == "yes" ) echo "checked"; ?> <?php if ( $this->_license_activations < 5 ): ?>disabled="disabled"<?php endif; ?> /> <?php _e('Enable address autocomplete.', 'checkout-wc'); ?></label>
                            <p><span class="description"><?php _e('Enable or disable address autocomplete feature. Requires Google API key.', 'checkout-wc'); ?></span></p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row" valign="top">
                            <label for="<?php echo sanitize_title_with_dashes( $this->plugin_instance->get_settings_manager()->get_field_name('google_places_api_key') ); ?>"><?php _e('Google API Key', 'checkout-wc'); ?></label>
                        </th>
                        <td>
                            <input type="password" size="40" id="google_places_api_key" value="<?php echo esc_attr( $this->plugin_instance->get_settings_manager()->get_setting('google_places_api_key') ); ?>" name="<?php echo $this->plugin_instance->get_settings_manager()->get_field_name('google_places_api_key'); ?>" />
                            <p>
                                <span class="description">
                                    <?php _e('Google API Key. Available in the <a target="_blank" href="https://developers.google.com/places/web-service/get-api-key">Google Cloud Platform Console</a>.', 'checkout-wc'); ?>
                                </span>
                            </p>
                        </td>
                    </tr>
                </tbody>
            </table>

			<?php submit_button(); ?>
        </form>
        <?php
    }

    function maybe_display_upgrade_required_notice( $min_activations = 5 ) {
	    if ( $this->_license_activations < $min_activations ): ?>
            <div class='cfw-notification-message'>
                <strong><?php _e('License Upgrade Required', 'checkout-wc' ); ?></strong> &mdash; <?php _e('A Growth or Developer License is required to enable these features.', 'checkout-wc' ); ?>
			    <?php echo sprintf( __('You can upgrade your license in <a target="_blank" href="%1$s">My Account</a>. For help upgrading your license, <a target="_blank" href="%2$s">click here.</a>', 'checkout-wc'), 'https://www.checkoutwc.com/account/',  'https://kb.checkoutwc.com/article/53-upgrading-your-license'); ?>
            </div>
	    <?php endif;
    }

	/**
	 * The template tab
	 *
	 * @since 2.0.0
	 * @access public
	 */
    public function templates_tab() {
	    $templates = $this->plugin_instance->get_templates_manager()->getAvailableTemplates();
	    $active_template = $this->plugin_instance->get_settings_manager()->get_setting('active_template');
	    ?>
        <h3><?php _e( 'Templates', 'checkout-wc' ); ?></h3>

        <div class="theme-browser">
            <div class="themes wp-clearfix">
                <?php foreach( $templates as $template ):
                    $screenshot = $template->get_template_uri() . '/screenshot.png';

                    $active = ($active_template == $template->get_slug() ) ? true : false;
                    ?>
                    <?php add_thickbox(); ?>
                    <div class="theme <?php if ( $active ) echo "active"; ?>">
                        <div class="theme-screenshot">
                            <a href="#TB_inline?width=1200&height=900&inlineId=theme-preview-<?php echo $template->get_slug(); ?>" class="thickbox">
                                <img class="theme-screenshot-img" src="<?php echo $screenshot; ?>" />
                            </a>
                            <div id="theme-preview-<?php echo $template->get_slug(); ?>" style="display:none;">
                                <img src="<?php echo $screenshot; ?>" />
                            </div>
                        </div>
                        <div class="theme-id-container">

                            <h2 class="theme-name" id="<?php echo $template->get_slug(); ?>-name"><strong><?php if ( $active ) _e('Active: '); ?></strong><?php echo $template->get_name(); ?></h2>


                            <?php if ( ! $active ): ?>
                                <div class="theme-actions">
                                    <form name="settings" id="mg_gwp" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
                                        <input type="hidden" name="<?php echo $this->plugin_instance->get_settings_manager()->get_field_name('active_template'); ?>" value="<?php echo $template->get_slug(); ?>" />
                                        <?php $this->plugin_instance->get_settings_manager()->the_nonce(); ?>
                                        <?php submit_button( __('Activate', 'checkout-wc'), 'button-secondary', $name = 'submit', $wrap = false); ?>
                                    </form>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
	    <?php
    }

	/**
	 * The design tab
     *
     * @since 1.0.0
     * @access public
	 */
    public function design_tab() {
	    ?>
        <form name="settings" id="mg_gwp" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
		    <?php $this->plugin_instance->get_settings_manager()->the_nonce(); ?>

            <h3><?php _e( 'Global Settings', 'checkout-wc' ); ?></h3>
            <p><?php _e( 'These settings apply to all themes.', 'checkout-wc' ) ;?></p>
            <table class="form-table">
                <tbody>
                    <tr>
                        <th scope="row" valign="top">
                            <?php _e('Logo', 'checkout-wc'); ?>
                        </th>
                        <td>
                            <div class='image-preview-wrapper'>
                                <img id='image-preview' src='<?php echo wp_get_attachment_url( $this->plugin_instance->get_settings_manager()->get_setting('logo_attachment_id') ); ?>' width='100' style='max-height: 100px; width: 100px;'>
                            </div>
                            <input id="upload_image_button" type="button" class="button" value="<?php _e( 'Upload image' ); ?>" />
                            <input type='hidden' name='<?php echo $this->plugin_instance->get_settings_manager()->get_field_name('logo_attachment_id'); ?>' id='logo_attachment_id' value="<?php echo $this->plugin_instance->get_settings_manager()->get_setting('logo_attachment_id'); ?>">

                            <a class="delete-custom-img button secondary-button"><?php _e('Clear Logo', 'checkout-wc'); ?></a>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row" valign="top">
                            <label for="<?php echo sanitize_title_with_dashes( $this->plugin_instance->get_settings_manager()->get_field_name('footer_text') ); ?>"><?php _e('Footer Text', 'checkout-wc'); ?></label>
                        </th>
                        <td>
                            <?php wp_editor( $this->plugin_instance->get_settings_manager()->get_setting('footer_text'), sanitize_title_with_dashes( $this->plugin_instance->get_settings_manager()->get_field_name('footer_text') ), array('textarea_rows' => 5, 'textarea_name' => $this->plugin_instance->get_settings_manager()->get_field_name('footer_text'), 'tinymce' => true ) ); ?>
                            <p>
                                        <span class="description">
                                            <?php _e('If left blank, a standard copyright notice will be displayed. Set to a single space to override this behavior.', 'checkout-wc'); ?>
                                        </span>
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row" valign="top">
                            <label for="<?php echo sanitize_title_with_dashes( $this->plugin_instance->get_settings_manager()->get_field_name('cart_summary_mobile_label') ); ?>"><?php _e('Cart Summary Mobile Label', 'checkout-wc'); ?></label>
                        </th>
                        <td>
                            <input type="text" value="<?php echo esc_attr( $this->plugin_instance->get_settings_manager()->get_setting('cart_summary_mobile_label') ); ?>" name="<?php echo $this->plugin_instance->get_settings_manager()->get_field_name('cart_summary_mobile_label'); ?>" />
                            <p>
                                <span class="description">
                                    <?php _e('Example: Show order summary and coupons', 'checkout-wc'); ?><br />
                                    <?php _e('If left blank, this default will be used: ', 'checkout-wc'); ?><?php _e( 'Show order summary', 'checkout-wc' ); ?>
                                </span>
                            </p>
                        </td>
                    </tr>
                </tbody>
            </table>

            <h3><?php _e( 'Theme Specific Settings', 'checkout-wc' ); ?></h3>

	        <?php
            $template_path = $this->plugin_instance->get_templates_manager()->getActiveTemplate()->get_slug();
            ?>
            <table class="form-table template-settings template-<?php echo $template_path; ?>">
                    <tbody>
                    <?php if ( $this->plugin_instance->get_templates_manager()->getActiveTemplate()->supports( 'header-background' ) ): ?>
                    <tr>
                        <th scope="row" valign="top">
                            <label for="<?php echo $this->plugin_instance->get_settings_manager()->get_field_name( 'header_background_color', array( $template_path ) ); ?>"><?php _e('Header Background Color', 'checkout-wc'); ?></label>
                        </th>
                        <td>
                            <input class="color-picker" type="text" name="<?php echo $this->plugin_instance->get_settings_manager()->get_field_name( 'header_background_color', array( $template_path ) ); ?>" value="<?php echo $this->plugin_instance->get_settings_manager()->get_setting( 'header_background_color', array( $template_path ) ); ?>" data-default-color="#ffffff" />
                        </td>
                    </tr>
                    <?php endif; ?>

                    <tr>
                        <th scope="row" valign="top">
                            <label for="<?php echo $this->plugin_instance->get_settings_manager()->get_field_name( 'header_text_color', array( $template_path ) ); ?>"><?php _e('Header Text Color', 'checkout-wc'); ?></label>
                        </th>
                        <td>
                            <input class="color-picker" type="text" name="<?php echo $this->plugin_instance->get_settings_manager()->get_field_name( 'header_text_color', array( $template_path ) ); ?>" value="<?php echo $this->plugin_instance->get_settings_manager()->get_setting( 'header_text_color', array( $template_path ) ); ?>" data-default-color="#2b2b2b" />
                        </td>
                    </tr>

                    <?php if ( $this->plugin_instance->get_templates_manager()->getActiveTemplate()->supports( 'footer-background' ) ): ?>
                    <tr>
                        <th scope="row" valign="top">
                            <label for="<?php echo $this->plugin_instance->get_settings_manager()->get_field_name( 'footer_background_color',  array( $template_path ) ); ?>"><?php _e('Footer Background Color', 'checkout-wc'); ?></label>
                        </th>
                        <td>
                            <input class="color-picker" type="text" name="<?php echo $this->plugin_instance->get_settings_manager()->get_field_name( 'footer_background_color',  array( $template_path ) ); ?>" value="<?php echo $this->plugin_instance->get_settings_manager()->get_setting( 'footer_background_color',  array( $template_path ) ); ?>" data-default-color="#ffffff" />
                        </td>
                    </tr>
                    <?php endif; ?>

                    <tr>
                        <th scope="row" valign="top">
                            <label for="<?php echo $this->plugin_instance->get_settings_manager()->get_field_name( 'footer_color',  array( $template_path ) ); ?>"><?php _e('Footer Text Color', 'checkout-wc'); ?></label>
                        </th>
                        <td>
                            <input class="color-picker" type="text" name="<?php echo $this->plugin_instance->get_settings_manager()->get_field_name( 'footer_color',  array( $template_path ) ); ?>" value="<?php echo $this->plugin_instance->get_settings_manager()->get_setting( 'footer_color',  array( $template_path ) ); ?>" data-default-color="#999999" />
                        </td>
                    </tr>

                    <?php if ( $this->plugin_instance->get_templates_manager()->getActiveTemplate()->supports( 'summary-background' ) ): ?>
                        <tr>
                            <th scope="row" valign="top">
                                <label for="<?php echo $this->plugin_instance->get_settings_manager()->get_field_name( 'summary_background_color', array( $template_path ) ); ?>"><?php _e('Summary Background Color', 'checkout-wc'); ?></label>
                            </th>
                            <td>
                                <input class="color-picker" type="text" name="<?php echo $this->plugin_instance->get_settings_manager()->get_field_name( 'summary_background_color', array( $template_path ) ); ?>" value="<?php echo $this->plugin_instance->get_settings_manager()->get_setting( 'summary_background_color', array( $template_path ) ); ?>" data-default-color="#fafafa" />
                            </td>
                        </tr>
                    <?php endif; ?>

                    <tr>
                        <th scope="row" valign="top">
                            <label for="<?php echo $this->plugin_instance->get_settings_manager()->get_field_name( 'button_color',  array( $template_path ) ); ?>"><?php _e('Primary Button Color', 'checkout-wc'); ?></label>
                        </th>
                        <td>
                            <input class="color-picker" type="text" name="<?php echo $this->plugin_instance->get_settings_manager()->get_field_name( 'button_color',  array( $template_path ) ); ?>" value="<?php echo $this->plugin_instance->get_settings_manager()->get_setting( 'button_color',  array( $template_path ) ); ?>" data-default-color="#e9a81d" />
                        </td>
                    </tr>

                    <tr>
                        <th scope="row" valign="top">
                            <label for="<?php echo $this->plugin_instance->get_settings_manager()->get_field_name( 'button_text_color',  array( $template_path ) ); ?>"><?php _e('Primary Button Text Color', 'checkout-wc'); ?></label>
                        </th>
                        <td>
                            <input class="color-picker" type="text" name="<?php echo $this->plugin_instance->get_settings_manager()->get_field_name( 'button_text_color',  array( $template_path ) ); ?>" value="<?php echo $this->plugin_instance->get_settings_manager()->get_setting( 'button_text_color',  array( $template_path ) ); ?>" data-default-color="#000000" />
                        </td>
                    </tr>

                    <tr>
                        <th scope="row" valign="top">
                            <label for="<?php echo $this->plugin_instance->get_settings_manager()->get_field_name( 'secondary_button_color',  array( $template_path ) ); ?>"><?php _e('Secondary Button Color', 'checkout-wc'); ?></label>
                        </th>
                        <td>
                            <input class="color-picker" type="text" name="<?php echo $this->plugin_instance->get_settings_manager()->get_field_name( 'secondary_button_color',  array( $template_path ) ); ?>" value="<?php echo $this->plugin_instance->get_settings_manager()->get_setting( 'secondary_button_color',  array( $template_path ) ); ?>" data-default-color="#999999" />
                        </td>
                    </tr>

                    <tr>
                        <th scope="row" valign="top">
                            <label for="<?php echo $this->plugin_instance->get_settings_manager()->get_field_name( 'secondary_button_text_color',  array( $template_path ) ); ?>"><?php _e('Secondary Button Text Color', 'checkout-wc'); ?></label>
                        </th>
                        <td>
                            <input class="color-picker" type="text" name="<?php echo $this->plugin_instance->get_settings_manager()->get_field_name( 'secondary_button_text_color',  array( $template_path ) ); ?>" value="<?php echo $this->plugin_instance->get_settings_manager()->get_setting( 'secondary_button_text_color', array( $template_path ) ); ?>" data-default-color="#ffffff" />
                        </td>
                    </tr>

                    <tr>
                        <th scope="row" valign="top">
                            <label for="<?php echo $this->plugin_instance->get_settings_manager()->get_field_name( 'link_color',  array( $template_path ) ); ?>"><?php _e('Link Color', 'checkout-wc'); ?></label>
                        </th>
                        <td>
                            <input class="color-picker" type="text" name="<?php echo $this->plugin_instance->get_settings_manager()->get_field_name( 'link_color',  array( $template_path )); ?>" value="<?php echo $this->plugin_instance->get_settings_manager()->get_setting( 'link_color',  array( $template_path ) ); ?>" data-default-color="#e9a81d" />
                        </td>
                    </tr>

                    <tr>
                        <th scope="row" valign="top">
                            <label for="<?php echo $this->plugin_instance->get_settings_manager()->get_field_name( 'custom_css',  array( $template_path ) ); ?>"><?php _e('Custom CSS', 'checkout-wc'); ?></label>
                        </th>
                        <td>
                            <?php wp_editor( $this->plugin_instance->get_settings_manager()->get_setting( 'custom_css',  array( $template_path ) ), sanitize_title_with_dashes( $this->plugin_instance->get_settings_manager()->get_field_name( 'custom_css',  array( $template_path ) ) ), array('textarea_rows' => 5, 'quicktags' => false, 'media_buttons' => false, 'textarea_name' => $this->plugin_instance->get_settings_manager()->get_field_name( 'custom_css',  array( $template_path ) ), 'tinymce' => false ) ); ?>
                            <p>
                                <span class="description">
                                    <?php _e('Add Custom CSS rules to fully control the appearance of the checkout template.', 'checkout-wc'); ?>
                                </span>
                            </p>
                        </td>
                    </tr>
                </tbody>
                </table>

		    <?php submit_button(); ?>
        </form>
        <?php
    }

    function addons_tab() {
	    /**
	     * Return an array of addon tabs
         *
         * 'foo' => array(
         *              'name'    => 'Foo',
         *              'function => callable,
         *          )
	     */
        $addon_tabs = apply_filters( 'cfw_admin_addon_tabs', array() );
        $current_addon_tab = isset( $_GET['addontab'] ) ? esc_attr( $_GET['addontab'] ) : key( $addon_tabs );
        $callable = $addon_tabs[ $current_addon_tab ][ 'function' ];

	    $array_keys = array_keys( $addon_tabs );

        if ( empty($addon_tabs) ) return;
        ?>
        <div class="wrap">
            <ul class="subsubsub">
                <?php foreach ( $addon_tabs as $id => $addon_tab ): ?>
                    <li>
                        <a href="<?php echo add_query_arg( array( 'addontab' => $id ) ); ?>" class="<?php if ( $id == $current_addon_tab ) echo 'current'; ?>" ><?php echo $addon_tab['name']; ?></a>
                        <?php if ( $id !== end( $array_keys ) ): ?>
                        |
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>

            <br class="clear">

	        <?php is_callable( $callable ) ? call_user_func( $callable ) : null; ?>
		</div>
        <?php
    }

	/**
	 * The license tab
     *
     * @since 1.0.0
     * @access public
	 */
    public function license_tab() {
	    $this->plugin_instance->get_updater()->admin_page();
    }

	/**
	 * The support tab
     *
     * @since 1.0.0
     * @access public
	 */
    public function support_tab() {
        ?>
        <h3><?php _e('Need help?', 'checkout-wc'); ?></h3>

        <p><?php _e('Excellent support is in our DNA.', 'checkout-wc'); ?></p>

	    <?php submit_button( __('Contact Support', 'checkout-wc'), 'primary', false, false, array('id'=> 'checkoutwc-support-button') ); ?>

        <script>
            jQuery("#checkoutwc-support-button").click(function() {
                Beacon("open");
            });
        </script>
        <?php
    }

	/**
	 * The recommended plugins tab
	 *
	 * @since 2.10.0
	 * @access public
	 */
	public function recommended_plugins_tab() {
	    $plugins = array();
	    $plugins[] = array(
            'slug'        => 'paypal-for-woocommerce',
            'url'         => 'https://www.angelleye.com/product/woocommerce-paypal-plugin/',
            'name'        => 'PayPal for WooCommerce',
            'description' => 'Upgrade the WooCommerce PayPal Gateway options available to your buyers for FREE!',
            'author'      => 'Angell EYE, LLC',
            'image'       => 'https://www.angelleye.com/wp-content/uploads/2014/02/paypal-for-woocommerce-thumbnail.jpg',
        );

	    $plugins[] = array(
            'slug'        => 'wp-sent-mail',
            'url'         => 'https://www.wpsentmail.com',
            'name'        => 'WP Sent Mail',
            'description' => 'A sent mail folder for WordPress. View every email your store sends, track opens, and re-send right from the dashboard.',
            'author'      => 'Objectiv',
            'image'       => 'https://www.checkoutwc.com/wp-content/uploads/2019/07/Smaller-Square-WPSM.jpg',
        );
	    ?>
		<p><?php _e('The WooCommerce ecosystem is full of fantastic plugins. Here are a few that work really well with CheckoutWC that we wholeheartedly recommend to our customers. (A few of these links may include affiliate tracking. This does not impact our decision to include a plugin.)' ); ?>
        <div class="recommended-plugin-list">
            <?php foreach( $plugins as $plugin_info ): ?>
                <?php $this->recommended_plugin_card( $plugin_info ); ?>
            <?php endforeach; ?>
        </div>
        <?php
	}

	function recommended_plugin_card( $plugin_info ) {
	    ?>
		<div class="plugin-card plugin-card-<?php echo $plugin_info['slug']; ?>">
            <div class="plugin-card-top">
                <div class="name column-name">
                    <h3>
                        <a target="_blank" href="<?php echo $plugin_info['url']; ?>">
                            <?php echo $plugin_info['name']; ?> <img src="<?php echo $plugin_info['image']; ?>" class="plugin-icon" alt="">
                        </a>
                    </h3>
                </div>
                <div class="action-links">
                    <ul class="plugin-action-buttons">
                        <li>
                            <a class="button" target="_blank"  href="<?php echo $plugin_info['url']; ?>" role="button"><?php _e('More Info'); ?></a></li>
                        </li>
                    </ul>
                </div>
                <div class="desc column-description">
                    <p><?php echo $plugin_info['description']; ?></p>
                    <p class="authors"> <cite><?php echo sprintf( cfw__( 'By %s' ), $plugin_info['author'] ); ?></cite></p>
                </div>
            </div>
        </div>
        <?php
    }

	/**
     * Retrieves the current tab
     *
     * @since 1.0.0
     * @access public
	 * @return bool
	 */
	public function get_current_tab() {
	    return empty($_GET['subpage']) ? false : $_GET['subpage'];
    }

	/**
	 * Retrieves the current tab
	 *
	 * @since 1.0.0
	 * @access public
	 * @return bool
	 */
	public function get_current_addon_tab() {
		return empty($_GET['addontab']) ? false : $_GET['addontab'];
	}

	/**
	 * Adds a notification that nags about the license key
     *
     * @since 1.0.0
     * @access public
	 */
	public function add_key_nag() {
		global $pagenow;

		if( $pagenow == 'plugins.php' ) {
			add_action( 'after_plugin_row_' . $this->plugin_instance->get_path_manager()->get_base(), array($this, 'after_plugin_row_message'), 10, 2 );
		}
	}

	/**
	 * @since 1.0.0
     * @access public
	 */
	public function after_plugin_row_message() {
		$key_status = $this->plugin_instance->get_updater()->get_field_value('key_status');

		if ( empty($key_status) ) return;

		if ( $key_status != "valid" ) {
			$current = get_site_transient( 'update_plugins' );
			if ( isset( $current->response[ plugin_basename(__FILE__) ] ) ) return;

			if ( is_network_admin() || ! is_multisite() ) {
				$wp_list_table = _get_list_table('WP_Plugins_List_Table');
				echo '<tr class="plugin-update-tr"><td colspan="' . $wp_list_table->get_column_count() . '" class="plugin-update colspanchange"><div class="update-message">';
				echo $this->keynag();
				echo '</div></td></tr>';
			}
		}
	}

	/**
     * @since 1.0.0
     * @access public
	 * @return string
	 */
	public function keynag() {
		return "<span style='color:red'>You're missing out on important updates because your license key is missing, invalid, or expired.</span>";
	}

	/**
	 * @since 1.0.0
     * @access public
	 */
	public function admin_scripts() {
	    // WooCommerce admin styles
		wp_enqueue_style( 'woocommerce_admin_styles' );

		// Add the admin stylesheet
	    wp_enqueue_style('objectiv-cfw-admin-styles', CFW_URL . "assets/admin/css/admin.css", array(), CFW_VERSION);

	    // Enqueue the admin stylesheet
		wp_enqueue_style('objectiv-cfw-admin-styles');

		// Add the color picker css file
		wp_enqueue_style( 'wp-color-picker' );

		// Add media picker script
		wp_enqueue_media();

		// Include our custom jQuery file with WordPress Color Picker dependency
		wp_enqueue_script( 'objectiv-cfw-admin', CFW_URL . 'assets/admin/js/admin.js', array( 'jquery', 'wp-color-picker', 'wc-enhanced-select' ), CFW_VERSION );

		// Localize the script with new data
		$settings_array = array(
			'logo_attachment_id' => $this->plugin_instance->get_settings_manager()->get_setting('logo_attachment_id'),
		);
		wp_localize_script( 'objectiv-cfw-admin', 'objectiv_cfw_admin', $settings_array );
    }

	/**
	 * add_notice_key_nag function
     *
     * @since 1.0.0
     * @access public
	 */
	public function add_notice_key_nag() {
		$key_status = $this->plugin_instance->get_updater()->get_field_value('key_status');
		$license_key = $this->plugin_instance->get_updater()->get_field_value('license_key');

		if ( ! empty($_GET['cfw_welcome']) ) return;

		// Validate Key Status
		if ( empty($license_key) || ( ($key_status !== "valid" || $key_status == "inactive" || $key_status == "site_inactive") ) ) {
			$important = '';
		    if ( isset($_GET['page']) && $_GET['page'] == 'cfw-settings') {
		        $important = "style='display:block !important'";
            }

		    echo "<div $important class='notice notice-error is-dismissible checkout-wc'> <p>" . $this->renew_or_purchase_nag($key_status, $license_key) . "</p></div>";
		}
	}

	/**
	 * add_compatibility_nag function
     *
     * @since 2.32.1
     * @access public
	 */
	public function add_compatibility_nag() {
        $active_plugins = get_option( 'active_plugins' );
        $incompatible_plugins = [];

        if ( in_array( 'woo-checkout-field-editor-pro/checkout-form-designer.php', $active_plugins ) ) {
            $incompatible_plugins[] = 'Checkout Field Editor for WooCommerce';
        }

        if ( in_array( 'wc-fields-factory/wcff.php', $active_plugins ) ) {
            $incompatible_plugins[] = 'WC Fields Factory';
        }

        if ( ! empty( $incompatible_plugins ) ) {
	        $important = '';
	        if ( isset($_GET['page']) && $_GET['page'] == 'cfw-settings') {
		        $important = "style='display:block !important'";
	        }

	        echo "<div $important class=\"notice notice-error is-dismissible checkout-wc\"><p><strong>" . __( 'Checkout for WooCommerce: Warning incompatible plugins detected!', 'checkout-wc' ) . '</strong>';
	        echo '<ol>';

	        foreach( $incompatible_plugins as $incompatible_plugin ) {
	            echo "<li>{$incompatible_plugin}</li>";
            }

	        echo '</ol>';
	        echo __( 'Please deactivate these plugins to avoid problems with your checkout!', 'checkout-wc' ) . '</p></div>';
        }
    }

	/**
     * @since 1.0.0
     * @access public
	 * @param $key_status
	 * @param $license_key
	 * @return String The renewal or purchase notice.
	 */
	public function renew_or_purchase_nag( $key_status, $license_key ) {
		if ( $key_status == "expired" ) {
			return sprintf(__('Checkout for WooCommerce: Your license key appears to have expired. Please verify that your license key is valid or <a target="_blank" href="https://www.checkoutwc.com/checkout/?edd_license_key=%s">renew your license now</a> to restore full functionality.', $license_key), 'checkout-wc');
		}

		return __( 'Checkout for WooCommerce: Your license key is missing or invalid. Please verify that your license key is valid or <a target="_blank" href="https://www.checkoutwc.com/">purchase a license</a> to restore full functionality.', 'checkout-wc');
	}

	function add_welcome_notice() {
	    if ( ! empty($_GET['cfw_welcome']) ) {
	        echo "<div style='display:block !important' class='notice notice-info'><p>" . __('Thank you for installing Checkout for WooCommerce! To get started, click on <strong>License</strong> below and activate your license key!', 'checkout-wc') . "</p></div>";
        }
    }

	function welcome_screen_do_activation_redirect() {
		// Bail if no activation redirect
		if ( ! get_transient( '_cfw_welcome_screen_activation_redirect' ) ) {
			return;
		}

		// Delete the redirect transient
		delete_transient( '_cfw_welcome_screen_activation_redirect' );

		// Bail if activating from network, or bulk
		if ( is_network_admin() || isset( $_GET['activate-multi'] ) ) {
			return;
		}

		// Redirect to bbPress about page
		wp_safe_redirect( add_query_arg( array( 'page' => 'cfw-settings', 'cfw_welcome' => 'true' ), admin_url( 'options-general.php' ) ) );
    }

    function add_action_links( $links ) {
	    $settings_link = array(
		    '<a href="' . admin_url( 'options-general.php?page=cfw-settings' ) . '">' . __('Settings', 'checkout-wc') . '</a>',
	    );

	    return array_merge( $settings_link, $links );
    }

    function maybe_migrate_settings() {
	    $settings_version = $this->plugin_instance->get_settings_manager()->get_setting( 'settings_version' );

	    if ( empty($settings_version) ) {
		    $cfw_templates = $this->plugin_instance->get_templates_manager()->get_templates_information();

		    foreach( $cfw_templates as $template_path => $template_information ) {
                $this->plugin_instance->get_settings_manager()->update_setting( 'header_background_color', $this->plugin_instance->get_settings_manager()->get_setting('header_background_color'), array( $template_path ) );
                $this->plugin_instance->get_settings_manager()->update_setting( 'header_text_color', $this->plugin_instance->get_settings_manager()->get_setting('header_text_color'), array( $template_path ) );
                $this->plugin_instance->get_settings_manager()->update_setting( 'footer_background_color', $this->plugin_instance->get_settings_manager()->get_setting('footer_background_color'), array( $template_path ) );
                $this->plugin_instance->get_settings_manager()->update_setting( 'footer_color', $this->plugin_instance->get_settings_manager()->get_setting('footer_color'), array( $template_path ) );
                $this->plugin_instance->get_settings_manager()->update_setting( 'link_color', $this->plugin_instance->get_settings_manager()->get_setting('link_color'), array( $template_path ) );
                $this->plugin_instance->get_settings_manager()->update_setting( 'button_color', $this->plugin_instance->get_settings_manager()->get_setting('button_color'), array( $template_path ) );
                $this->plugin_instance->get_settings_manager()->update_setting( 'button_text_color', $this->plugin_instance->get_settings_manager()->get_setting('button_text_color'), array( $template_path ) );
                $this->plugin_instance->get_settings_manager()->update_setting( 'secondary_button_color', $this->plugin_instance->get_settings_manager()->get_setting('secondary_button_color'), array( $template_path ) );
                $this->plugin_instance->get_settings_manager()->update_setting( 'secondary_button_text_color', $this->plugin_instance->get_settings_manager()->get_setting('secondary_button_text_color'), array( $template_path ) );
            }

		    /**
		     * Theme Specific Settings
		     */

		    // Copify Summary Background
		    $this->plugin_instance->get_settings_manager()->update_setting( 'summary_background_color', '#f8f8f8', array( 'copify' ) );

		    // Futurist Header Background / Header Text
		    $this->plugin_instance->get_settings_manager()->update_setting( 'header_background_color', '#000000', array( 'futurist' ) );
		    $this->plugin_instance->get_settings_manager()->update_setting( 'header_text_color', '#ffffff', array( 'futurist' ) );

		    // Set active theme
            $this->plugin_instance->get_settings_manager()->update_setting( 'active_template', 'default' );

		    $this->plugin_instance->get_settings_manager()->update_setting( 'settings_version', '200' );
        }
    }

	/**
	 * @since 1.1.5
	 * @param $order
	 */
	public function shipping_phone_display_admin_order_meta( $order ) {
		$shipping_phone = get_post_meta( $order->get_id(), '_shipping_phone', true );

		if ( empty($shipping_phone) ) {
		    return;
        }

		echo '<p><strong>' . __( 'Phone' ) . ':</strong><br /><a href="tel:' . $shipping_phone . '">' . $shipping_phone . '</a></p>';
	}
}