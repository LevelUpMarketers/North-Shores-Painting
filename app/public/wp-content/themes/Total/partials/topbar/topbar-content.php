<?php

use TotalTheme\Topbar\Core as Topbar;

/**
 * Topbar content.
 *
 * @package TotalTheme
 * @subpackage Partials
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'TotalTheme\Topbar\Core' ) ) {
	return;
}

$content = totaltheme_call_static( 'Topbar\Core', 'get_content' );

if ( $content || has_nav_menu( 'topbar_menu' ) ) : ?>

	<div id="top-bar-content" <?php Topbar::content_class(); ?>><?php

		// Get topbar menu.
		get_template_part( 'partials/topbar/topbar-menu' );

		// Display content.
		if ( $content ) {
			if ( $wpb_style = totaltheme_get_instance_of( 'TotalTheme\Integration\WPBakery\Shortcode_Inline_Style' ) ) {
				$wpb_style->render_style( Topbar::get_template_id() );
			}
			echo do_shortcode( totaltheme_replace_vars( wp_kses_post( $content ) ) );
		}

	?></div>

<?php
endif;
