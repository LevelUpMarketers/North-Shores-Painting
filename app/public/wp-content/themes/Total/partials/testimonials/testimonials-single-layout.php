<?php
/**
 * Testimonials single post layout.
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 5.5
 */

defined( 'ABSPATH' ) || exit;

?>

<div id="single-blocks" class="wpex-clr">

	<div id="testimonials-single-content" <?php wpex_testimonials_single_content_class(); ?>>

		<?php
		// "Quote" style.
		if ( 'blockquote' === wpex_get_testimonials_single_layout() ) :

			wpex_set_loop_instance( 'singular' );

			get_template_part( 'partials/testimonials/testimonials-entry' );

		// Display full content.
		else : ?>

			<?php the_content(); ?>

		<?php endif; ?>

	</div>

	<?php
	// Displays comments if enabled.
	if ( get_theme_mod( 'testimonials_comments', false ) ) :

		get_template_part( 'partials/testimonials/testimonials-single-comments' );

	endif; ?>

</div>