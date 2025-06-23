<?php

defined( 'ABSPATH' ) || exit;

$footer_is_custom = $footer_is_custom ?? totaltheme_call_static( 'Footer\Core', 'is_custom' );

if ( $footer_is_custom && ! get_theme_mod( 'footer_builder_footer_widgets', false ) ) {
	return;
}

$fields = [];

/*** GENERAL SETTINGS */
if ( ! $footer_is_custom ) {
	$fields[] = [
		'id' => 'footer_widgets',
		'default' => true,
		'control' => [
			'label' => esc_html__( 'Footer Widgets', 'total' ),
			'type' => 'totaltheme_toggle',
		],
	];
}

$fields[] = [
	'id' => 'footer_dark_surface',
	'transport' => 'postMessage',
	'default' => true,
	'control' => [
		'label' => esc_html__( 'Dark Surface', 'total' ),
		'type' => 'totaltheme_toggle',
		'description' => esc_html__( 'Adds dark styling to the element, disable to use default color scheme.', 'total' ),
	],
];

if ( ! $footer_is_custom ) {
	$fields[] = [
		'id' => 'fixed_footer',
		'default' => false,
		'control' => [
			'label' => esc_html__( 'Fixed Footer', 'total' ),
			'type' => 'totaltheme_toggle',
			'description' => esc_html__( 'Adds a min-height to your #main container to keep your footer widgets at the bottom of the browser window.', 'total' ),
		],
	];
}

if ( ! $footer_is_custom ) {
	$fields[] = [
		'id' => 'footer_reveal',
		'default' => false,
		'control' => [
			'label' => esc_html__( 'Footer Reveal', 'total' ),
			'type' => 'totaltheme_toggle',
			'description' => esc_html__( 'The footer widgets will be placed in a fixed postion and display on scroll. This setting is for the "Full-Width" layout and desktops only.', 'total' ),
		],
	];
}

$fields[] = [
	'id' => 'footer_link_underline',
	'default' => false,
	'transport' => 'postMessage',
	'control' => [
		'type' => 'totaltheme_toggle',
		'label' => esc_html__( 'Underline Links', 'total' ),
		'description' => esc_html__( 'Applies to "standard" links only.', 'total' ),
	],
	'inline_css' => [
		'target' => '#footer',
		'alter' => [
			'--wpex-link-decoration-line',
			'--wpex-hover-link-decoration-line',
		],
		'value' => 'underline',
	],
];

$fields[] = [
	'id' => 'footer_padding',
	'transport' => 'postMessage',
	'control' => [
		'type' => 'totaltheme_trbl',
		'label' => esc_html__( 'Padding', 'total' ),
	],
	'inline_css' => [
		'target' => '#footer-inner',
		'alter' => 'padding',
	],
];

$fields[] = [
	'id' => 'footer_background',
	'transport' => 'postMessage',
	'control' => [
		'type' => 'totaltheme_color',
		'label' => esc_html__( 'Background Color', 'total' ),
	],
	'inline_css' => [
		'target' => '#footer',
		'alter' => [
			'--wpex-surface-1',
			'background-color',
		],
	],
];

$fields[] = [
	'id' => 'footer_bg_img',
	'control' => [
		'type' => 'media',
		'mime_type' => 'image',
		'label' => esc_html__( 'Background Image', 'total' ),
	],
];

$fields[] = [
	'id' => 'footer_bg_img_style',
	'default' => '',
	'control' => [
		'label' => esc_html__( 'Background Image Style', 'total' ),
		'type'  => 'select',
		'choices' => 'bg_style',
	],
	'control_display' => [
		'check' => 'footer_bg_img',
		'value' => 'true',
	],
];

$fields[] = [
	'id' => 'footer_bg_position',
	'transport' => 'postMessage',
	'control' => [
		'label' => esc_html__( 'Background Image Position', 'total' ),
		'type'  => 'text',
		'description' => \esc_html__( 'Enter your custom background position.', 'total' ) . ' (<a href="https://developer.mozilla.org/en-US/docs/Web/CSS/background-position" target="_blank" rel="noopener noreferrer">' . \esc_html__( 'see mozilla docs', 'total' ) . ' &#8599;</a>)',
	],
	'control_display' => [
		'check' => 'footer_bg_img',
		'value' => 'true',
	],
	'inline_css' => [
		'target' => '#footer',
		'alter' => 'background-position',
	],
];

$fields[] = [
	'id' => 'footer_color',
	'transport' => 'postMessage',
	'control' => [
		'type' => 'totaltheme_color',
		'label' => esc_html__( 'Text Color', 'total' ),
	],
	'inline_css' => [
		'target' => '#footer',
		'alter' => [
			'color',
			'--wpex-heading-color', // this is fallback as this option has always changed the heading color.
			// Target all text colors.
			'--wpex-text-2',
			'--wpex-text-3',
			'--wpex-text-4',
		],
	],
];

$fields[] = [
	'id' => 'footer_borders',
	'transport' => 'postMessage',
	'control' => [
		'type' => 'totaltheme_color',
		'label' => esc_html__( 'Borders Color', 'total' ),
	],
	'inline_css' => [
		'target' => '#footer',
		'alter' => [
			'--wpex-border-main',
			'--wpex-table-cell-border-color',
		],
	],
];

$fields[] = [
	'id' => 'footer_heading_color',
	'transport' => 'postMessage',
	'control' => [
		'type' => 'totaltheme_color',
		'label' => esc_html__( 'Headings Color', 'total' ),
		'description' => esc_html__( 'Applies to headings (h2, h3, h4, h5) inside widgets.', 'total' ),
	],
	'inline_css' => [
		'target' => '#footer',
		'alter' => '--wpex-heading-color',
	],
];

$fields[] = [
	'id' => 'footer_link_color',
	'transport' => 'postMessage',
	'control' => [
		'type' => 'totaltheme_color',
		'label' => esc_html__( 'Link Color', 'total' ),
	],
	'inline_css' => [
		'target' => '#footer',
		'alter' => [
			'--wpex-link-color',
			'--wpex-hover-link-color', // !!important!!
		],
	],
];

$fields[] = [
	'id' => 'footer_link_color_hover',
	'transport' => 'postMessage',
	'control' => [
		'type' => 'totaltheme_color',
		'label' => esc_html__( 'Link Color: Hover', 'total' ),
	],
	'inline_css' => [
		'target' => '#footer',
		'alter' => '--wpex-hover-link-color',
	],
];

/*** WIDGET TITLES */
$fields[] = [
	'id' => 'footer_widgets_titles_heading',
	'control' => [
		'type' => 'totaltheme_heading',
		'label' => esc_html__( 'Widget Titles', 'total' ),
	],
];

$fields[] = [
	'id' => 'footer_headings',
	'transport' => 'postMessage',
	'default' => 'div',
	'control' => [
		'label' => esc_html__( 'Widget Title HTML Tag', 'total' ),
		'type' => 'select',
		'choices' => [
			'h2' => 'h2',
			'h3' => 'h3',
			'h4' => 'h4',
			'h5' => 'h5',
			'h6' => 'h6',
			'span' => 'span',
			'div' => 'div',
		],
	],
];

$fields[] = [
	'id' => 'footer_headings_color',
	'transport' => 'postMessage',
	'control' => [
		'type' => 'totaltheme_color',
		'label' => esc_html__( 'Widget Title Color', 'total' ),
	],
	'inline_css' => [
		'target' => '.footer-widget .widget-title',
		'alter' => 'color',
	],
];


$fields[] = [
	'id' => 'footer_headings_background',
	'transport' => 'postMessage',
	'control' => [
		'type' => 'totaltheme_color',
		'label' => esc_html__( 'Widget Title Background', 'total' ),
	],
	'inline_css' => [
		'target' => '.footer-widget .widget-title',
		'alter' => 'background-color',
	],
];

$fields[] = [
	'id' => 'footer_headings_padding',
	'transport' => 'postMessage',
	'control' => [
		'type' => 'totaltheme_trbl',
		'label' => esc_html__( 'Widget Title Padding', 'total' ),
	],
	'inline_css' => [
		'target' => '.footer-widget .widget-title',
		'alter' => 'padding',
	],
];

$fields[] = [
	'id' => 'footer_headings_align',
	'transport' => 'postMessage',
	'control' =>  [
		'type' => 'select',
		'label' => esc_html__( 'Widget Title Text Align', 'total' ),
		'choices' => [
			'' => esc_html__( 'Default','total' ),
			'left' => esc_html__( 'Left','total' ),
			'right' => esc_html__( 'Right','total' ),
			'center' => esc_html__( 'Center','total' ),
		],
	],
	'inline_css' => [
		'target' => '.footer-widget .widget-title',
		'alter' => 'text-align',
	],
];

/*** WIDGET COLUMNS */
$fields[] = [
	'id' => 'footer_widgets_col_heading',
	'control' => [
		'type' => 'totaltheme_heading',
		'label' => esc_html__( 'Columns', 'total' ),
	],
];

$fields[] = [
	'id' => 'footer_widgets_columns',
	'default' => '4',
	'control' => [
		'label' => esc_html__( 'Columns', 'total' ),
		'type' => 'select',
		'choices' => [
			'1' => '1',
			'2' => '2',
			'3' => '3',
			'4' => '4',
			'5' => '5',
			'6' => '6',
		],
	],
];

$fields[] = [
	'id' => 'footer_widgets_gap',
	'transport' => 'postMessage',
	'control' => [
		'label' => esc_html__( 'Gap', 'total' ),
		'type' => 'select',
		'choices' => 'column_gap',
	],
];

$fields[] = [
	'id' => 'footer_widgets_bottom_margin',
	'transport' => 'postMessage',
	'control' => [
		'label' => esc_html__( 'Bottom Margin', 'total' ),
		'type' => 'totaltheme_length_unit',
		'description' => esc_html__( 'The Bottom Margin is technically applied to each widget so you have space between widgets added in the same column. If you alter this value you should probably also change your general Footer top padding so the top/bottom spacing in your footer area match.', 'total' ),
	],
	'inline_css' => [
		'target' => '.footer-widget',
		'alter' => 'padding-block-end',
		'sanitize' => 'fallback_px',
	],
];

$fields[] = [
	'id' => 'footer_widgets_col_1_width',
	'transport' => 'postMessage',
	'control' => [
		'type' => 'totaltheme_length_unit',
		'units' => [ 'px', '%', 'func' ],
		'label' => esc_html__( 'Column 1 Width', 'total' ),
	],
	'inline_css' => [
		'target' => '.footer-box.col-1',
		'alter' => 'width',
		'sanitize' => 'fallback_px',
	],
];

$fields[] = [
	'id' => 'footer_widgets_col_2_width',
	'transport' => 'postMessage',
	'control' => [
		'type' => 'totaltheme_length_unit',
		'units' => [ 'px', '%', 'func' ],
		'label' => esc_html__( 'Column 2 Width', 'total' ),
	],
	'inline_css' => [
		'target' => '.footer-box.col-2',
		'alter' => 'width',
		'sanitize' => 'fallback_px',
	],
];

$fields[] = [
	'id' => 'footer_widgets_col_3_width',
	'transport' => 'postMessage',
	'control' => [
		'type' => 'totaltheme_length_unit',
		'units' => [ 'px', '%', 'func' ],
		'label' => esc_html__( 'Column 3 Width', 'total' ),
	],
	'inline_css' => [
		'target' => '.footer-box.col-3',
		'alter' => 'width',
		'sanitize' => 'fallback_px',
	],
];

$fields[] = [
	'id' => 'footer_widgets_col_4_width',
	'transport' => 'postMessage',
	'control' => [
		'type' => 'totaltheme_length_unit',
		'units' => [ 'px', '%', 'func' ],
		'label' => esc_html__( 'Column 4 Width', 'total' ),
	],
	'inline_css' => [
		'target' => '.footer-box.col-4',
		'alter' => 'width',
		'sanitize' => 'fallback_px',
	],
];

$fields[] = [
	'id' => 'footer_widgets_col_5_width',
	'transport' => 'postMessage',
	'control' => [
		'type' => 'totaltheme_length_unit',
		'units' => [ 'px', '%', 'func' ],
		'label' => esc_html__( 'Column 5 Width', 'total' ),
	],
	'inline_css' => [
		'target' => '.footer-box.col-5',
		'alter' => 'width',
		'sanitize' => 'fallback_px',
	],
];

$fields[] = [
	'id' => 'footer_widgets_col_6_width',
	'transport' => 'postMessage',
	'control' => [
		'type' => 'totaltheme_length_unit',
		'units' => [ 'px', '%', 'func' ],
		'label' => esc_html__( 'Column 6 Width', 'total' ),
	],
	'inline_css' => [
		'target' => '.footer-box.col-6',
		'alter' => 'width',
		'sanitize' => 'fallback_px',
	],
];

$this->sections['wpex_footer_widgets'] = [
	'title'    => esc_html__( 'General', 'total' ),
	'settings' => $fields,
];

unset( $fields );
