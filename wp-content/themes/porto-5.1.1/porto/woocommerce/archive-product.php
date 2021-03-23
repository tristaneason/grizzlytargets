<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive
 *
 * @version     3.4.0
 */

defined( 'ABSPATH' ) || exit;

get_header( 'shop' );

?>

<?php wc_get_template_part( 'archive-product-content' ); ?>

<?php
get_footer( 'shop' );
