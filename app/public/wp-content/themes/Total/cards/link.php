<?php

defined( 'ABSPATH' ) || exit;

$html = '';

$class = 'wpex-card-title wpex-self-start theme-txt-link';

if ( function_exists( 'totalthemecore_get_instance_of' ) ) {
	$post_cards_instance = totalthemecore_get_instance_of( 'Vcex\Post_Cards' );
	if ( is_callable( [ $post_cards_instance, 'get_atts' ] ) ) {
		$post_cards_atts = $post_cards_instance->get_atts();
		if ( is_array( $post_cards_atts ) && isset( $post_cards_atts['display_type'] )
			&& in_array( $post_cards_atts['display_type'], [ 'ul_list', 'ol_list' ] )
		) {
			$class = trim( str_replace( 'wpex-card-title', '', $class ) );
		}
	}
}

if ( get_queried_object_id() === $this->post_id ) {
	$class .= ' active';
}

$link_open = $this->get_link_open( [
	'class' => $class,
] );

if ( ! $link_open ) {
	return;
}

$html .= $link_open;

$html .= $this->get_the_title();

$html .= $this->get_link_close();

return $html;
