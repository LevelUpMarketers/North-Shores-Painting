<?php

defined( 'ABSPATH' ) || exit;

$dark_mode_enabled = $dark_mode_enabled ?? totaltheme_call_static( 'Dark_Mode', 'is_enabled' );
$logo_settings     = [];

$logo_settings[] = [
	'id' => 'logo_has_link',
	'default' => true,
	'control' => [
		'label' => esc_html__( 'Logo Link', 'total' ),
		'type' => 'totaltheme_toggle',
	],
];

$logo_settings[] = [
	'id' => 'logo_link_url',
	'sanitize_callback' => 'sanitize_url',
	'control' => [
		'label' => esc_html__( 'Logo Link', 'total' ),
		'type' => 'text',
		'input_attrs' => [
			'placeholder' => \esc_attr( esc_url( \home_url( '/' ) ) ),
		],
	],
	'control_display' => [
		'check' => 'logo_has_link',
		'value' => 'true',
	],
];

$logo_settings[] = [
	'id' => 'logo_text',
	'control' => [
		'type' => 'text',
		'label' => esc_html__( 'Logo Text', 'total' ),
		'input_attrs' => [
			'placeholder' => esc_html( get_bloginfo( 'name' ) ),
		],
		'description' => esc_html__( 'By default the theme uses your "Site Title" for the logo text but you can enter a custom text here to override it. This will also be used for the logo alt tag when displaying an image based logo.', 'total' ),
	],
];

$logo_settings[] = [
	'id' => 'logo_top_margin',
	'transport' => 'postMessage',
	'control' => [
		'type' => 'totaltheme_length_unit',
		'label' => esc_html__( 'Top Padding', 'total' ),
		'placeholder' => '0',
	],
	'inline_css' => [
		'target' => '.logo-padding',
		'alter' => 'padding-block-start',
		'sanitize' => 'fallback_px',
	],
	'control_display' => [
		'check' => 'header_style',
		'value' => [ 'seven', 'eight', 'nine', 'ten', 'six' ],
		'compare' => 'not_equal',
	],
];

$logo_settings[] = [
	'id' => 'logo_bottom_margin',
	'transport' => 'postMessage',
	'control' => [
		'type' => 'totaltheme_length_unit',
		'label' => esc_html__( 'Bottom Padding', 'total' ),
		'placeholder' => '0',
	],
	'inline_css' => [
		'target' => '.logo-padding',
		'alter' => 'padding-block-end',
		'sanitize' => 'fallback_px',
	],
	'control_display' => [
		'check' => 'header_style',
		'value' => [ 'seven', 'eight', 'nine', 'ten', 'six' ],
		'compare' => 'not_equal',
	],
];

// @todo update to use css var? (like a mobile menu gutter).
$logo_settings[] = [
	'id' => 'logo_mobile_side_offset',
	'transport' => 'refresh', // this is an advanced CSS option because of RTL and custom mobile menu breakpoint.
	'sanitize_callback' => 'TotalTheme\Customizer\Sanitize_Callbacks::pixel',
	'control' => [
		'type' => 'totaltheme_length_unit',
		'units' => [ 'px' ],
		'label' => esc_html__( 'Mobile Side Offset', 'total' ),
		'description' => esc_html__( 'You can use this option to add an offset to your logo on the right side (or left for RTL layouts) if needed to prevent any content from overlapping your logo such as your mobile menu toggle when using a larger sized logo.', 'total' ),
		'placeholder' => '0',
	],
	'control_display' => [
		'check' => 'header_style',
		'value' => [ 'seven', 'eight', 'nine', 'ten' ], // already have gutters.
		'compare' => 'not_equal',
	],
];

$logo_settings[] = [
	'id' => 'logo_color',
	'transport' => 'postMessage',
	'control' => [
		'type' => 'totaltheme_color',
		'label' => esc_html__( 'Text Color', 'total' ),
	],
	'control_display' => [
		'check' => 'custom_logo',
		'value' => 'false',
	],
	'inline_css' => [
		'target' => ':root',
		'alter' => '--wpex-site-logo-color',
	],
];

$logo_settings[] = [
	'id' => 'logo_hover_color',
	'transport' => 'postMessage',
	'control' => [
		'type' => 'totaltheme_color',
		'label' => esc_html__( 'Text Color: Hover', 'total' ),
	],
	'control_display' => [
		'check' => 'custom_logo',
		'value' => 'false',
	],
	'inline_css' => [
		'target' => ':root',
		'alter' => '--wpex-hover-site-logo-color',
	],
];

$logo_settings[] = [
	'id' => 'custom_logo',
	'control' => [
		'label' => esc_html__( 'Image Logo', 'total' ),
		'type' => 'media',
		'mime_type' => 'image'
	],
];

if ( $dark_mode_enabled ) {
	$logo_settings[] = [
		'id' => 'custom_logo_dark',
		'control' => [
			'label' => esc_html__( 'Dark Mode: Image Logo', 'total' ),
			'type' => 'media',
			'mime_type' => 'image',
		],
		'control_display' => [
			'check' => 'custom_logo',
			'value' => 'not_empty',
		],
	];
}

$logo_settings[] = [
	'id' => 'logo_img_max_height',
	'transport' => 'postMessage',
	'control' => [
		'label' => esc_html__( 'Max Height Ratio', 'total' ),
		'type' => 'number',
		'description' => esc_html__( 'Applies a max-height to the logo as a percentage of the header height to prevent overflow and create vertical padding.', 'total' ),
		'input_attrs' => [
			'placeholder' => '0.6',
			'min' => '0.1',
			'step' => '0.1',
			'max' => '1',
		],
		'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::can_logo_max_ratio',
	],
	'inline_css' => [
		'target' => ':root',
		'alter' => '--wpex-site-logo-max-height-ratio',
		'sanitize' => 'decimal',
	],
];

$logo_settings[] = [
	'id' => 'logo_height',
	'control' => [
		'label' => esc_html__( 'Height (required for SVG logos)', 'total' ),
		'type' => 'totaltheme_length_unit',
		'units' => [ 'px' ],
		'description' => esc_html__( 'Used for image height attribute tag. If left empty, the theme will calculate the height automatically.', 'total' ),
	],
	'control_display' => [
		'check' => 'custom_logo',
		'value' => 'not_empty',
	],
];

$logo_settings[] = [
	'id' => 'apply_logo_height',
	'control' => [
		'label' => esc_html__( 'Apply Height', 'total' ),
		'type' => 'totaltheme_toggle',
		'description' => esc_html__( 'Enable to apply your logo height to the image. Useful for displaying large logos at a smaller size. Note: If you have enabled the shrink sticky header style you need to alter your height value under the Sticky Header settings.', 'total' ),
	],
	'control_display' => [
		'check' => 'custom_logo',
		'value' => 'not_empty',
	],
];

$logo_settings[] = [
	'id' => 'logo_width',
	'control' => [
		'label' => esc_html__( 'Width', 'total' ) . ' ' . esc_html__( '(optional)', 'total' ),
		'description' => esc_html__( 'Used for image width attribute tag.', 'total' ),
		'type' => 'totaltheme_length_unit',
		'units' => [ 'px' ],
	],
	'control_display' => [
		'check' => 'custom_logo',
		'value' => 'not_empty',
	],
];

$logo_settings[] = [
	'id' => 'retina_logo',
	'control' => [
		'label' => esc_html__( 'Retina Image Logo', 'total' ),
		'type' => 'media',
		'mime_type' => 'image',
	],
	'control_display' => [
		'check' => 'custom_logo',
		'value' => 'not_empty',
	],
];

if ( $dark_mode_enabled ) {
	$logo_settings[] = [
		'id' => 'retina_logo_dark',
		'control' => [
			'label' => esc_html__( 'Dark Mode: Retina Image Logo', 'total' ),
			'type' => 'media',
			'mime_type' => 'image',
		],
		'control_display' => [
			'check' => 'custom_logo',
			'value' => 'not_empty',
		],
	];
}

$logo_settings[] = [
	'id' => 'logo_max_width',
	'transport' => 'postMessage',
	'control' => [
		'label' => esc_html__( 'Logo Max Width: Desktop', 'total' ),
		'type' => 'totaltheme_length_unit',
		'description' => esc_html__( 'Screens 960px wide and greater.', 'total' ),
	],
	'control_display' => [
		'check' => 'custom_logo',
		'value' => 'not_empty',
	],
	'inline_css' => [
		'media_query' => '(min-width: 960px)',
		'target' => '#site-logo .logo-img',
		'alter' => 'max-width',
	],
];

$logo_settings[] = [
	'id' => 'logo_max_width_tablet_portrait',
	'transport' => 'postMessage',
	'control' => [
		'label' => esc_html__( 'Logo Max Width: Tablet Portrait', 'total' ),
		'type' => 'totaltheme_length_unit',
		'description' => esc_html__( 'Screens 768px-959px wide.', 'total' ),
	],
	'control_display' => [
		'check' => 'custom_logo',
		'value' => 'not_empty',
	],
	'inline_css' => [
		'media_query' => '(min-width: 768px) and (max-width: 959px)',
		'target' => '#site-logo .logo-img',
		'alter' => 'max-width',
	],
];

$logo_settings[] = [
	'id' => 'logo_max_width_phone',
	'transport' => 'postMessage',
	'control' => [
		'label' => esc_html__( 'Logo Max Width: Phone', 'total' ),
		'type' => 'totaltheme_length_unit',
		'description' => esc_html__( 'Screens smaller than 767px wide.', 'total' ),
	],
	'control_display' => [
		'check' => 'custom_logo',
		'value' => 'not_empty',
	],
	'inline_css' => [
		'media_query' => '(max-width: 767px)',
		'target' => '#site-logo .logo-img',
		'alter' => 'max-width',
	],
];

$this->sections['wpex_header_logo'] = [
	'title'    => esc_html__( 'Logo', 'total' ),
	'panel'    => 'wpex_header',
	'settings' => $logo_settings,
];

unset( $logo_settings );
