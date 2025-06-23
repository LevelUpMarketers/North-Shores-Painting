<?php

use TotalTheme\Topbar\Social as Topbar_Social;

/**
 * Topbar social profiles.
 *
 * @package TotalTheme
 * @subpackage Partials
 * @version 5.10
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'TotalTheme\Topbar\Social' ) || ! Topbar_Social::is_enabled() ) {
	return;
}

// Get alt content.
$social_alt = Topbar_Social::get_alt_content();

// Display Social alternative.
if ( ! empty( $social_alt ) ) :

	if ( WPEX_VC_ACTIVE && $wpb_style = totaltheme_get_instance_of( 'Integration\WPBakery\Shortcode_Inline_Style' ) ) {
		$wpb_style->render_style( Topbar_Social::get_template_id() );
	}

	?>

	<div id="top-bar-social-alt" <?php Topbar_Social::wrapper_class(); ?>><?php

		echo do_shortcode( $social_alt );

	?></div>

<?php
// If social alternative is defined lets bail.
return;

// End social alternative check.
endif; ?>

<div id="top-bar-social" <?php Topbar_Social::wrapper_class(); ?>><?php
	wpex_hook_topbar_social_top();
	Topbar_Social::render_list();
	wpex_hook_topbar_social_bottom();
?></div>