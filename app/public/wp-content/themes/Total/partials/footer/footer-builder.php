<?php

/**
 * Footer Builder.
 *
 * @package TotalTheme
 * @subpackage Partials
 * @version 6.1
 *
 * @todo remove the entry classname from footer-builder-content?
 */

defined( 'ABSPATH' ) || exit;

$template_id = totaltheme_call_static( 'Footer\Core', 'get_template_id' );

if ( ! $template_id ) {
	return;
}

$is_edit_mode = wpex_is_footer_builder_page() && ( totaltheme_is_wpb_frontend_editor() || totaltheme_is_elementor_editor() );

$extra_class = '';

if ( $is_edit_mode && totaltheme_is_wpb_frontend_editor() ) {
	$extra_class .= ' footer-builder--vc-compose-mode';
}

if ( $color_scheme = (string) get_theme_mod( 'footer_builder_color_scheme' ) ) {
	$extra_class .=  ' ' . totaltheme_get_color_scheme_classname( $color_scheme );
}

?>

<footer id="footer-builder" class="footer-builder<?php echo $extra_class ? ' ' . esc_attr( trim( $extra_class ) ) : ''; ?>">
	<div class="footer-builder-content container entry wpex-clr">
		<?php if ( $is_edit_mode ) {
			while ( have_posts() ) : the_post();
				the_content();
			endwhile;
		} else {
			totaltheme_render_template( $template_id );
		} ?>
	</div>
</footer>
