<?php

defined( 'ABSPATH' ) || exit;

if ( empty( $content ) ) {
	return;
}

$html = '';

if ( is_callable( [ 'WPBMap', 'addAllMappedShortcodes' ] ) ) {
	\WPBMap::addAllMappedShortcodes(); // Fixes issues with AJAX load more.
}

$content = totaltheme_shortcode_unautop( $content ); // removes <p> tags around shortcodes.
$content = do_shortcode( $content );

if ( totaltheme_call_static( 'Pagination\Load_More', 'is_doing_ajax' ) || ( function_exists( 'vcex_doing_loadmore' ) && vcex_doing_loadmore() ) ) {
	// Don't create extra CSS.
} else {
	$add_to_parsed = true;
	if ( $wpb_style = totaltheme_get_instance_of( 'Integration\WPBakery\Shortcode_Inline_Style' ) ) {
		$html .= $wpb_style->get_style( [ $template_id, $this->post_id ], $add_to_parsed );
	}
}

if ( $this->has_link() ) {
	$content = str_replace( '<a', '<span', $content );
	$content = str_replace( '</a>', '</span>', $content );

	$html .= $this->get_link_open( [
		'class' => 'wpex-card-inner wpex-no-underline wpex-inherit-color',
		'attributes' => [
			'aria-label' => get_the_title( $this->post_id ),
		],
	] );

	$html .= $content;
	$html .= $this->get_link_close();
} else {
	$html = $html .= '<div class="wpex-card-inner">' . $content . '</div>';
}

return $html;
