<?php
/**
 * Renders the page header title.
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 5.10.1
 */

defined( 'ABSPATH' ) || exit;

$args = wpex_page_header_title_args();

if ( empty( $args['string'] ) ) {
	return;
}

?>

<?php wpex_hook_page_header_title_before(); ?>

<<?php wpex_page_header_title_tag( $args ); ?> <?php wpex_page_header_title_class(); ?>>

	<span><?php echo do_shortcode( wp_kses_post( $args['string'] ) ); ?></span>

</<?php wpex_page_header_title_tag( $args ); ?>>

<?php wpex_hook_page_header_title_after(); ?>