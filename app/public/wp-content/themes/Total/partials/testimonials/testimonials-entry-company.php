<?php
/**
 * Testimonials entry company
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! wpex_has_testimonial_company() ) {
	return;
}

?>

<div <?php wpex_testimonials_entry_company_class(); ?>>

	<?php if ( wpex_has_testimonial_company_link() ) : ?>

		<a href="<?php echo wpex_get_testimonial_company_url(); ?>" class="wpex-text-inherit wpex-hover-text-inherit"<?php wpex_testimonials_entry_company_link_target(); ?>><?php echo wp_kses_post( wpex_get_testimonial_company() ); ?></a>

	<?php else : ?>

		<?php echo wp_kses_post( wpex_get_testimonial_company() ); ?>

	<?php endif; ?>

</div>