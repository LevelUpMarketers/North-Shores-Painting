<?php

/**
 * Portfolio entry thumbnail
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 5.10
 */

defined( 'ABSPATH' ) || exit;

$overlay = totaltheme_call_static(
	'Overlays',
	'get_entry_image_overlay_style',
	'portfolio'
);

?>

<a href="<?php the_permalink(); ?>" title="<?php wpex_esc_title(); ?>" class="portfolio-entry-media-link">
	<?php wpex_portfolio_entry_thumbnail(); ?>
	<?php wpex_entry_media_after( 'portfolio' ); ?>
	<?php totaltheme_render_overlay( 'inside_link', $overlay ); ?>
</a>

<?php
totaltheme_render_overlay( 'outside_link', $overlay );
