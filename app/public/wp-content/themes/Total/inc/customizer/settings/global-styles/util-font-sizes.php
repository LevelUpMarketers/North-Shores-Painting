<?php

defined( 'ABSPATH' ) || exit;

$font_sizes  = wpex_utl_font_sizes();
$legacy_typo = $legacy_typo ?? totaltheme_has_classic_styles();

if ( ! $font_sizes ) {
	return;
}

if ( $legacy_typo ) {
	unset( $font_sizes['base'] );
}

$settings = [];

foreach ( $font_sizes as $key => $label ) {
	if ( ! $key ) {
		continue;
	}

	$settings[] = [
		'id' => "font_size_{$key}",
		'transport' => 'postMessage',
		'control' => [
			'type' => 'totaltheme_length_unit',
			'label' => sprintf( esc_html_x( '%s Font size', 'Noun: Customizer setting for controlling the theme preset font sizes.', 'total' ), $label ),
		],
		'inline_css' => [
			'target' => ':root',
			'alter' => "--wpex-text-{$key}",
		],
	];
}

$this->sections['wpex_util_font_sizes'] = [
	'title'    => esc_html__( 'Preset Font Sizes', 'total' ),
	'panel'    => 'wpex_global_styles',
	'settings' => $settings,
];

// Free up memory
unset( $font_sizes, $settings );