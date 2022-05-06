<?php

echo '<div class="tb-content">';
global $current_screen;
if ( ( $current_object = get_queried_object() ) && $current_object->term_id ) {
	if ( $current_object->description ) {
		echo do_shortcode( $current_object->description );
	}
} else {
	if ( 'excerpt' == $atts['content_display'] ) {
		echo porto_get_excerpt( (int) $atts['excerpt_length'], false, false, $current_screen && $current_screen->is_block_editor() ? false : true );
	} else {
		if ( $current_screen && $current_screen->is_block_editor() ) {
			echo do_shortcode( get_the_content() );
		} else {
			the_content();
		}
	}
}
echo '</div>';
