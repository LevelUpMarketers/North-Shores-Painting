<?php

/**
 * Togglebar content.
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;

if ( $content = wpex_togglebar_content() ) {
	if ( WPEX_VC_ACTIVE && $wpb_style = totaltheme_get_instance_of( 'TotalTheme\Integration\WPBakery\Shortcode_Inline_Style' ) ) {
		$wpb_style->render_style( wpex_togglebar_content_id() );
	}
	?>
	<div class="entry wpex-clr"><?php
		echo do_shortcode( wp_kses_post( $content ) );
	?></div>
<?php
}
