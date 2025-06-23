<?php

defined( 'ABSPATH' ) || exit;

$dark_mode_enabled = $dark_mode_enabled ?? totaltheme_call_static( 'Dark_Mode', 'is_enabled' );
$fields = [];

$fields[] = [
	'id' => 'fixed_header_menu',
	'default' => true,
	'control' => [
		'label' => esc_html__( 'Sticky Header Menu', 'total' ),
		'type' => 'totaltheme_toggle',
		'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::can_sticky_menu',
	],
];

$fields[] = [
	'id' => 'sticky_menu_header_notice',
	'control' => [
		'description' => esc_html__( 'Your selected header style uses a sticky menu on desktop. The settings below will apply to the mobile view only.', 'total' ),
		'type' => 'totaltheme_notice',
		'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::has_sticky_menu_notice',
	],
];

$fields[] = [
	'id' => 'fixed_header',
	'default' => true,
	'control' => [
		'label' => esc_html__( 'Sticky Header', 'total' ),
		'type' => 'totaltheme_toggle',
		'description' => esc_html__( 'Enable to keep the header visible as you scroll down the page.', 'total' ),
	],
];

$fields[] = [
	'id' => 'fixed_header_style',
	'default' => 'standard',
	'sanitize_callback' => 'esc_html',
	'control' => [
		'label' => esc_html__( 'Style', 'total' ),
		'type' => 'select',
		'choices' => 'TotalTheme\Header\Sticky::style_choices',
		'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::has_sticky_header',
		'description' => esc_html__( 'If you are using a header style with a sticky menu on desktop, these settings will apply to mobile only (if enabled via the setting below).', 'total' ),
	],
];

$fields[] = [
	'id' => 'fixed_header_mobile',
	'sanitize_callback' => 'esc_html',
	'control' => [
		'label' => esc_html__( 'Sticky on Mobile', 'total' ),
		'description' => esc_html__( 'If disabled the sticky header will only function on desktops.', 'total' ),
		'type' => 'totaltheme_toggle',
		'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::has_sticky_header',
	],
];

$fields[] = [
	'id' => 'has_fixed_header_dropshadow',
	'sanitize_callback' => 'esc_html',
	'default' => true,
	'control' => [
		'label' => esc_html__( 'Sticky Header Shadow', 'total' ),
		'description' => esc_html__( 'Enables a drop shadow on the header while it\'s sticky.', 'total' ),
		'type' => 'totaltheme_toggle',
		'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::has_sticky_header',
	],
];

$fields[] = [
	'id' => 'fixed_header_start_position',
	'sanitize_callback' => 'esc_html',
	'control' => [
		'label' => esc_html__( 'Sticky Start Position', 'total' ),
		'type' => 'text',
		'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::has_sticky_header',
		'description' => esc_html__( 'By default, the header becomes sticky as soon as you reach the header while scrolling. You can use this field to enter a number (in pixels) to offset the point at which the header becomes sticky (based on the top of the page) or the classname or ID of another element so that the header becomes sticky when it reaches that point (example: #my-custom-div).', 'total' ),
	],
];

$fields[] = [
	'id' => 'fixed_header_shrink_start_height',
	'control' => [
		'label' => esc_html__( 'Logo Start Height', 'total' ),
		'type' => 'totaltheme_length_unit',
		'units' => [ 'px' ],
		'description' => esc_html__( 'Applies a max height to your header logo to provide a smoother animation when shrinking.', 'total' ),
		'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::has_sticky_header_shrink',
		'placeholder' => '60',
	],
	'inline_css' => [
		'target' => ':root',
		'alter' => '--wpex-site-header-shrink-start-height',
		'sanitize' => 'px',
	],
];

$fields[] = [
	'id' => 'fixed_header_shrink_end_height',
	'control' => [
		'label' => esc_html__( 'Shrunk Header Height', 'total' ),
		'type' => 'totaltheme_length_unit',
		'units' => [ 'px' ],
		'description' => esc_html__( 'Unless you are using one of the "Flex" header styles, the end height will be set to your Shrunk Height value plus 20px for a top and bottom padding of 10px.', 'total' ),
		'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::has_sticky_header_shrink',
		'placeholder' => '50',
	],
	'inline_css' => [
		'target' => ':root',
		'alter' => '--wpex-site-header-shrink-end-height',
		'sanitize' => 'px',
	],
];

$fields[] = [
	'id' => 'fixed_header_shrink_end_logo_font_size',
	'transport' => 'postMessage',
	'control' => [
		'label' => esc_html__( 'Logo Shrunk Font Size', 'total' ),
		'type' => 'totaltheme_length_unit',
		'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::has_sticky_header_shrink',
		'description' => esc_html__( 'If you are not using an image logo you can enter a font size for your text logo when the sticky header is shrunk.', 'total' ),
	],
	'inline_css' => [
		'target' => '#site-header.sticky-header-shrunk .site-logo-text',
		'alter' => 'font-size',
	],
];

$fields[] = [
	'id' => 'fixed_header_opacity',
	'transport' => 'postMessage',
	'control' => [
		'type' => 'number',
		'label' => esc_html__( 'Opacity', 'total' ),
		'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::has_sticky_header',
		'input_attrs' => [
			'min'  => 0.1,
			'max'  => 1,
			'step' => 0.1,
			'placeholder' => 1,
		],
	],
	'inline_css' => [
		'target' => '.wpex-sticky-header-holder.is-sticky #site-header',
		'alter' => 'opacity',
	],
];

$fields[] = [
	'id' => 'fixed_header_logo',
	'control' => [
		'label' => esc_html__( 'Sticky Logo', 'total' ),
		'type' => 'media',
		'mime_type' => 'image',
		'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::can_sticky_header_custom_logo',
		'description' => esc_html__( 'If this custom logo is a different size, for best results go to the Logo section and apply a custom height to your logo.', 'total' ),
	],
];

$fields[] = [
	'id' => 'fixed_header_logo_retina',
	'control' => [
		'label' => esc_html__( 'Sticky Logo Retina', 'total' ),
		'type' => 'media',
		'mime_type' => 'image',
		'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::has_sticky_header_custom_logo',
	],
];

if ( $dark_mode_enabled ) {
	$fields[] = [
		'id' => 'fixed_header_logo_dark',
		'control' => [
			'label' => esc_html__( 'Dark Mode: Sticky Logo', 'total' ),
			'type' => 'media',
			'mime_type' => 'image',
			'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::has_sticky_header_custom_logo',
		],
	];
	$fields[] = [
		'id' => 'fixed_header_logo_retina_dark',
		'control' => [
			'label' => esc_html__( 'Dark Mode: Sticky Logo Retina', 'total' ),
			'type' => 'media',
			'mime_type' => 'image',
			'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::has_sticky_header_custom_logo',
		],
	];
}

$this->sections['wpex_header_fixed'] = [
	'title' => esc_html__( 'Sticky Header', 'total' ),
	'panel' => 'wpex_header',
	'settings' => $fields,
];


unset( $fields );
