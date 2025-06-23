<?php

use TotalTheme\Footer\Callout as Footer_Callout;

/**
 * Footer callout
 *
 * @package TotalTheme
 * @subpackage Partials
 * @version 5.10
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'TotalTheme\Footer\Callout' ) || ! Footer_Callout::is_enabled() ) {
	return;
}

$content    = Footer_Callout::get_content();
$has_button = (bool) Footer_Callout::get_button();

if ( ! $content && ! $has_button ) {
	return;
}

$aria_html_safe = '';
$aria_label = wpex_get_aria_label( 'footer_callout' );

if ( $aria_label && is_string( $aria_label ) ) {
	$aria_html_safe = 'role="region" aria-label="' . esc_attr( $aria_label ) . '"';
}

?>

<div id="footer-callout-wrap" <?php Footer_Callout::wrapper_class(); ?><?php echo $aria_html_safe; // @codingStandardsIgnoreLine ?>>

	<div id="footer-callout" <?php Footer_Callout::inner_class(); ?>>

		<?php if ( $content ) { ?>

			<div id="footer-callout-left" <?php Footer_Callout::content_class(); ?>><?php
				echo do_shortcode( wp_kses_post( $content ) );
			?></div>

			<?php if ( $has_button ) { ?>

				<div id="footer-callout-right" <?php Footer_Callout::button_class(); ?>><?php

					Footer_Callout::render_button();

				?></div>

			<?php } ?>

		<?php } elseif ( $has_button ) { ?>

			<?php Footer_Callout::render_button(); ?>

		<?php } ?>

	</div>

</div>
