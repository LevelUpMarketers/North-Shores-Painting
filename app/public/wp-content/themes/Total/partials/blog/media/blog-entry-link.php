<?php

/**
 * Blog entry link format
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 5.10
 */

defined( 'ABSPATH' ) || exit;

$overlay = totaltheme_call_static(
	'Overlays',
	'get_entry_image_overlay_style',
	'post'
);

?>

<a href="<?php wpex_permalink(); ?>" title="<?php wpex_esc_title(); ?>" class="blog-entry-media-link">
	<?php echo wpex_get_blog_entry_thumbnail(); ?>
	<?php wpex_entry_media_after( 'blog' ); ?>
	<?php totaltheme_render_overlay( 'inside_link', $overlay ); ?>
</a>

<?php
totaltheme_render_overlay( 'outside_link', $overlay );
