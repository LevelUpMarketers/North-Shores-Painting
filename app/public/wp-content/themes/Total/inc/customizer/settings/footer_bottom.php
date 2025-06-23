<?php

defined( 'ABSPATH' ) || exit;

$footer_is_custom = $footer_is_custom ?? totaltheme_call_static( 'Footer\Core', 'is_custom' );

if ( $footer_is_custom && ! get_theme_mod( 'footer_builder_footer_bottom', false ) ) {
	return;
}

$fields = [];

if ( ! $footer_is_custom ) {
	$fields[] = [
		'id' => 'footer_bottom',
		'default' => true,
		'control' => [
			'label' => esc_html__( 'Footer Bottom', 'total' ),
			'type' => 'totaltheme_toggle',
		],
	];
}

$fields[] = [
	'id' => 'footer_bottom_dark_surface',
	'transport' => 'postMessage',
	'default' => true,
	'control' => [
		'label' => esc_html__( 'Dark Surface', 'total' ),
		'type' => 'totaltheme_toggle',
		'description' => esc_html__( 'Adds dark styling to the element, disable to use default color scheme.', 'total' ),
	],
];

$fields[] = [
	'id' => 'bottom_footer_link_underline',
	'default' => false,
	'transport' => 'postMessage',
	'control' => [
		'type' => 'totaltheme_toggle',
		'label' => esc_html__( 'Underline Links', 'total' ),
		'description' => esc_html__( 'Applies to both the copyright and the menu.', 'total' ),
	],
	'inline_css' => [
		'target' => '#footer-bottom',
		'alter' => [
			'--wpex-link-decoration-line',
			'--wpex-hover-link-decoration-line',
		],
		'value' => 'underline',
	],
];

$fields[] = [
	'id' => 'footer_copyright_text',
	'transport' => 'partialRefresh',
	'default' => TotalTheme\Footer\Bottom\Copyright::get_default_content(),
	'control' => [
		'label' => esc_html__( 'Copyright', 'total' ),
		'type' => 'wpex_textarea',
	],
];

$fields[] = [
	'id' => 'bottom_footer_text_align',
	'transport' => 'partialRefresh',
	'control' =>  [
		'type' => 'select',
		'label' => esc_html__( 'Layout', 'total' ),
		'choices' => [
			'' => esc_html__( 'Side by Side','total' ),
			'left' => esc_html__( 'Stacked - Start Aligned','total' ),
			'center' => esc_html__( 'Stacked - Centered','total' ),
			'right' => esc_html__( 'Stacked - End Aligned','total' ),
		],
	],
];

$fields[] = [
	'id' => 'bottom_footer_breakpoint',
	'transport' => 'refresh',
	'control' => [
		'label' => esc_html__( 'Stack Breakpoint', 'total' ),
		'type' => 'select',
		'choices' => 'breakpoint',
	],
	'control_display' => [
		'check' => 'bottom_footer_text_align',
		'value' => 'false',
	],
];

$fields[] = [
	'id' => 'bottom_footer_stack_align',
	'transport' => 'refresh',
	'default' => 'center',
	'control' => [
		'label' => esc_html__( 'Stacked Alignment', 'total' ),
		'type' => 'select',
		'choices' => [
			'left'   => esc_html__( 'Start', 'total' ),
			'center' => esc_html__( 'Center', 'total' ),
			'right'  => esc_html__( 'End', 'total' ),
		],
	],
	'control_display' => [
		'check' => 'bottom_footer_text_align',
		'value' => 'false',
	],
];

$fields[] = [
	'id' => 'bottom_footer_stack_flip',
	'transport' => 'refresh',
	'control' => [
		'label' => esc_html__( 'Stacked Flip', 'total' ),
		'description' => esc_html__( 'Flips the vertical order of the copyright and menu when stacked.', 'total' ),
		'type' => 'totaltheme_toggle',
	],
	'control_display' => [
		'check' => 'bottom_footer_text_align',
		'value' => 'false',
	],
];

$fields[] = [
	'id' => 'bottom_footer_padding',
	'transport' => 'postMessage',
	'control' =>  [
		'type' => 'totaltheme_trbl',
		'label' => esc_html__( 'Padding', 'total' ),
	],
	'inline_css' => [
		'target' => '#footer-bottom',
		'alter' => 'padding',
	],
];

$fields[] = [
	'id' => 'bottom_footer_background',
	'transport' => 'postMessage',
	'control' =>  [
		'type' => 'totaltheme_color',
		'label' => esc_html__( 'Background', 'total' ),
	],
	'inline_css' => [
		'target' => '#footer-bottom',
		'alter' => 'background-color',
	],
];
$fields[] = [
	'id' => 'bottom_footer_color',
	'transport' => 'postMessage',
	'control' =>  [
		'type' => 'totaltheme_color',
		'label' => esc_html__( 'Text Color', 'total' ),
	],
	'inline_css' => [
		'target' => '#footer-bottom',
		'alter' => [
			'color',
			// Target all surface colors.
			'--wpex-text-2',
			'--wpex-text-3',
			'--wpex-text-4',
		],
	],
];

$fields[] = [
	'id' => 'bottom_footer_link_color',
	'transport' => 'postMessage',
	'control' =>  [
		'type' => 'totaltheme_color',
		'label' => esc_html__( 'Link Color', 'total' ),
	],
	'inline_css' => [
		'target' => '#footer-bottom',
		'alter' => [
			'--wpex-link-color',
			'--wpex-hover-link-color',
		],
	],
];

$fields[] = [
	'id' => 'bottom_footer_link_color_hover',
	'transport' => 'postMessage',
	'control' =>  [
		'type' => 'totaltheme_color',
		'label' => esc_html__( 'Link Color: Hover', 'total' ),
	],
	'inline_css' => [
		'target' => '#footer-bottom',
		'alter' => '--wpex-hover-link-color',
	],
];

// General
$this->sections['wpex_footer_bottom'] = [
	'title'    => esc_html__( 'General', 'total' ),
	'settings' => $fields,
];

unset( $fields );
