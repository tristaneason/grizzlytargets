<?php

namespace Objectiv\Plugins\Checkout\Core;
use Objectiv\Plugins\Checkout\Main;
use Objectiv\Plugins\Checkout\Managers\ExtendedPathManager;
use Objectiv\Plugins\Checkout\Managers\SettingsManager;
use Objectiv\Plugins\Checkout\Managers\TemplatesManager;

/**
 * Class Customizer
 *
 * @link objectiv.co
 * @since 2.4.0
 * @package Objectiv\Plugins\Checkout\Core
 * @author Clifton Griffin <clif@checkoutwc.com>
 */
class Customizer {
	var $settings_manager;
	var $templates_manager;
	var $path_manager;

	/**
	 * Customizer constructor.
	 *
	 * @param SettingsManager $settings_manager
	 * @param TemplatesManager $template_manager
	 * @param ExtendedPathManager $path_manager
	 */
	public function __construct( $settings_manager, $template_manager, $path_manager ) {
		$this->settings_manager  = $settings_manager;
		$this->templates_manager = $template_manager;
		$this->path_manager      = $path_manager;

		$this->customizer_workaround();
		add_action( 'customize_register', array( $this, 'register_customizer_settings' ) );
	}

	function customizer_workaround() {
		add_action(
			'wp', function() {
				if ( is_customize_preview() || ! empty( $_GET['customize_changeset_uuid'] ) ) {
					// Reload the settings manager
					$cfw = Main::instance();

					$cfw->set_settings_manager( new SettingsManager() );
					$this->settings_manager = $cfw->get_settings_manager();

					$active_template = $this->settings_manager->get_setting( 'active_template' );

					// Create the template manager
					$cfw->set_templates_manager( new TemplatesManager( $this->path_manager, empty( $active_template ) ? 'default' : $active_template ) );
					$this->templates_manager = $cfw->get_templates_manager();

					// Initiate form - wp is late enough that the customizer will pick up the changes
					$cfw->init_hooks();
				}
			}
		);
	}

	function register_customizer_settings( $wp_customize ) {
		/**
		 * Register Settings
		 */
		// Enabled
		$wp_customize->add_setting(
			$this->get_customizer_field_name( 'enable' ), // No need to use a SERIALIZED name, as `theme_mod` settings already live under one db record
			array(
				'default'           => $this->settings_manager->get_setting( 'enable' ), //Default setting/value to save
				'type'              => 'option', //Is this an 'option' or a 'theme_mod'?
				'capability'        => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
				'transport'         => 'refresh', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
				'sanitize_callback' => array( $this, 'checkbox_sanitize' ),
			)
		);

		// Header Scripts
		$wp_customize->add_setting(
			$this->get_customizer_field_name( 'header_scripts' ), // No need to use a SERIALIZED name, as `theme_mod` settings already live under one db record
			array(
				'default'    => $this->settings_manager->get_setting( 'header_scripts' ), // Default setting/value to save
				'type'       => 'option', // Is this an 'option' or a 'theme_mod'?
				'capability' => 'edit_theme_options', // Optional. Special permissions for accessing this setting.
				'transport'  => 'refresh', // What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
			)
		);

		// Footer Scripts
		$wp_customize->add_setting(
			$this->get_customizer_field_name( 'footer_scripts' ), // No need to use a SERIALIZED name, as `theme_mod` settings already live under one db record
			array(
				'default'    => $this->settings_manager->get_setting( 'footer_scripts' ), // Default setting/value to save
				'type'       => 'option', // Is this an 'option' or a 'theme_mod'?
				'capability' => 'edit_theme_options', // Optional. Special permissions for accessing this setting.
				'transport'  => 'refresh', // What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
			)
		);

		// Templates
		$wp_customize->add_setting(
			$this->get_customizer_field_name( 'active_template' ), // No need to use a SERIALIZED name, as `theme_mod` settings already live under one db record
			array(
				'default'    => $this->settings_manager->get_setting( 'active_template' ), // Default setting/value to save
				'type'       => 'option', // Is this an 'option' or a 'theme_mod'?
				'capability' => 'edit_theme_options', // Optional. Special permissions for accessing this setting.
				'transport'  => 'refresh', // What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
			)
		);

		// Design
		$wp_customize->add_setting(
			$this->get_customizer_field_name( 'logo_attachment_id' ), // No need to use a SERIALIZED name, as `theme_mod` settings already live under one db record
			array(
				'default'    => $this->settings_manager->get_setting( 'logo_attachment_id' ), // Default setting/value to save
				'type'       => 'option', // Is this an 'option' or a 'theme_mod'?
				'capability' => 'edit_theme_options', // Optional. Special permissions for accessing this setting.
				'transport'  => 'refresh', // What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
			)
		);

		$wp_customize->add_setting(
			$this->get_customizer_field_name( 'footer_text' ), // No need to use a SERIALIZED name, as `theme_mod` settings already live under one db record
			array(
				'default'    => $this->settings_manager->get_setting( 'footer_text' ), // Default setting/value to save
				'type'       => 'option', // Is this an 'option' or a 'theme_mod'?
				'capability' => 'edit_theme_options', // Optional. Special permissions for accessing this setting.
				'transport'  => 'refresh', // What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
			)
		);

		$active_template    = $this->templates_manager->getActiveTemplate();

		if ( $active_template->supports( 'header-background' ) ) {
			$wp_customize->add_setting(
				$this->get_customizer_field_name( 'header_background_color', array( $active_template->get_slug() ) ), // No need to use a SERIALIZED name, as `theme_mod` settings already live under one db record
				array(
					'default'    => $this->settings_manager->get_setting( 'header_background_color', array( $active_template->get_slug() ) ), // Default setting/value to save
					'type'       => 'option', // Is this an 'option' or a 'theme_mod'?
					'capability' => 'edit_theme_options', // Optional. Special permissions for accessing this setting.
					'transport'  => 'refresh', // What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
				)
			);
		}

		$wp_customize->add_setting(
			$this->get_customizer_field_name( 'header_text_color', array( $active_template->get_slug() ) ), // No need to use a SERIALIZED name, as `theme_mod` settings already live under one db record
			array(
				'default'    => $this->settings_manager->get_setting( 'header_text_color', array( $active_template->get_slug() ) ), // Default setting/value to save
				'type'       => 'option', // Is this an 'option' or a 'theme_mod'?
				'capability' => 'edit_theme_options', // Optional. Special permissions for accessing this setting.
				'transport'  => 'refresh', // What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
			)
		);

		if ( $active_template->supports( 'footer-background' ) ) {
			$wp_customize->add_setting(
				$this->get_customizer_field_name( 'footer_background_color', array( $active_template->get_slug() ) ), // No need to use a SERIALIZED name, as `theme_mod` settings already live under one db record
				array(
					'default'    => $this->settings_manager->get_setting( 'footer_background_color', array( $active_template->get_slug() ) ), // Default setting/value to save
					'type'       => 'option', // Is this an 'option' or a 'theme_mod'?
					'capability' => 'edit_theme_options', // Optional. Special permissions for accessing this setting.
					'transport'  => 'refresh', // What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
				)
			);
		}

		$wp_customize->add_setting(
			$this->get_customizer_field_name( 'footer_color', array( $active_template->get_slug() ) ), // No need to use a SERIALIZED name, as `theme_mod` settings already live under one db record
			array(
				'default'    => $this->settings_manager->get_setting( 'footer_color', array( $active_template->get_slug() ) ), // Default setting/value to save
				'type'       => 'option', // Is this an 'option' or a 'theme_mod'?
				'capability' => 'edit_theme_options', // Optional. Special permissions for accessing this setting.
				'transport'  => 'refresh', // What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
			)
		);

		if ( $active_template->supports( 'summary-background' ) ) {
			$wp_customize->add_setting(
				$this->get_customizer_field_name( 'summary_background_color', array( $active_template->get_slug() ) ), // No need to use a SERIALIZED name, as `theme_mod` settings already live under one db record
				array(
					'default'    => $this->settings_manager->get_setting( 'summary_background_color', array( $active_template->get_slug() ) ), // Default setting/value to save
					'type'       => 'option', // Is this an 'option' or a 'theme_mod'?
					'capability' => 'edit_theme_options', // Optional. Special permissions for accessing this setting.
					'transport'  => 'refresh', // What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
				)
			);
		}

		$wp_customize->add_setting(
			$this->get_customizer_field_name( 'button_color', array( $active_template->get_slug() ) ), // No need to use a SERIALIZED name, as `theme_mod` settings already live under one db record
			array(
				'default'    => $this->settings_manager->get_setting( 'button_color', array( $active_template->get_slug() ) ), // Default setting/value to save
				'type'       => 'option', // Is this an 'option' or a 'theme_mod'?
				'capability' => 'edit_theme_options', // Optional. Special permissions for accessing this setting.
				'transport'  => 'refresh', // What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
			)
		);

		$wp_customize->add_setting(
			$this->get_customizer_field_name( 'button_text_color', array( $active_template->get_slug() ) ), // No need to use a SERIALIZED name, as `theme_mod` settings already live under one db record
			array(
				'default'    => $this->settings_manager->get_setting( 'button_text_color', array( $active_template->get_slug() ) ), // Default setting/value to save
				'type'       => 'option', // Is this an 'option' or a 'theme_mod'?
				'capability' => 'edit_theme_options', // Optional. Special permissions for accessing this setting.
				'transport'  => 'refresh', // What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
			)
		);

		$wp_customize->add_setting(
			$this->get_customizer_field_name( 'secondary_button_color', array( $active_template->get_slug() ) ), // No need to use a SERIALIZED name, as `theme_mod` settings already live under one db record
			array(
				'default'    => $this->settings_manager->get_setting( 'secondary_button_color', array( $active_template->get_slug() ) ), // Default setting/value to save
				'type'       => 'option', // Is this an 'option' or a 'theme_mod'?
				'capability' => 'edit_theme_options', // Optional. Special permissions for accessing this setting.
				'transport'  => 'refresh', // What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
			)
		);

		$wp_customize->add_setting(
			$this->get_customizer_field_name( 'secondary_button_text_color', array( $active_template->get_slug() ) ), // No need to use a SERIALIZED name, as `theme_mod` settings already live under one db record
			array(
				'default'    => $this->settings_manager->get_setting( 'secondary_button_text_color', array( $active_template->get_slug() ) ), // Default setting/value to save
				'type'       => 'option', // Is this an 'option' or a 'theme_mod'?
				'capability' => 'edit_theme_options', // Optional. Special permissions for accessing this setting.
				'transport'  => 'refresh', // What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
			)
		);

		$wp_customize->add_setting(
			$this->get_customizer_field_name( 'link_color', array( $active_template->get_slug() ) ), // No need to use a SERIALIZED name, as `theme_mod` settings already live under one db record
			array(
				'default'    => $this->settings_manager->get_setting( 'link_color', array( $active_template->get_slug() ) ), // Default setting/value to save
				'type'       => 'option', // Is this an 'option' or a 'theme_mod'?
				'capability' => 'edit_theme_options', // Optional. Special permissions for accessing this setting.
				'transport'  => 'refresh', // What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
			)
		);

		$wp_customize->add_setting(
			$this->get_customizer_field_name( 'custom_css', array( $active_template->get_slug() ) ), // No need to use a SERIALIZED name, as `theme_mod` settings already live under one db record
			array(
				'default'    => $this->settings_manager->get_setting( 'custom_css', array( $active_template->get_slug() ) ), // Default setting/value to save
				'type'       => 'option', // Is this an 'option' or a 'theme_mod'?
				'capability' => 'edit_theme_options', // Optional. Special permissions for accessing this setting.
				'transport'  => 'refresh', // What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
			)
		);


		/**
		 * Checkout for WooCommerce - Panel
		 */
		$wp_customize->add_panel(
			'cfw',
			array(
				'title'       => __( 'Checkout for WooCommerce', 'checkout-wc' ),
				'priority'    => 1000,
				'capability'  => 'edit_theme_options',
				'description' => __( 'Checkout for WooCommerce provides a beautiful, conversion optimized checkout template for WooCommerce.', 'checkout-wc' ),
			)
		);

		/**
		 * General Section
		 */
		$wp_customize->add_section(
			'cfw-general',
			array(
				'title'      => __( 'General', 'checkout-wc' ),
				'priority'   => 10,
				'capability' => 'edit_theme_options',
				'panel'      => 'cfw',
			)
		);

		$wp_customize->add_control(
			$this->get_customizer_field_name( 'enable' ),
			array(
				'type'     => 'checkbox',
				'label'    => __( 'Enable / Disable', 'checkout-wc' ), // Admin-visible name of the control
				'settings' => $this->get_customizer_field_name( 'enable' ), // Which setting to load and manipulate (serialized is okay)
				'priority' => 10, // Determines the order this control appears in for the specified section
				'section'  => 'cfw-general', // ID of the section this control should render in (can be one of yours, or a WordPress default section)
			)
		);

		$wp_customize->add_control(
			$this->get_customizer_field_name( 'header_scripts' ),
			array(
				'type'     => 'textarea',
				'label'    => __( 'Header Scripts', 'checkout-wc' ), // Admin-visible name of the control
				'settings' => $this->get_customizer_field_name( 'header_scripts' ), // Which setting to load and manipulate (serialized is okay)
				'priority' => 10, // Determines the order this control appears in for the specified section
				'section'  => 'cfw-general', // ID of the section this control should render in (can be one of yours, or a WordPress default section)
			)
		);

		$wp_customize->add_control(
			$this->get_customizer_field_name( 'footer_scripts' ),
			array(
				'type'     => 'textarea',
				'label'    => __( 'Footer Scripts', 'checkout-wc' ), // Admin-visible name of the control
				'settings' => $this->get_customizer_field_name( 'footer_scripts' ), // Which setting to load and manipulate (serialized is okay)
				'priority' => 10, // Determines the order this control appears in for the specified section
				'section'  => 'cfw-general', // ID of the section this control should render in (can be one of yours, or a WordPress default section)
			)
		);

		/**
		 * Template Section
		 */
		$wp_customize->add_section(
			'cfw-templates',
			array(
				'title'      => __( 'Template', 'checkout-wc' ),
				'priority'   => 10,
				'capability' => 'edit_theme_options',
				'panel'      => 'cfw',
			)
		);

		$template_choices = [];

		foreach ( $this->templates_manager->getAvailableTemplates() as $folder_name => $template ) {
			$template_choices[ $folder_name ] = $template->get_name();
		}

		$wp_customize->add_control(
			$this->get_customizer_field_name( 'active_template' ),
			array(
				'type'     => 'select',
				'label'    => __( 'Template', 'checkout-wc' ), // Admin-visible name of the control
				'settings' => $this->get_customizer_field_name( 'active_template' ), // Which setting to load and manipulate (serialized is okay)
				'priority' => 10, // Determines the order this control appears in for the specified section
				'section'  => 'cfw-templates', // ID of the section this control should render in (can be one of yours, or a WordPress default section)
				'choices'  => $template_choices,
			)
		);

		/**
		 * Design Section
		 */
		$wp_customize->add_section(
			'cfw-design',
			array(
				'title'      => __( 'Design', 'checkout-wc' ),
				'priority'   => 10,
				'capability' => 'edit_theme_options',
				'panel'      => 'cfw',
			)
		);

		$wp_customize->add_control(
			new \WP_Customize_Media_Control(
				$wp_customize,
				$this->get_customizer_field_name( 'logo_attachment_id' ),
				array(
					'label'    => __( 'Logo', 'checkout-wc' ), // Admin-visible name of the control
					'settings' => $this->get_customizer_field_name( 'logo_attachment_id' ), // Which setting to load and manipulate (serialized is okay)
					'priority' => 10, // Determines the order this control appears in for the specified section
					'section'  => 'cfw-design', // ID of the section this control should render in (can be one of yours, or a WordPress default section)
				)
			)
		);

		$wp_customize->add_control(
			$this->get_customizer_field_name( 'footer_text' ),
			array(
				'type'     => 'textarea',
				'label'    => __( 'Footer Text', 'checkout-wc' ), // Admin-visible name of the control
				'settings' => $this->get_customizer_field_name( 'footer_text' ), // Which setting to load and manipulate (serialized is okay)
				'priority' => 10, // Determines the order this control appears in for the specified section
				'section'  => 'cfw-design', // ID of the section this control should render in (can be one of yours, or a WordPress default section)
			)
		);

		/**
		 * Design: Template Section
		 */
		$wp_customize->add_section(
			'cfw-design-template',
			array(
				'title'       => __( 'Design', 'checkout-wc' ) . ': ' . __( 'Theme Specific Settings', 'checkout-wc' ),
				'priority'    => 10,
				'capability'  => 'edit_theme_options',
				'panel'       => 'cfw',
				'description' => __( 'These settings only apply to the currently active template. If you switch templates in the customizer, these settings will become unavailable but saving will publish any changes here prior to switching templates!', 'checkout-wc' ),
			)
		);

		$wp_customize->add_control(
			new \WP_Customize_Color_Control(
				$wp_customize,
				$this->get_customizer_field_name( 'header_background_color', array( $active_template->get_slug() ) ),
				array(
					'label'           => __( 'Header Background Color', 'checkout-wc' ), // Admin-visible name of the control
					'settings'        => $this->get_customizer_field_name( 'header_background_color', array( $active_template->get_slug() ) ), // Which setting to load and manipulate (serialized is okay)
					'priority'        => 10, // Determines the order this control appears in for the specified section
					'section'         => 'cfw-design-template', // ID of the section this control should render in (can be one of yours, or a WordPress default section)
					'active_callback' => array( $this, 'is_correct_template_active' ),
				)
			)
		);

		$wp_customize->add_control(
			new \WP_Customize_Color_Control(
				$wp_customize,
				$this->get_customizer_field_name( 'header_text_color', array( $active_template->get_slug() ) ),
				array(
					'label'           => __( 'Header Text Color', 'checkout-wc' ), // Admin-visible name of the control
					'settings'        => $this->get_customizer_field_name( 'header_text_color', array( $active_template->get_slug() ) ), // Which setting to load and manipulate (serialized is okay)
					'priority'        => 10, // Determines the order this control appears in for the specified section
					'section'         => 'cfw-design-template', // ID of the section this control should render in (can be one of yours, or a WordPress default section)
					'active_callback' => array( $this, 'is_correct_template_active' ),
				)
			)
		);

		$wp_customize->add_control(
			new \WP_Customize_Color_Control(
				$wp_customize,
				$this->get_customizer_field_name( 'footer_background_color', array( $active_template->get_slug() ) ),
				array(
					'label'           => __( 'Footer Background Color', 'checkout-wc' ), // Admin-visible name of the control
					'settings'        => $this->get_customizer_field_name( 'footer_background_color', array( $active_template->get_slug() ) ), // Which setting to load and manipulate (serialized is okay)
					'priority'        => 10, // Determines the order this control appears in for the specified section
					'section'         => 'cfw-design-template', // ID of the section this control should render in (can be one of yours, or a WordPress default section)
					'active_callback' => array( $this, 'is_correct_template_active' ),
				)
			)
		);

		$wp_customize->add_control(
			new \WP_Customize_Color_Control(
				$wp_customize,
				$this->get_customizer_field_name( 'footer_color', array( $active_template->get_slug() ) ),
				array(
					'label'           => __( 'Footer Text Color', 'checkout-wc' ), // Admin-visible name of the control
					'settings'        => $this->get_customizer_field_name( 'footer_color', array( $active_template->get_slug() ) ), // Which setting to load and manipulate (serialized is okay)
					'priority'        => 10, // Determines the order this control appears in for the specified section
					'section'         => 'cfw-design-template', // ID of the section this control should render in (can be one of yours, or a WordPress default section)
					'active_callback' => array( $this, 'is_correct_template_active' ),
				)
			)
		);

		$wp_customize->add_control(
			new \WP_Customize_Color_Control(
				$wp_customize,
				$this->get_customizer_field_name( 'summary_background_color', array( $active_template->get_slug() ) ),
				array(
					'label'           => __( 'Summary Background Color', 'checkout-wc' ), // Admin-visible name of the control
					'settings'        => $this->get_customizer_field_name( 'summary_background_color', array( $active_template->get_slug() ) ), // Which setting to load and manipulate (serialized is okay)
					'priority'        => 10, // Determines the order this control appears in for the specified section
					'section'         => 'cfw-design-template', // ID of the section this control should render in (can be one of yours, or a WordPress default section)
					'active_callback' => array( $this, 'is_correct_template_active' ),
				)
			)
		);

		$wp_customize->add_control(
			new \WP_Customize_Color_Control(
				$wp_customize,
				$this->get_customizer_field_name( 'button_color', array( $active_template->get_slug() ) ),
				array(
					'label'           => __( 'Primary Button Color', 'checkout-wc' ), // Admin-visible name of the control
					'settings'        => $this->get_customizer_field_name( 'button_color', array( $active_template->get_slug() ) ), // Which setting to load and manipulate (serialized is okay)
					'priority'        => 10, // Determines the order this control appears in for the specified section
					'section'         => 'cfw-design-template', // ID of the section this control should render in (can be one of yours, or a WordPress default section)
					'active_callback' => array( $this, 'is_correct_template_active' ),
				)
			)
		);

		$wp_customize->add_control(
			new \WP_Customize_Color_Control(
				$wp_customize,
				$this->get_customizer_field_name( 'button_text_color', array( $active_template->get_slug() ) ),
				array(
					'label'           => __( 'Primary Button Text Color', 'checkout-wc' ), // Admin-visible name of the control
					'settings'        => $this->get_customizer_field_name( 'button_text_color', array( $active_template->get_slug() ) ), // Which setting to load and manipulate (serialized is okay)
					'priority'        => 10, // Determines the order this control appears in for the specified section
					'section'         => 'cfw-design-template', // ID of the section this control should render in (can be one of yours, or a WordPress default section)
					'active_callback' => array( $this, 'is_correct_template_active' ),
				)
			)
		);

		$wp_customize->add_control(
			new \WP_Customize_Color_Control(
				$wp_customize,
				$this->get_customizer_field_name( 'secondary_button_color', array( $active_template->get_slug() ) ),
				array(
					'label'           => __( 'Secondary Button Color', 'checkout-wc' ), // Admin-visible name of the control
					'settings'        => $this->get_customizer_field_name( 'secondary_button_color', array( $active_template->get_slug() ) ), // Which setting to load and manipulate (serialized is okay)
					'priority'        => 10, // Determines the order this control appears in for the specified section
					'section'         => 'cfw-design-template', // ID of the section this control should render in (can be one of yours, or a WordPress default section)
					'active_callback' => array( $this, 'is_correct_template_active' ),
				)
			)
		);

		$wp_customize->add_control(
			new \WP_Customize_Color_Control(
				$wp_customize,
				$this->get_customizer_field_name( 'secondary_button_text_color', array( $active_template->get_slug() ) ),
				array(
					'label'           => __( 'Secondary Button Text Color', 'checkout-wc' ), // Admin-visible name of the control
					'settings'        => $this->get_customizer_field_name( 'secondary_button_text_color', array( $active_template->get_slug() ) ), // Which setting to load and manipulate (serialized is okay)
					'priority'        => 10, // Determines the order this control appears in for the specified section
					'section'         => 'cfw-design-template', // ID of the section this control should render in (can be one of yours, or a WordPress default section)
					'active_callback' => array( $this, 'is_correct_template_active' ),
				)
			)
		);

		$wp_customize->add_control(
			new \WP_Customize_Color_Control(
				$wp_customize,
				$this->get_customizer_field_name( 'link_color', array( $active_template->get_slug() ) ),
				array(
					'label'           => __( 'Link Color', 'checkout-wc' ), // Admin-visible name of the control
					'settings'        => $this->get_customizer_field_name( 'link_color', array( $active_template->get_slug() ) ), // Which setting to load and manipulate (serialized is okay)
					'priority'        => 10, // Determines the order this control appears in for the specified section
					'section'         => 'cfw-design-template', // ID of the section this control should render in (can be one of yours, or a WordPress default section)
					'active_callback' => array( $this, 'is_correct_template_active' ),
				)
			)
		);

		$wp_customize->add_control(
			$this->get_customizer_field_name( 'custom_css', array( $active_template->get_slug() ) ),
			array(
				'type'            => 'textarea',
				'label'           => __( 'Custom CSS', 'checkout-wc' ), // Admin-visible name of the control
				'settings'        => $this->get_customizer_field_name( 'custom_css', array( $active_template->get_slug() ) ), // Which setting to load and manipulate (serialized is okay)
				'priority'        => 10, // Determines the order this control appears in for the specified section
				'section'         => 'cfw-design-template', // ID of the section this control should render in (can be one of yours, or a WordPress default section),
				'active_callback' => array( $this, 'is_correct_template_active' ),
			)
		);
	}

	function is_correct_template_active( $control ) {
		return stripos( $control->id, $control->manager->get_setting( $this->get_customizer_field_name( 'active_template' ) )->value() ) !== false;
	}

	function get_customizer_field_name( $setting, $keys = array() ) {
		$field_name = str_ireplace( '[string]', '', $this->settings_manager->get_field_name( $setting, $keys ) );

		return str_ireplace( '__setting', '__settings', $field_name );
	}

	function checkbox_sanitize( $checked ) {
		return $checked == 'yes' ? true : false;
	}
}
