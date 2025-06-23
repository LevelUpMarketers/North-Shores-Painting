<?php

defined( 'ABSPATH' ) || exit;

if ( function_exists( 'totalthemecore_get_instance_of' ) ) {
	$post_cards_instance = totalthemecore_get_instance_of( 'Vcex\Post_Cards' );
	if ( is_callable( [ $post_cards_instance, 'get_atts' ] ) ) {
		$post_cards_atts = $post_cards_instance->get_atts();
		if ( is_array( $post_cards_atts ) && isset( $post_cards_atts['display_type'] )
			&& in_array( $post_cards_atts['display_type'], [ 'ul_list', 'ol_list' ] )
		) {
			return $this->get_element( [
				'class'   => 'wpex-heading',
				'link'    => true,
				'content' => $this->get_the_title(),
			] );
		}
	}
}

return $this->get_title( [
	'class' => 'wpex-heading',
 ] );
