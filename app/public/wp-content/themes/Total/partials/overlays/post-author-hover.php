<?php

/**
 * Overlay: Post Author Hover.
 *
 * @package TotalTheme
 * @subpackage Partials
 * @version 5.10
 */

defined( 'ABSPATH' ) || exit;

// Only used for inside position.
if ( 'inside_link' !== $position ) {
	return;
}

// Get post author.
$author = $args['post_author'] ?? get_the_author();

?>

<div class="overlay-post-author theme-overlay overlay-hide wpex-absolute wpex-inset-0 wpex-transition-all wpex-duration-<?php echo totaltheme_get_overlay_speed(); ?> wpex-flex wpex-items-end wpex-p-20 wpex-text-white" aria-hidden="true">
	<span class="overlay-bg wpex-bg-center wpex-bg-no-repeat wpex-bg-<?php echo totaltheme_get_overlay_bg_color(); ?> wpex-block wpex-absolute wpex-inset-0 wpex-opacity-<?php echo totaltheme_get_overlay_opacity( '20' ); ?>"></span>
	<div class="wpex-flex wpex-items-center wpex-relative">
		<div class="wpex-mr-10"><?php
			echo get_avatar(
				get_the_author_meta( 'ID' ),
				32,
				'',
				'',
				[
					'class'      => 'wpex-rounded-full wpex-object-cover',
					'extra_attr' => 'style="aspect-ratio:1/1;"', // prevents issues with custom aspect ratios.
				]
			);
		?></div>
		<div><?php echo esc_html( $author ); ?></div>
	</div>
</div>
