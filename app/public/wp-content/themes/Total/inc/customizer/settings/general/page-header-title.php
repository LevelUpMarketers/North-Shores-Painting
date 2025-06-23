<?php

defined( 'ABSPATH' ) || exit;

$this->sections['wpex_page_header'] = [
	'title' => esc_html__( 'Page Header Title', 'total' ),
	'panel' => 'wpex_general',
	'settings' => [
		[
			'id' => 'page_header_full_width',
			'default' => true,
			'control' => [
				'label' => esc_html__( 'Full Width', 'total' ),
				'type' => 'totaltheme_toggle',
				'description' => esc_html__( 'By default the page header title background and border expands the full-width of your site.', 'total' ),
			],
		],
		[
			'id' => 'page_header_enable_archive_label',
			'default' => false,
			'control' => [
				'label' => esc_html__( 'Archive Label', 'total' ),
				'type' => 'totaltheme_toggle',
				'description' => esc_html__( 'Enable to display the archive type before the archive title. Example "Category: Name".', 'total' ),
			],
		],
		[
			'id' => 'page_header_subheading_location',
			'transport' => 'refresh',
			'default' => 'page_header_content',
			'control' => [
				'label' => esc_html__( 'Subheading Location', 'total' ),
				'type' => 'select',
				'choices' => [
					'' => esc_html__( 'Default', 'total' ),
					'page_header_content' => esc_html__( 'Page Header Content', 'total' ),
					'page_header_aside' => esc_html__( 'Page Header Aside', 'total' ),
				],
			],
		],
		[
			'id' => 'page_header_style',
			//'transport' => 'postMessage', // needs refresh because of body class and active_callbacks
			'control' => [
				'label' => esc_html__( 'Style', 'total' ),
				'type' => 'select',
				'choices' => 'TotalTheme\Page\Header::style_choices',
			],
		],
		[
			'id' => 'page_header_html_tag',
			'transport' => 'postMessage',
			'default' => 'h1',
			'control' => [
				'label' => esc_html__( 'Title HTML Tag', 'total' ),
				'type' => 'select',
				'choices' => [ 'h1' => 'h1', 'span' => 'span' ],
			],
		],
		[
			'id' => 'page_header_breakpoint',
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'Responsive Breakpoint', 'total' ),
				'type' => 'select',
				'choices' => 'breakpoint',
				'description' => esc_html__( 'Option used for header styles that have content on the side such as breadcrumbs.', 'total' ),
			],
		],
		[
			'id' => 'page_header_min_height',
			'transport' => 'postMessage',
			'control_display' => [
				'check' => 'page_header_style',
				'value' => 'background-image',
			],
			'control' => [
				'type' => 'totaltheme_length_unit',
				'label' => esc_html__( 'Min-Height', 'total' ),
				'placeholder' => '400',
			],
			'inline_css' => [
				'target' => '.page-header.background-image-page-header',
				'alter' => 'min-height',
			],
		],
		[
			'id' => 'page_header_align_items',
			'transport' => 'postMessage',
			'default' => 'center',
			'control_display' => [
				'check' => 'page_header_style',
				'value' => 'background-image',
			],
			'control' => [
				'type' => 'select',
				'label' => esc_html__( 'Vertical Alignment', 'total' ),
				'choices' => [
					'' => esc_html__( 'Default', 'total' ),
					'start' => esc_html__( 'Top', 'total' ),
					'center' => esc_html__( 'Center', 'total' ),
					'end' => esc_html__( 'Bottom', 'total' ),
				],
			],
		],
		[
			'id' => 'page_header_text_align',
			'control' => [
				'type' => 'select',
				'label' => esc_html__( 'Text Align', 'total' ),
				'choices' => [
					'' => esc_html__( 'Default', 'total' ),
					'left' => esc_html__( 'Left', 'total' ),
					'center' => esc_html__( 'Center', 'total' ),
					'right' => esc_html__( 'Right', 'total' ),
				],
			],
		],
		[
			'id' => 'page_header_overlay_opacity',
			'transport' => 'postMessage',
			'control_display' => [
				'check' => 'page_header_style',
				'value' => 'background-image',
			],
			'control' => [
				'type' => 'select',
				'label' => esc_html__( 'Overlay Opacity', 'total' ),
				'choices' => 'opacity',
			],
		],
		[
			'id' => 'page_header_overlay_bg',
			'transport' => 'postMessage',
			'control_display' => [
				'check' => 'page_header_style',
				'value' => 'background-image',
			],
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Overlay Background', 'total' ),
			],
			'inline_css' => [
				'target' => '.background-image-page-header-overlay',
				'alter' => 'background-color',
			],
		],
		[
			'id' => 'page_header_hidden_main_top_padding',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_length_unit',
				'label' => esc_html__( 'Hidden Page Header Title Spacing', 'total' ),
				'description' => esc_html__( 'When the page header title is set to hidden there won\'t be any space between the header and the main content. You can enter a default spacing here.', 'total' ),
			],
			'inline_css' => [
				'target' => '.page-header-disabled:not(.has-overlay-header):not(.no-header-margin) #content-wrap',
				'alter' => 'padding-block-start',
			],
		],
		[
			'id' => 'page_header_top_padding',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_length_unit',
				'label' => esc_html__( 'Top Padding', 'total' ),
			],
			'inline_css' => [
				'target' => '.page-header.wpex-supports-mods',
				'alter' => 'padding-block-start',
			],
		],
		[
			'id' => 'page_header_bottom_padding',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_length_unit',
				'label' => esc_html__( 'Bottom Padding', 'total' ),
			],
			'inline_css' => [
				'target' => '.page-header.wpex-supports-mods',
				'alter' => 'padding-block-end',
			],
		],
		[
			'id' => 'page_header_bottom_margin',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_length_unit',
				'label' => esc_html__( 'Bottom Margin', 'total' ),
			],
			'inline_css' => [
				'target' => '.page-header',
				'alter' => 'margin-block-end',
			],
		],
		[
			'id' => 'page_header_background',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Background', 'total' ),
			],
			'inline_css' => [
				'target' => '.page-header.wpex-supports-mods',
				'alter' => 'background-color',
			],
		],
		[
			'id' => 'page_header_title_color',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Text Color', 'total' ),
			],
			'inline_css' => [
				'target' => '.page-header.wpex-supports-mods .page-header-title',
				'alter' => 'color',
			],
		],
		[
			'id' => 'page_header_top_border',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Top Border Color', 'total' ),
			],
			'inline_css' => [
				'target' => '.page-header.wpex-supports-mods',
				'alter' => 'border-top-color',
			],
		],
		[
			'id' => 'page_header_bottom_border',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Bottom Border Color', 'total' ),
			],
			'inline_css' => [
				'target' => '.page-header.wpex-supports-mods',
				'alter' => 'border-bottom-color',
			],
		],
		[
			'id' => 'page_header_top_border_width',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_length_unit',
				'units' => [ 'px' ],
				'label' => esc_html__( 'Top Border Width', 'total' ),
			],
			'inline_css' => [
				'target' => '.page-header.wpex-supports-mods',
				'alter' => 'border-top-width',
				'sanitize' => 'px',
			],
		],
		[
			'id' => 'page_header_bottom_border_width',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_length_unit',
				'units' => [ 'px' ],
				'label' => esc_html__( 'Bottom Border Width', 'total' ),
			],
			'inline_css' => [
				'target' => '.page-header.wpex-supports-mods',
				'alter' => 'border-bottom-width',
				'sanitize' => 'px',
			],
		],
		[
			'id' => 'page_header_background_img',
			'transport' => 'refresh',
			'control' => [
				'type' => 'media',
				'mime_type' => 'image',
				'label' => esc_html__( 'Background Image', 'total' ),
			],
		],
		[
			'id' => 'page_header_background_img_style',
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'Background Image Style', 'total' ),
				'type' => 'select',
				'choices' => 'bg_style',
				'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::has_page_header_background',
			],
		],
		[
			'id' => 'page_header_background_position',
			'control' => [
				'label' => esc_html__( 'Background Image Position', 'total' ),
				'type'  => 'text',
				'description' => \esc_html__( 'Enter your custom background position.', 'total' ) . ' (<a href="https://developer.mozilla.org/en-US/docs/Web/CSS/background-position" target="_blank" rel="noopener noreferrer">' . \esc_html__( 'see mozilla docs', 'total' ) . ' &#8599;</a>)',
				'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::has_page_header_background',
			],
			'inline_css' => [
				'target' => '.page-header.wpex-supports-mods',
				'alter' => 'background-position',
			],
		],
		[
			'id' => 'page_header_background_use_secondary_thumbnail',
			'default' => false,
			'control' => [
				'type' => 'totaltheme_toggle',
				'label' => esc_html__( 'Use Secondary Thumbnail as Background', 'total' ),
				'description' => esc_html__( 'When enabled the theme will use the secondary thumbnail defined in the Theme Settings metabox as the page header title background.', 'total' ),
				'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::has_page_header_background',
			],
		],
		[
			'id' => 'page_header_background_fetch_thumbnail',
			'control' => [
				'type' => 'totaltheme_multi_select',
				'label' => esc_html__( 'Use Featured Thumbnail as Background', 'total' ),
				'description' => esc_html__( 'Check the box next to any post type where you want to display the featured image as the page header title background.', 'total' ),
				'choices' => 'post_types',
				'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::has_page_header_background',
			],
		],
	],
];
