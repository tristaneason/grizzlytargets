<?php
$output = '';
extract(
	shortcode_atts(
		array(
			'type'               => '',
			'position'           => '',
			'offset'             => '',
			'thickness'          => 3,
			'bgcolor'            => '',
			'active_bgcolor'     => '',
			'animation_type'     => '',
			'animation_duration' => 1000,
			'animation_delay'    => 0,
			'el_class'           => '',
		),
		$atts
	)
);

$el_class = porto_shortcode_extract_class( $el_class );

if ( 'circle' == $type ) {

} else {
	$output .= '<div class="porto-scroll-progress">';
	$output .= '<div class="porto-scroll-progress-inner"></div>';
	$output .= '</div>';
}

echo porto_filter_output( $output );
