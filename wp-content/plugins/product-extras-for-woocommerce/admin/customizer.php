<?php
/**
 * Include the Customizer Library
 * @since 2.3.3
 */

 // Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

// Certain themes throw a JavaScript error related to the color picker
function pewc_enqueue_color_picker( $hook_suffix ) {
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script( 'wp-color-picker');
}
add_action( 'admin_enqueue_scripts', 'pewc_enqueue_color_picker' );

add_action( 'customize_register', 'pewc_add_product_extras_section' );

function pewc_add_customizer_section( $wp_customize ) {
  pewc_add_product_extras_section( $wp_customize );
}

function pewc_add_product_extras_section( $wp_customize ) {

  $wp_customize->add_panel( 'pewc_panel', array(
    'priority'       => 201,
    'capability'     => 'edit_theme_options',
    'theme_supports' => '',
    'title'          => __( 'Product Add-Ons Ultimate', 'pewc' )
  ) );

  $wp_customize->add_section(
    'pewc_section',
    array(
      'title'    => __( 'General', 'pewc' ),
      'priority' => 10,
      'panel'    => 'pewc_panel'
    )
  );

  $wp_customize->add_section(
    'pewc_styles_section',
    array(
      'title'    => __( 'Styles', 'pewc' ),
      'priority' => 20,
      'panel'    => 'pewc_panel',
    )
  );

  $wp_customize->add_setting(
    'pewc_list_margin_left',
    array(
      'default'              => '',
      'type'                 => 'theme_mod',
      'capability'           => 'manage_woocommerce',
      'default'              =>  '0'
    )
  );

  $wp_customize->add_control(
    'pewc_list_margin_left',
    array(
      'label'    => __( 'Fields Wrapper Margin Left', 'pewc' ),
      'section'  => 'pewc_styles_section',
      'settings' => 'pewc_list_margin_left',
      'type'     => 'number'
    )
  );

  $wp_customize->add_setting(
    'pewc_list_margin_bottom',
    array(
      'default'              => '',
      'type'                 => 'theme_mod',
      'capability'           => 'manage_woocommerce',
      'default'              =>  '0'
    )
  );

  $wp_customize->add_control(
    'pewc_list_margin_bottom',
    array(
      'label'    => __( 'Fields Wrapper Margin Bottom', 'pewc' ),
      'section'  => 'pewc_styles_section',
      'settings' => 'pewc_list_margin_bottom',
      'type'     => 'number'
    )
  );

  $wp_customize->add_setting(
    'pewc_list_padding',
    array(
      'default'              => '',
      'type'                 => 'theme_mod',
      'capability'           => 'manage_woocommerce',
      'default'              =>  ''
    )
  );

  $wp_customize->add_control(
    'pewc_list_padding',
    array(
      'label'    => __( 'Fields Wrapper Padding', 'pewc' ),
      'section'  => 'pewc_styles_section',
      'settings' => 'pewc_list_padding',
      'type'     => 'number'
    )
  );

  $wp_customize->add_setting(
    'pewc_list_background',
    array(
      'default'              => '',
      'type'                 => 'theme_mod',
      'capability'           => 'manage_woocommerce',
      'default'              =>  ''
    )
  );

  $wp_customize->add_control(
    'pewc_list_background',
    array(
      'label'    => __( 'Fields Wrapper Background Colour', 'pewc' ),
      'section'  => 'pewc_styles_section',
      'settings' => 'pewc_list_background',
      'type'     => 'color'
    )
  );

  // Individual fields
  $wp_customize->add_setting(
    'pewc_field_margin_left',
    array(
      'default'              => '',
      'type'                 => 'theme_mod',
      'capability'           => 'manage_woocommerce',
      'default'              =>  '0'
    )
  );

  $wp_customize->add_control(
    'pewc_field_margin_left',
    array(
      'label'    => __( 'Field Margin Left', 'pewc' ),
      'section'  => 'pewc_styles_section',
      'settings' => 'pewc_field_margin_left',
      'type'     => 'number'
    )
  );

  $wp_customize->add_setting(
    'pewc_field_margin_bottom',
    array(
      'default'              => '',
      'type'                 => 'theme_mod',
      'capability'           => 'manage_woocommerce',
      'default'              =>  '16'
    )
  );

  $wp_customize->add_control(
    'pewc_field_margin_bottom',
    array(
      'label'    => __( 'Field Margin Bottom', 'pewc' ),
      'section'  => 'pewc_styles_section',
      'settings' => 'pewc_field_margin_bottom',
      'type'     => 'number'
    )
  );

  $wp_customize->add_setting(
    'pewc_field_padding_top',
    array(
      'default'              => '',
      'type'                 => 'theme_mod',
      'capability'           => 'manage_woocommerce',
      'default'              =>  ''
    )
  );

  $wp_customize->add_control(
    'pewc_field_padding_top',
    array(
      'label'    => __( 'Field Padding (Top and Bottom)', 'pewc' ),
      'section'  => 'pewc_styles_section',
      'settings' => 'pewc_field_padding_top',
      'type'     => 'number'
    )
  );

  $wp_customize->add_setting(
    'pewc_field_padding_left',
    array(
      'default'              => '',
      'type'                 => 'theme_mod',
      'capability'           => 'manage_woocommerce',
      'default'              =>  ''
    )
  );

  $wp_customize->add_control(
    'pewc_field_padding_left',
    array(
      'label'    => __( 'Field Padding (Left and Right)', 'pewc' ),
      'section'  => 'pewc_styles_section',
      'settings' => 'pewc_field_padding_left',
      'type'     => 'number'
    )
  );

  $wp_customize->add_setting(
    'pewc_field_background',
    array(
      'default'              => '',
      'type'                 => 'theme_mod',
      'capability'           => 'manage_woocommerce',
      'default'              =>  ''
    )
  );

  $wp_customize->add_control(
    'pewc_field_background',
    array(
      'label'    => __( 'Field Background Colour', 'pewc' ),
      'section'  => 'pewc_styles_section',
      'settings' => 'pewc_field_background',
      'type'     => 'color'
    )
  );

  $wp_customize->add_setting(
    'pewc_text_colour',
    array(
      'default'              => '',
      'type'                 => 'theme_mod',
      'capability'           => 'manage_woocommerce',
      'default'              =>  ''
    )
  );

  $wp_customize->add_control(
    'pewc_text_colour',
    array(
      'label'    => __( 'Text Colour', 'pewc' ),
      'section'  => 'pewc_styles_section',
      'settings' => 'pewc_text_colour',
      'type'     => 'color'
    )
  );

  $wp_customize->add_setting(
    'pewc_text_width',
    array(
      'default'              => true,
      'type'                 => 'theme_mod',
      'capability'           => 'manage_woocommerce'
    )
  );

  $wp_customize->add_control(
    'pewc_text_width',
    array(
      'label'    => __( 'Full Width Text Fields ', 'pewc' ),
      'section'  => 'pewc_styles_section',
      'settings' => 'pewc_text_width',
      'type'     => 'checkbox'
    )
  );

  $wp_customize->add_setting(
    'pewc_number_width',
    array(
      'default'              => true,
      'type'                 => 'theme_mod',
      'capability'           => 'manage_woocommerce'
    )
  );

  $wp_customize->add_control(
    'pewc_number_width',
    array(
      'label'    => __( 'Full Width Number Fields ', 'pewc' ),
      'section'  => 'pewc_styles_section',
      'settings' => 'pewc_number_width',
      'type'     => 'checkbox'
    )
  );

  $wp_customize->add_setting(
    'pewc_textarea_height',
    array(
      'default'              => '',
      'type'                 => 'theme_mod',
      'capability'           => 'manage_woocommerce'
    )
  );

  $wp_customize->add_control(
    'pewc_textarea_height',
    array(
      'label'    => __( 'Textarea Height', 'pewc' ),
      'section'  => 'pewc_styles_section',
      'settings' => 'pewc_textarea_height',
      'type'     => 'range',
      'input_attrs' => array(
        'min' => 2,
        'max' => 20,
        'step' => 1,
      )
    )
  );

  $wp_customize->add_setting(
    'pewc_select_width',
    array(
      'default'              => true,
      'type'                 => 'theme_mod',
      'capability'           => 'manage_woocommerce'
    )
  );

  $wp_customize->add_control(
    'pewc_select_width',
    array(
      'label'    => __( 'Full Width Select Fields ', 'pewc' ),
      'section'  => 'pewc_styles_section',
      'settings' => 'pewc_select_width',
      'type'     => 'checkbox'
    )
  );

  $wp_customize->add_setting(
    'pewc_block_label',
    array(
      'default'              => true,
      'type'                 => 'theme_mod',
      'capability'           => 'manage_woocommerce'
    )
  );

  $wp_customize->add_control(
    'pewc_block_label',
    array(
      'label'    => __( 'Display Label on Own Line', 'pewc' ),
      'section'  => 'pewc_styles_section',
      'settings' => 'pewc_block_label',
      'type'     => 'checkbox'
    )
  );

  $wp_customize->add_setting(
    'pewc_swatch_wrapper',
    array(
      'default'              => '',
      'type'                 => 'theme_mod',
      'capability'           => 'manage_woocommerce',
      'default'              =>  ''
    )
  );

  $wp_customize->add_control(
    'pewc_swatch_wrapper',
    array(
      'label'    => __( 'Swatch Highlight Colour', 'pewc' ),
      'section'  => 'pewc_styles_section',
      'settings' => 'pewc_swatch_wrapper',
      'type'     => 'color'
    )
  );

  /**
   * General panel
   */

  $wp_customize->add_setting(
    'pewc_enable_summary_panel',
    array(
      'default'              => '',
      'type'                 => 'option',
      'capability'           => 'manage_woocommerce',
      // 'sanitize_callback'    => 'wc_bool_to_string',
      // 'sanitize_js_callback' => 'wc_string_to_bool',
    )
  );

  $wp_customize->add_setting(
    'pewc_price_label',
    array(
      'default'              => '',
      'type'                 => 'option',
      'capability'           => 'manage_woocommerce',
      // 'sanitize_callback'    => 'wc_bool_to_string',
      // 'sanitize_js_callback' => 'wc_string_to_bool',
    )
  );

  $wp_customize->add_setting(
    'pewc_price_display',
    array(
      'default'              => 'before',
      'type'                 => 'option',
      'capability'           => 'manage_woocommerce',
      // 'sanitize_callback'    => 'wc_bool_to_string',
      // 'sanitize_js_callback' => 'wc_string_to_bool',
    )
  );

  $wp_customize->add_setting(
    'pewc_show_totals',
    array(
      'default'              => 'all',
      'type'                 => 'option',
      'capability'           => 'manage_woocommerce',
      // 'sanitize_callback'    => 'wc_bool_to_string',
      // 'sanitize_js_callback' => 'wc_string_to_bool',
    )
  );

  $wp_customize->add_setting(
    'pewc_product_total_label',
    array(
      'default'              => __( 'Product total', 'pewc' ),
      'type'                 => 'option',
      'capability'           => 'manage_woocommerce',
      // 'sanitize_callback'    => 'wc_bool_to_string',
      // 'sanitize_js_callback' => 'wc_string_to_bool',
    )
  );

  $wp_customize->add_setting(
    'pewc_options_total_label',
    array(
      'default'              => __( 'Options total', 'pewc' ),
      'type'                 => 'option',
      'capability'           => 'manage_woocommerce',
      // 'sanitize_callback'    => 'wc_bool_to_string',
      // 'sanitize_js_callback' => 'wc_string_to_bool',
    )
  );

  $wp_customize->add_setting(
    'pewc_flatrate_total_label',
    array(
      'default'              => __( 'Flat rate total', 'pewc' ),
      'type'                 => 'option',
      'capability'           => 'manage_woocommerce',
      // 'sanitize_callback'    => 'wc_bool_to_string',
      // 'sanitize_js_callback' => 'wc_string_to_bool',
    )
  );

  $wp_customize->add_setting(
    'pewc_grand_total_label',
    array(
      'default'              => __( 'Grand total', 'pewc' ),
      'type'                 => 'option',
      'capability'           => 'manage_woocommerce',
      // 'sanitize_callback'    => 'wc_bool_to_string',
      // 'sanitize_js_callback' => 'wc_string_to_bool',
    )
  );

  $wp_customize->add_control(
    'pewc_enable_summary_panel',
    array(
      'label'    => __( 'Enable summary panel', 'pewc' ),
      'section'  => 'pewc_section',
      'settings' => 'pewc_enable_summary_panel',
      'type'     => 'checkbox'
    )
  );

  $wp_customize->add_control(
    'pewc_price_label',
    array(
      'label'    => __( 'Price label', 'pewc' ),
      'section'  => 'pewc_section',
      'settings' => 'pewc_price_label',
      'type'     => 'text'
    )
  );

  $wp_customize->add_control(
    'pewc_price_display',
    array(
      'label'    => __( 'Price label display', 'pewc' ),
      'section'  => 'pewc_section',
      'settings' => 'pewc_price_display',
      'type'        => 'select',
      'choices'     => array(
        'before'			=> __( 'Before price', 'pewc' ),
        'after'				=> __( 'After price', 'pewc' ),
        'hide'				=> __( 'Hide price', 'pewc' )
      ),
    )
  );


  $wp_customize->add_control(
    'pewc_show_totals',
    array(
      'label'    => __( 'Display totals fields', 'pewc' ),
      'section'  => 'pewc_section',
      'settings' => 'pewc_show_totals',
      'type'        => 'select',
      'choices'     => array(
        'all'           => __( 'Show totals', 'pewc' ),
        'none'          => __( 'Hide totals', 'pewc' ),
        'total'         => __( 'Total only', 'pewc' ),
      ),
    )
  );

  $wp_customize->add_control(
    'pewc_product_total_label',
    array(
      'label'    => __( 'Product total label', 'pewc' ),
      'section'  => 'pewc_section',
      'settings' => 'pewc_product_total_label',
      'type'        => 'text'
    )
  );

  $wp_customize->add_control(
    'pewc_options_total_label',
    array(
      'label'    => __( 'Options total label', 'pewc' ),
      'section'  => 'pewc_section',
      'settings' => 'pewc_options_total_label',
      'type'        => 'text'
    )
  );

  $wp_customize->add_control(
    'pewc_flatrate_total_label',
    array(
      'label'    => __( 'Flat rate total label', 'pewc' ),
      'section'  => 'pewc_section',
      'settings' => 'pewc_flatrate_total_label',
      'type'        => 'text'
    )
  );

  $wp_customize->add_control(
    'pewc_grand_total_label',
    array(
      'label'    => __( 'Grand total label', 'pewc' ),
      'section'  => 'pewc_section',
      'settings' => 'pewc_grand_total_label',
      'type'        => 'text'
    )
  );

}

function pewc_customize_css() { ?>
  <style type="text/css">
    .pewc-group-content-wrapper {
      background-color: <?php echo get_theme_mod( 'pewc_list_background', 0 ); ?> !important;
    }
    ul.pewc-product-extra-groups {
      margin-left: <?php echo get_theme_mod( 'pewc_list_margin_left' ); ?>px;
      margin-bottom: <?php echo get_theme_mod( 'pewc_list_margin_bottom' ); ?>px;
      padding: <?php echo get_theme_mod( 'pewc_list_padding' ); ?>px;
      background-color: <?php echo get_theme_mod( 'pewc_list_background' ); ?>;
    }
    .pewc-product-extra-groups > li {
      margin-left: <?php echo get_theme_mod( 'pewc_field_margin_left' ); ?>px;
      margin-bottom: <?php echo get_theme_mod( 'pewc_field_margin_bottom' ); ?>px;
      padding-top: <?php echo get_theme_mod( 'pewc_field_padding_top' ); ?>px;
      padding-bottom: <?php echo get_theme_mod( 'pewc_field_padding_top' ); ?>px;
      padding-left: <?php echo get_theme_mod( 'pewc_field_padding_left' ); ?>px;
      padding-right: <?php echo get_theme_mod( 'pewc_field_padding_left' ); ?>px;
      background-color: <?php echo get_theme_mod( 'pewc_field_background' ); ?>;
      color: <?php echo get_theme_mod( 'pewc_text_colour', 0 ); ?>;
    }
    <?php if( get_theme_mod( 'pewc_text_width' ) ) { ?>
      input[type="text"].pewc-form-field,
      textarea.pewc-form-field {
        width: 100% !important
      }
    <?php } ?>
    <?php if( get_theme_mod( 'pewc_number_width' ) ) { ?>
      .pewc-item-name_price input[type="number"].pewc-form-field,
      .pewc-item-number input[type="number"].pewc-form-field {
        width: 100% !important
      }
    <?php } ?>
    <?php if( get_theme_mod( 'pewc_select_width' ) ) { ?>
      select.pewc-form-field {
        width: 100% !important
      }
    <?php } ?>
    textarea.pewc-form-field {
      height: <?php echo get_theme_mod( 'pewc_textarea_height', false ); ?>em;
    }
    <?php if( get_theme_mod( 'pewc_block_label' ) ) { ?>
      ul.pewc-product-extra-groups .pewc-item:not(.pewc-item-checkbox) label {
        display: block !important
      }
    <?php } ?>
    .pewc-radio-image-wrapper label input:checked + img,
    .pewc-checkbox-image-wrapper label input:checked + img {
    	border-color: <?php echo get_theme_mod( 'pewc_swatch_wrapper', 0 ); ?>
    }
  </style>
  <?php
}
add_action( 'wp_head', 'pewc_customize_css');
