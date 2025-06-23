<?php

/**
 * Overlay: Categories + Title Bottom Visible
 *
 * @package TotalTheme
 * @subpackage Partials
 * @version 6.1
 */

defined( 'ABSPATH' ) || exit;

if ( 'outside_link' !== $position ) {
	return;
}

$taxonomy = $args['taxonomy'] ?? wpex_get_post_type_cat_tax();
$title    = $args['post_title'] ?? get_the_title();
?>

<div class="overlay-cats-title-btm-v theme-overlay wpex-absolute wpex-bottom-0 wpex-inset-x-0 wpex-py-10 wpex-px-20 wpex-text-center wpex-text-white">
	<span class="overlay-bg wpex-bg-<?php echo totaltheme_get_overlay_bg_color(); ?> wpex-block wpex-absolute wpex-inset-0 wpex-opacity-<?php echo totaltheme_get_overlay_opacity(); ?>"></span>
	<div class="overlay-content wpex-relative">
		<?php if ( $taxonomy ) {
			wpex_list_post_terms( [
				'taxonomy' => $taxonomy,
				'class'    => 'wpex-hover-underline',
				'before'   => '<div class="overlay-cats-title-btm-v-cats wpex-italic wpex-text-sm wpex-opacity-80 wpex-child-inherit-color wpex-clr">',
				'after'    => '</div>',
				'instance' => 'overlay_categories_title-bottom_visible',
			] );
		} ?>
		<a href="<?php the_permalink(); ?>" class="overlay-cats-title-btm-v-title wpex-inherit-color wpex-font-semibold wpex-text-md wpex-no-underline"><?php echo esc_html( $title ); ?></a>
	</div>
</div>
