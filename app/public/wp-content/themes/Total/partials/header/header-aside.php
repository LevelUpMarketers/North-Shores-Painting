<?php

use TotalTheme\Header\Aside as Header_Aside;

/**
 * Header aside content used in Header Style Two, Three and Four.
 *
 * @package TotalTheme
 * @subpackage Partials
 * @version 6.3
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'TotalTheme\Header\Aside' ) ) {
	return;
}

$header_style = totaltheme_call_static( 'Header\Core', 'style' );
$content      = Header_Aside::get_content();

// Display header aside if content exists or it's header style 2 and the main search is enabled
if ( $content || ( get_theme_mod( 'main_search', true ) && 'two' === $header_style ) ) :

	// Placeholder
	$placeholder = esc_html__( 'search', 'total' );
	$placeholder = (string) apply_filters( 'wpex_get_header_aside_search_form_placeholder', $placeholder );

	// Button text
	$button_text = totaltheme_get_icon( 'search' ) . '<span class="wpex-hidden wpex-ml-10">' . esc_html__( 'Search', 'total' ) . '</span>';

	// Add inline template CSS
	if ( WPEX_VC_ACTIVE && $wpb_style = totaltheme_get_instance_of( 'Integration\WPBakery\Shortcode_Inline_Style' ) ) {
		$wpb_style->render_style( Header_Aside::get_template_id() );
	}

	// Adds extra space above on mobile
	Header_Aside::mobile_spacer();
	?>

	<aside id="header-aside" <?php Header_Aside::wrapper_class(); ?>>
		<div class="header-aside-content wpex-clr"><?php
			echo do_shortcode( wp_kses_post( do_blocks( $content ) ) );
		?></div>
		<?php if ( 'two' === $header_style && wp_validate_boolean( get_theme_mod( 'header_aside_search', true ) ) ) : ?>
			<div id="header-two-search" class="wpex-float-left wpex-min-float-right wpex-mt-10">
				<form method="get" class="header-two-searchform wpex-flex" action="<?php echo esc_url( home_url( '/' ) ); ?>">
					<label for="header-two-search-input" class="screen-reader-text"><?php echo esc_attr( $placeholder ); ?></label>
					<input type="search" id="header-two-search-input" class="wpex-rounded-0" name="s" placeholder="<?php echo esc_attr( $placeholder ); ?>" required>
					<?php if ( $current_language = (string) apply_filters( 'wpml_current_language', null ) ) : ?>
						<input type="hidden" name="lang" value="<?php echo esc_attr( $current_language ); ?>">
					<?php endif; ?>
					<?php if ( totaltheme_is_integration_active( 'woocommerce' ) && get_theme_mod( 'woo_header_product_searchform', false ) ) { ?>
						<input type="hidden" name="post_type" value="product">
					<?php } ?>
					<button type="submit" id="header-two-search-submit" class="theme-button wpex-rounded-0" aria-label="<?php echo esc_html__( 'Search', 'total' ); ?>"><?php echo apply_filters( 'wpex_header_aside_search_button_text', $button_text ); ?></button>
				</form>
			</div>
		<?php endif; ?>
	</aside>

<?php endif;
