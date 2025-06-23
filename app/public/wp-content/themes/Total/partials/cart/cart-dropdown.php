<?php
/**
 * Header cart dropdown.
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 5.12
 */

defined( 'ABSPATH' ) || exit;

if ( ! wp_script_is( 'wpex-wc-cart-dropdown', 'enqueued' ) ) {
	wp_enqueue_script( 'wpex-wc-cart-dropdown' );
}

?>

<div id="current-shop-items-dropdown" <?php wpex_header_drop_widget_class(); ?>>
	<div id="current-shop-items-inner">
		<?php the_widget(
			'WC_Widget_Cart',
			[],
			[
				'before_title' => '<span class="widgettitle screen-reader-text">',
				'after_title'  => '</span>'
			]
		); ?>
	</div>
</div>