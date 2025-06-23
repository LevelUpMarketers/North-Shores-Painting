<?php

/**
 * Title Category Visible Overlay.
 *
 * @package TotalTheme
 * @subpackage Partials
 * @version 6.1
 */

defined( 'ABSPATH' ) || exit;

// Only used for inside position.
if ( 'inside_link' !== $position ) {
	return;
}

$taxonomy = $args['taxonomy'] ?? wpex_get_post_type_cat_tax();

?>

<div class="overlay-title-category-visible theme-overlay wpex-absolute wpex-inset-0 wpex-flex wpex-items-center wpex-justify-center wpex-text-center">

	<div class="overlay-bg wpex-bg-<?php echo totaltheme_get_overlay_bg_color(); ?> wpex-absolute wpex-inset-0 wpex-opacity-<?php echo totaltheme_get_overlay_opacity(); ?>"></div>

	<div class="overlay-content wpex-relative wpex-text-white wpex-p-15 wpex-clr">

		<div class="overlay-title wpex-text-lg"><?php
			echo esc_html( $args['post_title'] ?? get_the_title() );
		?></div>

		<?php if ( $taxonomy ) {
			wpex_list_post_terms( [
				'taxonomy'   => $taxonomy,
				'before'     => '<div class="overlay-terms wpex-opacity-80 wpex-italic">',
				'after'      => '</div>',
				'show_links' => false,
				'instance'   => 'overlay_title-category-visible',
			] );
		} ?>

	</div>

</div>
