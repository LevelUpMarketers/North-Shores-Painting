<?php

defined( 'ABSPATH' ) || exit;

$this->sections['wpex_menu_search'] = [
	'title' => esc_html__( 'Search', 'total' ),
	'panel' => 'wpex_header',
	'settings' => [
		[
			'id' => 'menu_search_enable',
			'default' => true,
			'control' => [
				'label' => esc_html__( 'Menu Icon', 'total' ),
				'type' => 'totaltheme_toggle',
			],
		],
		[
			'id' => 'menu_search_style',
			'default' => 'drop_down',
			'control' => [
				'label' => esc_html__( 'Style', 'total' ),
				'type' => 'select',
				'choices' => [
					'drop_down' => esc_html__( 'Drop Down','total' ),
					'modal' => esc_html__( 'Modal (Live Search)','total' ),
					'overlay' => esc_html__( 'Site Overlay','total' ),
					'header_replace' => esc_html__( 'Header Replace','total' ),
					'custom_link' => esc_html__( 'Custom Link','total' ),
				],
				'description' => esc_html__( 'The vertical header may not support all styles.', 'total' ),
			],
		],
		[
			'id' => 'menu_search_custom_link',
			'sanitize_callback' => 'sanitize_url',
			'control' => [
				'label' => esc_html__( 'Custom Link', 'total' ),
				'type' => 'text',
				'description' => esc_html__( 'You can use the format /{page_slug}/ to link to a page on the current site.', 'total' ),
			],
			'control_display' => [
				'check' => 'menu_search_style',
				'value' => 'custom_link',
			],
		],
		[
			'id' => 'menu_search_slide_down',
			'default' => false,
			'control' => [
				'label' => esc_html__( 'Slide Down?', 'total' ),
				'type' => 'totaltheme_toggle',
			],
			'control_display' => [
				'check' => 'menu_search_style',
				'value' => 'header_replace',
			],
		],
		[
			'id' => 'menu_search_icon',
			'default' => 'search',
			'control' => [
				'label' => esc_html__( 'Icon', 'total' ),
				'type' => 'totaltheme_icon',
				'choices' => 'TotalTheme\Header\Menu\Search::icon_choices',
				'fallback' => 'search',
			],
		],
		[
			'id' => 'menu_search_icon_size',
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'Icon Size', 'total' ),
				'type' => 'totaltheme_length_unit',
			],
			'inline_css' => [
				'target' => '.wpex-menu-search-icon,.wpex-header-search-icon__icon',
				'alter' => 'font-size',
			],
		],
		[
			'id' => 'modal_search_notice',
			'control' => [
				'description' => sprintf( esc_html__( 'To customize the modal search %sclick here%s.', 'total' ), '<span class="totaltheme-customize-focus-link" data-wpex-section="wpex_search_modal">', '</span>' ),
				'type' => 'totaltheme_notice',
			],
			'control_display' => [
				'check' => 'menu_search_style',
				'value' => 'modal',
			],
		],
		[
			'id' => 'menu_search_placeholder',
			'control' => [
				'label' => esc_html__( 'Placeholder Text', 'total' ),
				'type' => 'text',
			],
			'control_display' => [
				'check' => 'menu_search_style',
				'compare' => 'not_equal',
				'value' => [ 'custom_link', 'modal' ],
			],
		],
		[
			'id' => 'search_header_replace_bg',
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'Header Replace: Background Color', 'total' ),
				'type' => 'totaltheme_color',
			],
			'control_display' => [
				'check' => 'menu_search_style',
				'value' => 'header_replace',
			],
			'inline_css' => [
				'target' => '#searchform-header-replace',
				'alter' => 'background-color',
			],
		],
		[
			'id' => 'search_header_replace_color',
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'Header Replace: Color', 'total' ),
				'type' => 'totaltheme_color',
			],
			'control_display' => [
				'check' => 'menu_search_style',
				'value' => 'header_replace',
			],
			'inline_css' => [
				'target' => '#searchform-header-replace,#searchform-header-replace-close',
				'alter' => 'color',
			],
		],
		[
			'id' => 'search_header_replace_font_size',
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'Header Replace: Font Size', 'total' ),
				'type' => 'select',
				'choices' => 'font_size',
			],
			'control_display' => [
				'check' => 'menu_search_style',
				'value' => 'header_replace',
			],
			'inline_css' => [
				'target' => '#searchform-header-replace',
				'alter' => 'font-size',
				'sanitize' => 'utl_font_size',
			],
		],
		[
			'id' => 'search_header_replace_close_icon_size',
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'Header Replace: Close Icon Size', 'total' ),
				'type' => 'select',
				'choices' => 'icon_size',
			],
			'control_display' => [
				'check' => 'menu_search_style',
				'value' => 'header_replace',
			],
		],
		[
			'id' => 'search_dropdown_top_border',
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'Drop Down Top Border', 'total' ),
				'type' => 'totaltheme_color',
			],
			'inline_css' => [
				'target' => '#searchform-dropdown',
				'alter' => '--wpex-dropmenu-colored-top-border-color',
			],
			'control_display' => [
				'check' => 'menu_search_style',
				'value' => 'drop_down',
			],
		],
		[
			'id' => 'search_overlay_background',
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'Overlay Background', 'total' ),
				'type' => 'totaltheme_color',
			],
			'inline_css' => [
				'target' => '#wpex-searchform-overlay',
				'alter' => 'background-color',
			],
			'control_display' => [
				'check' => 'menu_search_style',
				'value' => 'overlay',
			],
		],
	]
];
