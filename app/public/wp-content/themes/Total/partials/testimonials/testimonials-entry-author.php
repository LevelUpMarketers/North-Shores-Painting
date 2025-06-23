<?php
/**
 * Testimonials entry author
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! wpex_has_testimonial_author() ) {
	return;
}

?>

<div <?php wpex_testimonials_entry_author_class(); ?>><?php echo wp_kses_post( wpex_get_testimonial_author() ); ?></div>
