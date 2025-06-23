<?php
/**
 * Blog single thumbnail.
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 6.1
 */

defined( 'ABSPATH' ) || exit;

if ( ! has_post_thumbnail() ) {
	return;
}

if ( wpex_has_blog_single_thumbnail_lightbox() ) :
	wpex_enqueue_lightbox_scripts();
	?>
	<a href="<?php echo wpex_get_lightbox_image( get_post_thumbnail_id() ); ?>" title="<?php esc_attr_e( 'Enlarge Image', 'total' ); ?>" class="wpex-lightbox<?php wpex_entry_image_animation_classes(); ?>"><?php wpex_blog_post_thumbnail(); ?></a>
	<?php
else :
	wpex_blog_post_thumbnail();
endif;

wpex_blog_single_thumbnail_caption();