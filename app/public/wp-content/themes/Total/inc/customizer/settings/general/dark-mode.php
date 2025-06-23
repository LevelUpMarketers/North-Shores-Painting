<?php

defined( 'ABSPATH' ) || exit;

$header_is_custom = $header_is_custom ?? totaltheme_call_static( 'Header\Core', 'is_custom' );

$settings = [
	[
		'id' => 'dark_mode_check_system_pref',
		'default' => true,
		'control' => [
			'type' => 'totaltheme_toggle',
			'label' => esc_html__( 'Check System Preference', 'total' ),
		],
	],
	[
		'id' => 'dark_mode_icon_dark',
		'default' => 'circle-half-stroke',
		'control' => [
			'type' => 'totaltheme_icon',
			'label' => esc_html__( 'Dark Icon', 'total' ),
			'choices' => [ 'circle-half-stroke', 'moon-o', 'moon', 'material-dark-mode' ],
			'fallback' => 'circle-half-stroke',
		],
	],
	[
		'id' => 'dark_mode_icon_light',
		'default' => 'circle-half-stroke',
		'control' => [
			'type' => 'totaltheme_icon',
			'label' => esc_html__( 'Light Icon', 'total' ),
			'choices' => [ 'circle-half-stroke', 'moon', 'sun', 'sun-o', 'material-light-mode' ],
			'fallback' => 'circle-half-stroke',
		],
	],
];

if ( ! $header_is_custom ) {
	$settings[] = [
		'id' => 'dark_mode_menu_icon',
		'default' => true,
		'control' => [
			'type' => 'totaltheme_toggle',
			'label' => esc_html__( 'Menu Icon', 'total' ),
			'description' => esc_html__( 'Enable to automatically display the Light/Dark mode toggle icon in the main menu menu).', 'total' ),
		],
	];
	$settings[] = [
		'id' => 'dark_mode_toggle_font_size',
		'transport' => 'postMessage',
		'control' => [
			'label' => esc_html__( 'Header/Menu Icon Font Size', 'total' ),
			'desc' => esc_html__( 'Controls the font size of the dark mode icon when displayed in the header menu or header aside area.', 'total' ),
			'type' => 'totaltheme_length_unit',
		],
		'inline_css' => [
			'target' => '#site-navigation .menu-item-theme-toggle__icon,.wpex-header-dark-mode-icon__icon',
			'alter' => 'font-size',
		],
	];
}

$this->sections['wpex_dark_mode'] = [
	'title'    => esc_html__( 'Dark Mode', 'total' ),
	'panel'    => 'wpex_general',
	'settings' => $settings,
];

unset( $settings );
