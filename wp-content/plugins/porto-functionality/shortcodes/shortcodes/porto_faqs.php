<?php

// Porto FAQs
add_action( 'vc_after_init', 'porto_load_faqs_shortcode' );

function porto_load_faqs_shortcode() {
	$animation_type     = porto_vc_animation_type();
	$animation_duration = porto_vc_animation_duration();
	$animation_delay    = porto_vc_animation_delay();
	$custom_class       = porto_vc_custom_class();

	vc_map(
		array(
			'name'        => 'Porto ' . __( 'FAQs', 'porto-functionality' ),
			'base'        => 'porto_faqs',
			'category'    => __( 'Porto', 'porto-functionality' ),
			'description' => __( 'Show faqs with accordion', 'porto-functionality' ),
			'icon'        => 'fas fa-question-circle',
			'params'      => array(
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Title', 'porto-functionality' ),
					'param_name'  => 'title',
					'admin_label' => true,
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Category IDs', 'porto-functionality' ),
					'description' => __( 'comma separated list of category ids', 'porto-functionality' ),
					'param_name'  => 'cats',
					'admin_label' => true,
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'FAQ IDs', 'porto-functionality' ),
					'description' => __( 'comma separated list of faq ids', 'porto-functionality' ),
					'param_name'  => 'post_in',
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'FAQs Count (per page)', 'porto-functionality' ),
					'param_name' => 'number',
					'value'      => '8',
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Show Archive Link', 'porto-functionality' ),
					'param_name' => 'view_more',
					'value'      => array( __( 'Yes', 'js_composer' ) => 'yes' ),
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Extra class name for Archive Link', 'porto-functionality' ),
					'param_name' => 'view_more_class',
					'dependency' => array(
						'element'   => 'view_more',
						'not_empty' => true,
					),
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Show Category Filter', 'porto-functionality' ),
					'param_name' => 'filter',
					'value'      => array( __( 'Yes', 'js_composer' ) => 'yes' ),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Filter Type', 'porto-functionality' ),
					'param_name' => 'filter_type',
					'std'        => '',
					'value'      => array(
						__( 'Filter using Javascript/CSS', 'porto-functionality' ) => '',
						__( 'Ajax Loading', 'porto-functionality' ) => 'ajax',
					),
					'dependency' => array(
						'element'   => 'filter',
						'not_empty' => true,
					),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Pagination Style', 'porto-functionality' ),
					'param_name' => 'pagination',
					'std'        => '',
					'value'      => array(
						__( 'None', 'porto-functionality' ) => '',
						__( 'Ajax Pagination', 'porto-functionality' ) => 'yes',
						__( 'Infinite Scroll', 'porto-functionality' ) => 'infinite',
						__( 'Load More (Button)', 'porto-functionality' ) => 'load_more',
					),
				),
				$custom_class,
				$animation_type,
				$animation_duration,
				$animation_delay,
			),
		)
	);

	if ( ! class_exists( 'WPBakeryShortCode_Porto_Faqs' ) ) {
		class WPBakeryShortCode_Porto_Faqs extends WPBakeryShortCode {
		}
	}
}
