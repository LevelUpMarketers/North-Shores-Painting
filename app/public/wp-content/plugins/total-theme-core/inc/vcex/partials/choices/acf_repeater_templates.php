<?php

defined( 'ABSPATH') || exit;

$choices = [
	'' => esc_html( '— Select —', 'total-theme-core' ),
];

if ( \function_exists( 'totaltheme_call_non_static' ) ) {
	$templates = (array) totaltheme_call_non_static( 'Theme_Builder', 'get_template_choices', 'acf_repeater', false );
	if ( $templates ) {
		$choices = $choices + $templates;
	}
}

return $choices;
