<?php

defined( 'ABSPATH' ) || exit;

$this->sections['wpex_header_general'] = [
	'title' => esc_html__( 'General', 'total' ),
	'panel' => 'wpex_header',
	'settings' => [
		[
			'id' => 'enable_header',
			'default' => true,
			'control' => [
				'label' => esc_html__( 'Site Header', 'total' ),
				'type' => 'totaltheme_toggle',
			],
		],
		[
			'id' => 'full_width_header',
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'Full Width', 'total' ),
				'type' => 'totaltheme_toggle',
			],
			'control_display' => [
				'check' => 'main_layout_style',
				'value' => 'full-width',
			],
		],
		[
			'id' => 'header_style',
			'default' => 'one',
			'control' => [
				'label' => esc_html__( 'Style', 'total' ),
				'type' => 'select',
				'choices' => 'TotalTheme\Header\Core::style_choices',
				'description' => esc_html__( 'Consider using one of the newer modern "Flex" header styles.', 'total' ),
			],
		],
		[
			'id' => 'header_five_logo_menu_position',
			'sanitize_callback' => 'TotalTheme\Customizer\Sanitize_Callbacks::absint',
			'control' => [
				'label' => esc_html__( 'Logo Position', 'total' ),
				'type' => 'number',
				'input_attrs' => [
					'placeholder' => 'e.g: 3',
					'step' => '1',
				],
				'description' => esc_html__( 'By default the theme will use PHP to locate all top level items, divide them in half and round the number to calculate the middle position for your log. You can use this field to specify the exact position for your logo within the menu.', 'total' ),
			],
			'control_display' => [
				'check' => 'header_style',
				'value' => 'five',
			],
		],
		[
			'id' => 'header_height',
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'Height', 'total' ),
				'description' => esc_html__( 'The Flex header styles must have a fixed height.', 'total' ),
				'type' => 'totaltheme_length_unit',
				'units' => [ 'px', 'vw', 'vh', 'vmin', 'vmax', 'func' ],
				'placeholder' => '100',
			],
			'inline_css' => [
				'target' => ':root',
				'alter' => '--wpex-site-header-height',
				'sanitize' => 'fallback_px',
			],
			'control_display' => [
				'check' => 'header_style',
				'value' => [ 'seven', 'eight', 'nine', 'ten' ],
			],
		],
		[
			'id' => 'header_gutter',
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'Gutter', 'total' ),
				'description' => esc_html__( 'Adds spacing between your header elements. For the centered menu header style the gutter is applied below mobile menu breakpoint only.', 'total' ),
				'type' => 'totaltheme_length_unit',
				'placeholder' => '25',
			],
			'inline_css' => [
				'target' => ':root',
				'alter' => '--wpex-site-header-gutter',
				'sanitize' => 'margin',
			],
			'control_display' => [
				'check' => 'header_style',
				'value' => [ 'seven', 'eight', 'nine', 'ten' ],
			],
		],
		[
			'id' => 'vertical_header_style',
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'Vertical Header Style', 'total' ),
				'type' => 'select',
				'choices' => [
					'' => esc_html__( 'Default', 'total' ),
					'fixed' => esc_html__( 'Fixed', 'total' ),
				],
			],
			'control_display' => [
				'check' => 'header_style',
				'value' => 'six',
			],
		],
		[
			'id' => 'vertical_header_position',
			'default' => 'left',
			'control' => [
				'label' => esc_html__( 'Vertical Header Position', 'total' ),
				'type' => 'select',
				'choices' => [
					'left' => esc_html__( 'Left', 'total' ),
					'right' => esc_html__( 'Right', 'total' ),
				],
			],
			'control_display' => [
				'check' => 'header_style',
				'value' => 'six',
			],
		],
		[
			'id' => 'vertical_header_width',
			'transport' => 'postMessage',
			'sanitize_callback' => 'TotalTheme\Customizer\Sanitize_Callbacks::pixel',
			'control' => [
				'label' => esc_html__( 'Vertical Header Width', 'total' ),
				'type' => 'totaltheme_length_unit',
				'units' => [ 'px' ],
				'placeholder' => '280',
			],
			'inline_css' => [
				'target' => ':root',
				'alter' => '--wpex-vertical-header-width',
				'sanitize' => 'px',
			],
			'control_display' => [
				'check' => 'header_style',
				'value' => 'six',
			],
		],
		[
			'id' => 'header_top_padding',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_length_unit',
				'label' => esc_html__( 'Top Padding', 'total' ),
				'placeholder' => '30',
			],
			'control_display' => [
				'check' => 'header_style',
				'value' => [ 'seven', 'eight', 'nine', 'ten' ],
				'compare' => 'not_equal',
			],
			'inline_css' => [
				'target' => '.header-padding',
				'alter' => 'padding-block-start',
				'sanitize' => 'padding',
			],
		],
		[
			'id' => 'header_bottom_padding',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_length_unit',
				'label' => esc_html__( 'Bottom Padding', 'total' ),
				'placeholder' => '30',
			],
			'control_display' => [
				'check' => 'header_style',
				'value' => [ 'seven', 'eight', 'nine', 'ten' ],
				'compare' => 'not_equal',
			],
			'inline_css' => [
				'target' => '.header-padding',
				'alter' => 'padding-block-end',
				'sanitize' => 'padding',
			],
		],
		[
			'id' => 'header_color',
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'Text Color', 'total' ),
				'type' => 'totaltheme_color',
				'description' => esc_html__( 'The color option will target elements inside the header such as those located in the aside area, the mobile menu hamburger icon or custom content added via hooks. It will not target the logo or menu.', 'total' ),
			],
			'inline_css' => [
				'target' => '#site-header',
				'alter' => '--wpex-site-header-color',
			],
		],
		[
			'id' => 'header_background',
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'Background Color', 'total' ),
				'type' => 'totaltheme_color',
			],
			'inline_css' => [
				// @note we don't target :root() because we target this variable via the body class for the vertical header.
				'target' => '#site-header',
				'alter' => '--wpex-site-header-bg-color',
			],
		],
		[
			'id' => 'header_background_image',
			'control' => [
				'type' => 'media',
				'mime_type' => 'image',
				'label' => esc_html__( 'Background Image', 'total' ),
			],
		],
		[
			'id' => 'header_background_image_style',
			'control' => [
				'label' => esc_html__( 'Background Image Style', 'total' ),
				'type'  => 'select',
				'choices' => 'bg_style',
			],
		],
		[
			'id' => 'header_background_position',
			'control' => [
				'label' => esc_html__( 'Background Image Position', 'total' ),
				'type'  => 'text',
				'description' => \esc_html__( 'Enter your custom background position.', 'total' ) . ' (<a href="https://developer.mozilla.org/en-US/docs/Web/CSS/background-position" target="_blank" rel="noopener noreferrer">' . \esc_html__( 'see mozilla docs', 'total' ) . ' &#8599;</a>)',
			],
			'inline_css' => [
				'target' => '#site-header',
				'alter' => 'background-position',
			],
		],
		/*** Flex Header Aside ***/
		[
			'id' => 'header_flex_aside_heading',
			'control' => [
				'type' => 'totaltheme_heading',
				'label' => esc_html__( 'Aside', 'total' ),
			],
			'control_display' => [
				'check' => 'header_style',
				'value' => [ 'seven', 'eight', 'nine', 'ten' ],
			],
		],
		[
			'id' => 'header_flex_aside_visibility',
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'Visibility', 'total' ),
				'type' => 'totaltheme_visibility_select'
			],
			'control_display' => [
				'check' => 'header_style',
				'value' => [ 'seven', 'eight', 'nine', 'ten' ],
			],
		],
		[
			'id' => 'header_flex_aside_mobile_menu_insert_hook',
			'control' => [
				'type' => 'select',
				'label' => esc_html__( 'Insert Side Content Into Mobile Menu', 'total' ),
				'choices' => [
					'' => esc_html__( 'Do Not Insert', 'total' ),
					'top' => esc_html__( 'Top', 'total' ),
					'bottom' => esc_html__( 'Bottom', 'total' ),
				],
			],
			'control_display' => [
				'check' => 'header_style',
				'value' => [ 'seven', 'eight', 'nine', 'ten' ],
			],
		],
		[
			'id' => 'header_flex_aside_content',
			'transport' => 'partialRefresh',
			'control' => [
				'label' => esc_html__( 'Side Content', 'total' ),
				'type' => 'wpex_textarea',
				'description' => esc_html__( 'HTML and shortcodes allowed.', 'total' ),
			],
			'control_display' => [
				'check' => 'header_style',
				'value' => [ 'seven', 'eight', 'nine', 'ten' ],
			],
		],
		[
			'id' => 'header_flex_aside_gap',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_length_unit',
				'label' => esc_html__( 'Gap', 'total' ),
				'description' => esc_html__( 'Spacing between elements inserted into the aside content area.', 'total' ),
				'placeholder' => '0',
			],
			'inline_css' => [
				'target' => '.header-aside-content--flex',
				'alter' => 'gap',
			],
			'control_display' => [
				'check' => 'header_style',
				'value' => [ 'seven', 'eight', 'nine', 'ten' ],
			],
		],
		[
			'id' => 'header_flex_aside_link_color',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Link Color', 'total' ),
			],
			'inline_css' => [
				'target' => '#site-header-flex-aside',
				'alter' => [
					'--wpex-link-color',
					'--wpex-hover-link-color',
				],
			],
			'control_display' => [
				'check' => 'header_style',
				'value' => [ 'seven', 'eight', 'nine', 'ten' ],
			],
		],
		[
			'id' => 'header_flex_aside_link_hover_color',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Link Color: Hover', 'total' ),
			],
			'inline_css' => [
				'target' => '#site-header-flex-aside',
				'alter' => [
					'--wpex-hover-link-color',
				],
			],
			'control_display' => [
				'check' => 'header_style',
				'value' => [ 'seven', 'eight', 'nine', 'ten' ],
			],
		],
		/*** Aside ***/
		[
			'id' => 'header_aside_heading',
			'control' => [
				'type' => 'totaltheme_heading',
				'label' => esc_html__( 'Aside', 'total' ),
			],
			'control_display' => [
				'check' => 'header_style',
				'value' => 'header_has_aside',
			],
		],
		[
			'id' => 'header_flex_items',
			'control' => [
				'label' => esc_html__( 'Vertical Align Aside Content', 'total' ),
				'description' => esc_html__( 'Enabling this option will group your logo and aside content inside a flex container which will also ensure the items always display side by side.', 'total' ),
				'type' => 'totaltheme_toggle',
			],
			'control_display' => [
				'check' => 'header_style',
				'value' => 'two',
			],
		],
		[
			'id' => 'header_aside_search',
			'transport' => 'partialRefresh',
			'default' => true,
			'control' => [
				'label' => esc_html__( 'Header Aside Search', 'total' ),
				'type' => 'totaltheme_toggle',
			],
			'control_display' => [
				'check' => 'header_style',
				'value' => 'two',
			],
		],
		[
			'id' => 'header_aside_visibility',
			'transport' => 'postMessage',
			'default' => 'hide-at-mm-breakpoint',
			'control' => [
				'label' => esc_html__( 'Visibility', 'total' ),
				'type' => 'totaltheme_visibility_select'
			],
			'control_display' => [
				'check' => 'header_style',
				'value' => 'header_has_aside',
			],
		],
		[
			'id' => 'header_aside_mobile_menu_insert_hook',
			'control' => [
				'type' => 'select',
				'label' => esc_html__( 'Insert Side Content Into Mobile Menu', 'total' ),
				'choices' => [
					'' => esc_html__( 'Do Not Insert', 'total' ),
					'top' => esc_html__( 'Top', 'total' ),
					'bottom' => esc_html__( 'Bottom', 'total' ),
				],
			],
			'control_display' => [
				'check' => 'header_style',
				'value' => 'header_has_aside',
			],
		],
		[
			'id' => 'header_aside',
			'transport' => 'partialRefresh',
			'control' => [
				'label' => esc_html__( 'Header Aside Content', 'total' ),
				'type' => 'textarea',
				'description' => esc_html__( 'HTML and shortcodes allowed.', 'total' ),
			],
			'control_display' => [
				'check' => 'header_style',
				'value' => 'header_has_aside',
			],
		],
	],
];
