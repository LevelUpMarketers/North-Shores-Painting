<?php

/**
 * Staff entry thumbnail
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 5.10
 */

defined( 'ABSPATH' ) || exit;

$overlay = totaltheme_call_static(
	'Overlays',
	'get_entry_image_overlay_style',
	'staff'
);

$has_links = get_theme_mod( 'staff_links_enable', true );

if ( $has_links ) { ?>
	<a href="<?php wpex_permalink(); ?>" title="<?php wpex_esc_title(); ?>" class="staff-entry-media-link">
<?php }
	wpex_staff_entry_thumbnail();
	wpex_entry_media_after( 'staff' );
	totaltheme_render_overlay( 'inside_link', $overlay );
if ( $has_links ) { ?>
	</a>
<?php }

totaltheme_render_overlay( 'outside_link', $overlay );
