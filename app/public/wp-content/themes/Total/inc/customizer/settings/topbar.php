<?php

defined( 'ABSPATH' ) || exit;

$breakpoints = wpex_utl_breakpoints();

if ( $breakpoints && is_array( $breakpoints ) ) {
	$breakpoints['none'] = esc_html__( 'None (no stacking)', 'total' );
}

// General.
$this->sections['wpex_topbar_general'] = [
	'title' => esc_html__( 'General', 'total' ),
	'panel' => 'wpex_topbar',
	'settings' => [
		[
			'id' => 'top_bar',
			'default' => true,
			'control' => [
				'label' => esc_html__( 'Top Bar', 'total' ),
				'type' => 'totaltheme_toggle',
			],
		],
		[
			'id' => 'top_bar_fullwidth',
			'default' => false,
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'Full Width', 'total' ),
				'type' => 'totaltheme_toggle',
			],
			'control_display' => [
				'check' => 'main_layout_style',
				'value' => 'boxed',
				'compare' => 'not_equal',
			],
		],
		[
			'id' => 'top_bar_sticky',
			'default' => false,
			'control' => [
				'label' => esc_html__( 'Sticky on Scroll', 'total' ),
				'type' => 'totaltheme_toggle',
			],
		],
		[
			'id' => 'top_bar_sticky_mobile',
			'default' => true,
			'control' => [
				'label' => esc_html__( 'Sticky on Mobile', 'total' ),
				'type' => 'totaltheme_toggle',
			],
			'control_display' => [
				'check' => 'top_bar_sticky',
				'value' => 'true',
			],
		],
		[
			'id' => 'top_bar_link_underline',
			'default' => false,
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_toggle',
				'label' => esc_html__( 'Underline Links', 'total' ),
			],
			'inline_css' => [
				'target' => '#top-bar, .top-bar-item',
				'alter' => [
					'--wpex-link-decoration-line',
					'--wpex-hover-link-decoration-line',
				],
				'value' => 'underline',
			],
		],
		[
			'id' => 'top_bar_bottom_border',
			'default' => true,
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'Bottom Border', 'total' ),
				'type' => 'totaltheme_toggle',
			],
		],
		[
			'id' => 'top_bar_visibility',
			'control' => [
				'label' => esc_html__( 'Visibility', 'total' ),
				'type' => 'totaltheme_visibility_select',
			],
		],
		[
			'id' => 'top_bar_style',
			'default' => 'one',
			'transport' => 'partialRefresh',
			'control' => [
				'label' => esc_html__( 'Layout', 'total' ),
				'type' => 'select',
				'choices' => [
					'one' => esc_html__( 'Default', 'total' ),
					'two' => esc_html__( 'Reverse', 'total' ),
					'three' => esc_html__( 'Centered & Stacked', 'total' ),
				],
			],
		],
		[
			'id' => 'topbar_split_breakpoint',
			'transport' => 'refresh',
			'control' => [
				'label' => esc_html__( 'Responsive Breakpoint', 'total' ),
				'type' => 'select',
				'choices' => $breakpoints,
				'description' => esc_html__( 'Select the breakpoint at which point the top bar is split into a left/right layout.' ),
			],
		],
		[
			'id' => 'topbar_alignment',
			'transport' => 'refresh',
			'control' => [
				'label' => esc_html__( 'Stacked Alignment', 'total' ),
				'type' => 'select',
				'choices' => [
					''       => esc_html__( 'Default', 'total' ),
					'left'   => esc_html__( 'Start', 'total' ),
					'center' => esc_html__( 'Center', 'total' ),
					'right'  => esc_html__( 'End', 'total' ),
				],
			],
			'control_display' => [
				'check' => 'topbar_split_breakpoint',
				'value' => 'none',
				'compare' => 'not_equal',
			],
		],
		[
			'id' => 'top_bar_bg',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Background', 'total' ),
			],
			'inline_css' => [
				'target' => [
					'#top-bar-wrap',
					'.wpex-top-bar-sticky',
				],
				'alter' => 'background-color',
			],
		],
		[
			'id' => 'top_bar_border',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Border Color', 'total' ),
			],
			'inline_css' => [
				'target' => '#top-bar-wrap',
				'alter' => 'border-color',
			],
		],
		[
			'id' => 'top_bar_text',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Text Color', 'total' ),
			],
			'inline_css' => [
				'target' => '#top-bar',
				'alter' => [
					'color',
					// Target all text vars.
					'--wpex-text-2',
					'--wpex-text-3',
					'--wpex-text-4',
				],
			],
		],
		// link colors
		[
			'id' => 'top_bar_link_color',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Link Color', 'total' ),
			],
			'inline_css' => [
				'target' => '#top-bar',
				'alter' => '--wpex-link-color',
			],
		],
		[
			'id' => 'top_bar_link_color_hover',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Link Color: Hover', 'total' ),
			],
			'inline_css' => [
				'target' => '#top-bar',
				'alter' => '--wpex-hover-link-color',
			],
		],
		// Padding
		[
			'id' => 'top_bar_top_padding',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_length_unit',
				'label' => esc_html__( 'Top Padding', 'total' ),
				'placeholder' => '15',
			],
			'inline_css' => [
				'target' => '#top-bar',
				'alter' => 'padding-block-start',
				'sanitize' => 'padding',
			],
		],
		[
			'id' => 'top_bar_bottom_padding',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_length_unit',
				'label' => esc_html__( 'Bottom Padding', 'total' ),
				'placeholder' => '15',
			],
			'inline_css' => [
				'target' => '#top-bar',
				'alter' => 'padding-block-end',
				'sanitize' => 'padding',
			],
		],
	],
];

/*-----------------------------------------------------------------------------------*/
/* - TopBar => Content
/*-----------------------------------------------------------------------------------*/
$this->sections['wpex_topbar_content'] = [
	'title' => esc_html__( 'Content', 'total' ),
	'panel' => 'wpex_topbar',
	'settings' => [
		[
			'id' => 'top_bar_content',
			'transport' => 'partialRefresh',
			'default' => TotalTheme\Topbar\Core::get_default_content(),
			'control' => [
				'label' => esc_html__( 'Content', 'total' ),
				'type' => 'wpex_textarea',
				'rows' => 20,
				'description' => esc_html__( 'HTML and shortcodes allowed.', 'total' ),
			],
		],
		[
			'id' => 'topbar_item_breakpoint',
			'transport' => 'refresh',
			'control' => [
				'label' => esc_html__( 'Top Bar Item Responsive Breakpoint', 'total' ),
				'type' => 'select',
				'choices' => $breakpoints,
				'description' => esc_html__( 'By default the [topbar_item] shortcode elements stack at 640px but you can change their default stacking breakpoint.' ),
			],
			'control_display' => [
				'check' => 'topbar_split_breakpoint',
				'value' => 'none',
				'compare' => 'not_equal',
			],
		],
	],
];

/*-----------------------------------------------------------------------------------*/
/* - TopBar => Social
/*-----------------------------------------------------------------------------------*/
$this->sections['wpex_topbar_social'] = [
	'title' => esc_html__( 'Social', 'total' ),
	'panel' => 'wpex_topbar',
	'settings' => [
		[
			'id' => 'top_bar_social_alt',
			'transport' => 'partialRefresh',
			'control' => [
				'label' => esc_html__( 'Social Alternative', 'total' ),
				'type' => 'textarea',
				'description' => esc_html__( 'HTML and shortcodes allowed.', 'total' ),
			],
		],
		[
			'id' => 'top_bar_social',
			'default' => true,
			'transport' => 'refresh', // Other items relly on this conditionally to show/hide
			'control' => [
				'label' => esc_html__( 'Display Social', 'total' ),
				'type' => 'totaltheme_toggle',
				'description' => esc_html__( 'Display social links if the "Social Alternative" field above is empty.', 'total' ),
			],
		],
		[
			'id' => 'top_bar_social_target',
			'default' => 'blank',
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'Social Link Target', 'total' ),
				'type' => 'select',
				'choices' => [
					'blank' => esc_html__( 'New Window', 'total' ),
					'self' => esc_html__( 'Same Window', 'total' ),
				],
			],
		],
		[
			'id' => 'top_bar_social_style',
			'default' => 'default',
			'transport' => 'partialRefresh',
			'control' => [
				'label' => esc_html__( 'Social Style', 'total' ),
				'type' => 'select',
				'choices' => 'social_styles',
			],
		],
		[
			'id' => 'top_bar_social_gap',
			'transport' => 'partialRefresh',
			'control' => [
				'label' => esc_html__( 'Spacing Between Social Links', 'total' ),
				'type'  => 'select',
				'choices' => 'margin',
			],
		],
		[
			'id' => 'top_bar_social_dims',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_length_unit',
				'label' => esc_html__( 'Social Links Dimensions', 'total' ),
				'description' => esc_html__( 'Used to give your social links a custom height and width.', 'total' ),
			],
			'inline_css' => [
				'target' => '#top-bar-social a.wpex-social-btn',
				'alter' => [ 'width', 'height' ],
			],
		],
		[
			'id' => 'top_bar_social_font_size',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_length_unit',
				'label' => esc_html__( 'Social Links Font Size', 'total' ),
			],
			'inline_css' => [
				'target' => '#top-bar-social a.wpex-social-btn',
				'alter' => 'font-size',
				'sanitize' => 'font-size',
			],
		],
		[
			'id' => 'top_bar_social_color',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Social Links Color', 'total' ),
			],
			'control_display' => [
				'check' => 'top_bar_social_style',
				'value' => [ 'default', 'none' ],
			],
			'inline_css' => [
				'target' => '#top-bar-social a.wpex-social-btn-no-style',
				'alter' => 'color',
			],
		],
		[
			'id' => 'top_bar_social_hover_color',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Social Links Hover Color', 'total' ),
			],
			'control_display' => [
				'check' => 'top_bar_social_style',
				'value' => [ 'default', 'none' ],
			],
			'inline_css' => [
				'target' => '#top-bar-social a.wpex-social-btn-no-style:hover',
				'alter' => 'color',
			],
		],
	],
];

// Social settings.
if ( totaltheme_call_static( 'Topbar\Social', 'get_profile_options' ) && $this->is_customize_preview() ) {
	$this->sections['wpex_topbar_social']['settings'][] = [
		'id' => 'wpex_topbar_social_heading',
		'control' => [
			'type' => 'totaltheme_heading',
			'label' => esc_html__( 'Social Profiles', 'total' ),
		],
	];
	$this->sections['wpex_topbar_social']['settings'][] = [
		'id' => 'top_bar_social_profiles',
		'transport' => 'postMessage',
		'control' => [
			'type' => 'totaltheme_social_profiles',
		],
	];
}
