<?php

/**
 * CTP entry thumbnail
 *
 * @package TotalTheme
 * @subpackage Partials
 * @version 5.10
 *
 */

defined( 'ABSPATH' ) || exit;

$overlay = wpex_cpt_entry_overlay_style();

?>

<a href="<?php wpex_permalink(); ?>" title="<?php wpex_esc_title(); ?>" class="cpt-entry-media-link<?php wpex_entry_image_animation_classes(); ?>">
	<?php wpex_cpt_entry_thumbnail(); ?>
	<?php wpex_entry_media_after( get_post_type() . '_entry' ); ?>
	<?php totaltheme_render_overlay( 'inside_link', $overlay ); ?>
</a>

<?php
totaltheme_render_overlay( 'outside_link', $overlay );