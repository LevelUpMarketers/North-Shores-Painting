<?php

/**
 * Overlay: Title Category Hover.
 *
 * @package TotalTheme
 * @subpackage Partials
 * @version 6.1
 */

defined( 'ABSPATH' ) || exit;

// Only used for inside position
if ( 'inside_link' !== $position ) {
	return;
}

$speed    = totaltheme_get_overlay_speed();
$taxonomy = $args['taxonomy'] ?? wpex_get_post_type_cat_tax();

?>

<div class="overlay-title-category-hover overlay-hide theme-overlay wpex-absolute wpex-inset-0 wpex-transition-all wpex-duration-<?php echo intval( $speed ); ?> wpex-overflow-hidden wpex-flex wpex-items-center wpex-justify-center wpex-text-center">
	<div class="overlay-bg wpex-bg-<?php echo totaltheme_get_overlay_bg_color(); ?> wpex-absolute wpex-inset-0 wpex-opacity-<?php echo totaltheme_get_overlay_opacity(); ?>"></div>
	<div class="overlay-content overlay-scale wpex-relative wpex-text-white wpex-p-15 wpex-duration-<?php echo intval( $speed ); ?> wpex-transition-transform wpex-clr">
		<div class="overlay-title wpex-text-lg"><?php
			$title = $args['post_title'] ?? get_the_title();
			echo esc_html( $title );
		?></div>
		<?php if ( $taxonomy ) {
			wpex_list_post_terms( [
				'taxonomy'   => $taxonomy,
				'before'     => '<div class="overlay-terms wpex-opacity-80 wpex-italic">',
				'after'      => '</div>',
				'show_links' => false,
				'instance'   => 'overlay_title-category-hover',
			] );
		} ?>
	</div>
</div>
