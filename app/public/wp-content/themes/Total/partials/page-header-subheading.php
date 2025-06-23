<?php

/**
 * Page subheading output.
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 5.10
 */

defined( 'ABSPATH' ) || exit;

$subheading = totaltheme_call_static( 'Page\Header', 'get_subheading' );

if ( ! $subheading ) {
	return;
}

?>

<div <?php wpex_page_header_subheading_class(); ?>><?php echo do_shortcode( wp_kses_post( $subheading ) ); ?></div>
