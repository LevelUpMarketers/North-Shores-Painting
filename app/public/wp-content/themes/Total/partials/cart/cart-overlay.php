<?php

/**
 * Cart overlay.
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! wp_script_is( 'wpex-wc-cart-overlay', 'enqueued' ) ) {
	wp_enqueue_script( 'wpex-wc-cart-overlay' );
}

$legacy_typo = totaltheme_has_classic_styles();

?>

<div id="wpex-cart-overlay" class="wpex-fs-overlay wpex-fixed wpex-inset-0 wpex-z-modal wpex-duration-400 wpex-text-white wpex-invisible wpex-opacity-0">
	<button class="wpex-fs-overlay__close wpex-close wpex-unstyled-button wpex-block wpex-fixed wpex-top-0 wpex-right-0 wpex-mr-20 wpex-mt-20 <?php echo $legacy_typo ? 'wpex-text-5xl' : 'wpex-text-base'; ?>" aria-label="<?php esc_html_e( 'Close cart', 'total' ); ?>">
		<?php echo totaltheme_get_icon(
			'material-close',
			'wpex-close__icon wpex-flex',
			$legacy_typo ? 'sm' : 'xl'
		); ?>
	</button>
	<div class="wpex-fs-overlay__inner wpex-inner wpex-scale wpex-relative wpex-top-50 wpex-max-w-100 wpex-mx-auto wpex-bg-white wpex-py-10 wpex-px-20" tabindex="0"><?php
		the_widget(
			'WC_Widget_Cart',
			[],
			[
				'before_title' => '<span class="widgettitle screen-reader-text">',
				'after_title' => '</span>',
			]
		);
	?></div>
</div>
