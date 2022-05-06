<?php

// Porto Scroll Progress
add_action( 'vc_after_init', 'porto_load_scroll_progress_shortcode' );

function porto_load_scroll_progress_shortcode() {
	$animation_type     = porto_vc_animation_type();
	$animation_duration = porto_vc_animation_duration();
	$animation_delay    = porto_vc_animation_delay();
	$custom_class       = porto_vc_custom_class();

	vc_map(
		array(
			'name'        => 'Porto ' . __( 'Scroll Progress', 'porto-functionality' ),
			'base'        => 'porto_scroll_progress',
			'category'    => __( 'Porto', 'porto-functionality' ),
			'description' => __( 'display scroll progress bar in some positions.', 'porto-functionality' ),
			'icon'        => 'fas fa-scroll',
			'params'      => array(
				array(
					'type'        => 'dropdown',
					'heading'     => __( 'Type & Position', 'porto-functionality' ),
					'description' => __( 'If you select "Around the Scroll to Top button", default scroll to top button will be hidden.', 'porto-functionality' ),
					'param_name'  => 'type',
					'std'         => '',
					'value'       => array(
						__( 'Horizontal progress bar', 'porto-functionality' ) => '',
						__( 'Around the Scroll to Top button', 'porto-functionality' ) => 'circle',
					),
					'admin_label' => true,
				),
				array(
					'type'        => 'dropdown',
					'heading'     => __( 'Is Fixed Position?', 'porto-functionality' ),
					'param_name'  => 'position',
					'std'         => '',
					'value'       => array(
						__( 'No', 'porto-functionality' ) => '',
						__( 'Fixed on Top', 'porto-functionality' ) => 'top',
						__( 'Fixed on Bottom', 'porto-functionality' ) => 'bottom',
					),
					'admin_label' => true,
					'dependency'  => array(
						'element' => 'type',
						'value'   => array( '' ),
					),
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Offset Height', 'porto-functionality' ),
					'param_name'  => 'offset',
					'description' => __( 'Please input value including any valid css unit or jQuery selector. (ex: 10px, 5rem or .header-main)', 'porto-functionality' ),
					'dependency'  => array(
						'element' => 'position',
						'value'   => array( 'top', 'bottom' ),
					),
				),
				array(
					'type'        => 'number',
					'heading'     => __( 'Thickness', 'porto-functionality' ),
					'param_name'  => 'thickness',
					'value'       => 3,
					'min'         => 1,
					'max'         => 20,
					'suffix'      => 'px',
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Bar Color', 'porto-functionality' ),
					'param_name' => 'bgcolor',
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Active Bar Color', 'porto-functionality' ),
					'param_name' => 'active_bgcolor',
				),
				$custom_class,
				$animation_type,
				$animation_duration,
				$animation_delay,
			),
		)
	);

	if ( ! class_exists( 'WPBakeryShortCode_Porto_Scroll_Progress' ) ) {
		class WPBakeryShortCode_Porto_Scroll_Progress extends WPBakeryShortCode {
		}
	}
}
