<?php

defined( 'ABSPATH' ) || exit;

$legacy_typo = $legacy_typo ?? totaltheme_has_classic_styles();

// General
$this->sections['wpex_layout_general'] = [
	'title' => esc_html__( 'General', 'total' ),
	'panel' => 'wpex_layout',
	'settings' => [
		[
			'id' => 'container_max_width',
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'Max Width', 'total' ),
				'type' => 'totaltheme_length_unit',
				'units' => [ '%', 'px', 'vw', 'var', 'func' ],
				'placeholder' => '90',
				'default_unit' => '%',
				'description' => esc_html__( 'Used to prevent the site content from touching the edge of browser screen. It is recommended to use relative unit such as % or vw. Using a px unit will break the fluid layout unless your container width is using a relative unit.', 'total' ),
			],
			'inline_css' => [
				'target' => ':root,.site-boxed.wpex-responsive #wrap',
				'alter' => '--wpex-container-max-width',
			],
			'control_display' => [
				'check' => 'responsive',
				'value' => 'true',
			],
		],
		[
			'id' => 'content_layout',
			'default' => '',
			'control' => [
				'label' => esc_html__( 'Content Layout', 'total' ),
				'type' => 'select',
				'choices' => 'post_layout',
				'description' => esc_html__( 'Select your default content layout for your site. You can always browse to different tabs in the Customizer such as the blog tab to alter your layout specifically for your blog archives and posts.', 'total' ),
			],
		],
		[
			'id' => 'main_layout_style',
			'default' => 'full-width',
			'control' => [
				'label' => esc_html__( 'Site Layout Style', 'total' ),
				'type' => 'select',
				'choices' => [
					'full-width' => esc_html__( 'Full Width', 'total' ),
					'boxed' => esc_html__( 'Boxed', 'total' ),
				],
			],
		],
		[
			'id' => 'boxed_dropdshadow',
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'Container Drop Shadow', 'total' ),
				'type' => 'totaltheme_toggle', // @todo change to choices so we can choose from preset shadows.
			],
			'control_display' => [
				'check' => 'main_layout_style',
				'value' => 'boxed',
			],
		],
		[
			'id' => 'boxed_padding',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'text',
				'label' => esc_html__( 'Outer Margin', 'total' ),
				'input_attrs' => [
					'placeholder' => '40px 30px',
				],
			],
			'control_display' => [
				'check' => 'main_layout_style',
				'value' => 'boxed',
			],
			'inline_css' => [
				'target' => ':root',
				'alter' => '--wpex-boxed-layout-padding',
			],
		],
		[
			'id' => 'boxed_gutter',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_length_unit',
				'label' => esc_html__( 'Inner Gutter', 'total' ),
				'placeholder' => '30',
			],
			'control_display' => [
				'check' => 'main_layout_style',
				'value' => 'boxed',
			],
			'inline_css' => [
				'media_query' => '(min-width: 768px)',
				'target' => ':root',
				'alter' => '--wpex-boxed-layout-gutter',
				'sanitize' => 'fallback_px',
			],
		],
		[
			'id' => 'boxed_wrap_bg',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Inner Background', 'total' ),
			],
			'control_display' => [
				'check' => 'main_layout_style',
				'value' => 'boxed',
			],
			'inline_css' => [
				'target' => '.site-boxed #wrap',
				'alter' => [ 'background-color', '--wpex-site-header-bg-color' ]
			],
		],
		[
			'id' => 'site_frame_border',
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'Site Frame Border', 'total' ),
				'type' => 'totaltheme_toggle',
			],
		],
		[
			'id' => 'site_frame_border_color',
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'Site Frame Border Color', 'total' ),
				'type' => 'totaltheme_color',
			],
			'inline_css' => [
				'target' => '#wpex-sfb-l,#wpex-sfb-r,#wpex-sfb-t,#wpex-sfb-b',
				'alter' => 'background-color',
			],
			'control_display' => [
				'check' => 'site_frame_border',
				'value' => 'true',
			],
		],
		[
			'id' => 'site_frame_border_size',
			'transport' => 'postMessage',
			'sanitize_callback' => 'TotalTheme\Customizer\Sanitize_Callbacks::pixel',
			'control' => [
				'label' => esc_html__( 'Site Frame Border Size', 'total' ),
				'type' => 'totaltheme_length_unit',
				'placeholder' => '15',
				'units' => [ 'px' ],
			],
			'inline_css' => [
				'target' => '.has-frame-border',
				'alter' => '--wpex-site-frame-border-size',
				'sanitize' => 'px',
			],
			'control_display' => [
				'check' => 'site_frame_border',
				'value' => 'true',
			],
		],
		[
			'id' => 'has_primary_bottom_spacing',
			'default' => true,
			'control' => [
				'label' => esc_html__( 'Primary Bottom Spacing', 'total' ),
				'type' => 'totaltheme_toggle',
				'description' => esc_html__( 'Enables a bottom padding on the theme\'s #primary container so there is space between your content and footer. This spacing is removed by default for dynamic templates.', 'total' ),
			],
		],
		[
			'id' => 'primary_bottom_spacing',
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'Primary Bottom Spacing', 'total' ),
				'type' => 'totaltheme_length_unit',
				'placeholder' => '40',
			],
			'inline_css' => [
				'target' => ':root',
				'alter'  => '--wpex-primary-bottom-space',
			],
			'control_display' => [
				'check' => 'has_primary_bottom_spacing',
				'value' => 'true',
			],
		],
		[
			'id' => 'primary_bottom_spacing_condition',
			'sanitize_callback' => 'sanitize_text_field', // stops issues with WP storing '&amp;' instead of &.
			'control' => [
				'type' => 'textarea',
				'label' => esc_html__( 'Primary Bottom Spacing Conditions', 'total' ),
				'description' => sprintf( esc_html__( 'This field allows you to use %sconditional tags%s to limit the functionality to specific areas of the site via a query string. For example to limit by posts and pages you can use "is_page&is_single" or "is_singular=post,page". Separate conditions with an ampersand and use a comma seperated string for arrays.', 'total' ), '<a href="https://codex.wordpress.org/Conditional_Tags" target="_blank" rel="noopener noreferrer">', '</a>' ) . ' ' . sprintf( esc_html__( 'You can create and use your own conditional functions but they must be %swhitelisted%s', 'total' ), '<a href="https://totalwptheme.com/docs/snippets/conditional-logic-whitelist/" target="_blank" rel="noopener noreferrer">', '</a>' ),
			],
			'control_display' => [
				'check' => 'has_primary_bottom_spacing',
				'value' => 'true',
			],
		],
	],
];

// Desktop Widths
$this->sections['wpex_layout_desktop_widths'] = [
	'title' => esc_html__( 'Desktop Widths', 'total' ),
	'panel' => 'wpex_layout',
	'description' => esc_html__( 'For screens greater than or equal to 960px.', 'total' ),
	'settings' => [
		[
			'id' => 'main_container_width',
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'Main Container Width', 'total' ),
				'type' => 'totaltheme_length_unit',
				'allow_numeric' => false,
				'placeholder' => $legacy_typo ? '980' : '1280',
			],
			'inline_css' => [
				'target' => ':root,.site-boxed.wpex-responsive #wrap',
				'alter' => '--wpex-container-width',
				'sanitize' => 'container_width',
			],
		],
		[
			'id' => 'left_container_width',
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'Content Area Width', 'total' ),
				'type' => 'totaltheme_length_unit',
				'allow_numeric' => false,
				'default_unit' => '%',
				'placeholder' => $legacy_typo ? '69' : '65%',
			],
			'inline_css' => [
				'target' => ':root',
				'alter' => '--wpex-content-area-width',
				'media_query' => '(min-width: 960px)',
				'sanitize' => 'container_width',
			],
		],
		[
			'id' => 'sidebar_width',
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'Sidebar Width', 'total' ),
				'type' => 'totaltheme_length_unit',
				'allow_numeric' => false,
				'default_unit' => '%',
				'placeholder' => $legacy_typo ? '26' : '35%',
			],
			'inline_css' => [
				'target' => ':root',
				'alter' => '--wpex-primary-sidebar-width',
				'media_query' => '(min-width: 960px)',
				'sanitize' => 'container_width',
			],
		],
	],
];

// Medium Screen Widths
$this->sections['wpex_layout_medium_widths'] = [
	'title' => esc_html__( 'Medium Screens Widths', 'total' ),
	'panel' => 'wpex_layout',
	'description' => esc_html__( 'For screens between 960px - 1280px. Such as landscape tablets and small monitors/laptops.', 'total' ),
	'settings' => [
		[
			'id' => 'tablet_landscape_main_container_width',
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'Main Container Width', 'total' ),
				'type' => 'totaltheme_length_unit',
				'allow_numeric' => false,
				'placeholder' => esc_html__( 'inherit from desktop widths', 'total' ),
			],
			'inline_css' => [
				'target' => ':root,.site-boxed.wpex-responsive #wrap',
				'alter' => '--wpex-container-width',
				'media_query' => '(min-width: 960px) and (max-width: 1280px)',
				'sanitize' => 'container_width',
			],
		],
		[
			'id' => 'tablet_landscape_left_container_width',
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'Content Area Width', 'total' ),
				'type' => 'totaltheme_length_unit',
				'allow_numeric' => false,
				'default_unit' => '%',
				'placeholder' => esc_html__( 'inherit from desktop widths', 'total' ),
			],
			'inline_css' => [
				'target' => ':root',
				'alter' => '--wpex-content-area-width',
				'media_query' => '(min-width: 960px) and (max-width: 1280px)',
				'sanitize' => 'container_width',
			],
		],
		[
			'id' => 'tablet_landscape_sidebar_width',
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'Sidebar Width', 'total' ),
				'type' => 'totaltheme_length_unit',
				'allow_numeric' => false,
				'default_unit' => '%',
				'placeholder' => esc_html__( 'inherit from desktop widths', 'total' ),
			],
			'inline_css' => [
				'target' => ':root',
				'alter' => '--wpex-primary-sidebar-width',
				'media_query' => '(min-width: 960px) and (max-width: 1280px)',
				'sanitize' => 'container_width',
			],
		],
	],
];

// Tablet Portrait Widths
$this->sections['wpex_layout_tablet_widths'] = [
	'title' => esc_html__( 'Tablet Widths', 'total' ),
	'panel' => 'wpex_layout',
	'description' => esc_html__( 'For screens between 768px - 959px. Such as portrait tablet.', 'total' ),
	'settings' => [
		[
			'id' => 'tablet_main_container_width',
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'Main Container Width', 'total' ),
				'type' => 'totaltheme_length_unit',
				'allow_numeric' => false,
				'placeholder' => esc_html__( 'inherit from desktop widths', 'total' ),
			],
			'inline_css' => [
				'target' => ':root,.site-boxed.wpex-responsive #wrap',
				'alter' => '--wpex-container-width',
				'media_query' => '(min-width: 768px) and (max-width: 959px)',
				'sanitize' => 'container_width',
			],
		],
		[
			'id' => 'tablet_left_container_width',
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'Content Area Width', 'total' ),
				'type' => 'totaltheme_length_unit',
				'allow_numeric' => false,
				'default_unit' => '%',
				'placeholder' => '100',
			],
			'inline_css' => [
				'target' => ':root',
				'alter' => '--wpex-content-area-width',
				'media_query' => '(min-width: 768px) and (max-width: 959px)',
				'sanitize' => 'container_width',
			],
		],
		[
			'id' => 'tablet_sidebar_width',
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'Sidebar Width', 'total' ),
				'type' => 'totaltheme_length_unit',
				'allow_numeric' => false,
				'default_unit' => '%',
				'placeholder' => '100',
			],
			'inline_css' => [
				'target' => ':root',
				'alter' => '--wpex-primary-sidebar-width',
				'media_query' => '(min-width: 768px) and (max-width: 959px)',
				'sanitize' => 'container_width',
			],
		],
	],
];

// Mobile Phone Widths
$this->sections['wpex_layout_phone_widths'] = [
	'title' => esc_html__( 'Mobile Phone Widths', 'total' ),
	'panel' => 'wpex_layout',
	'settings' => [
		[
			'id' => 'mobile_landscape_main_container_width',
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'Landscape: Main Container Width', 'total' ),
				'type' => 'totaltheme_length_unit',
				'allow_numeric' => false,
				'placeholder' => esc_html__( 'inherit from max-width', 'total' ),
				'description' => '(min-width: 480px) and (max-width: 767px)',
			],
			'inline_css' => [
				'target' => ':root,.site-boxed.wpex-responsive #wrap',
				'alter' => '--wpex-container-width',
				'media_query' => '(min-width: 480px) and (max-width: 767px)',
				'sanitize' => 'container_width',
			],
		],
		[
			'id' => 'mobile_portrait_main_container_width',
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'Portrait: Main Container Width', 'total' ),
				'type' => 'totaltheme_length_unit',
				'allow_numeric' => false,
				'placeholder' => esc_html__( 'inherit from max-width', 'total' ),
				'description' => '(max-width: 767px)',
			],
			'inline_css' => [
				'target' => ':root,.site-boxed.wpex-responsive #wrap',
				'alter' => '--wpex-container-width',
				'media_query' => '(max-width: 767px)',
				'sanitize' => 'container_width',
			],
		],
	],
];
