<?php

defined( 'ABSPATH' ) || exit;

$legacy_typo = $legacy_typo ?? totaltheme_has_classic_styles();

$this->sections['wpex_loadmore'] = [
	'title' => esc_html__( 'Load More / Infinite Scroll', 'total' ),
	'panel' => 'wpex_general',
	'settings' => [
		[
			'id' => 'loadmore_btn_expanded',
			'default' => true,
			'control' => [
				'type' => 'totaltheme_toggle',
				'label' => esc_html__( 'Expanded Load More Button', 'total' ),
				'description' => esc_html__( 'This setting applies to archives only and does not apply to custom elements like the Post Cards.', 'total' ),
			],
		],
		[
			'id' => 'loadmore_text',
			'sanitize_callback' => 'wp_kses_post',
			'control' => [
				'type' => 'text',
				'label' => esc_html__( 'Load More Text', 'total' ),
				'input_attrs' => [
					'placeholder' => esc_html__( 'Load More', 'total' ),
				],
				'description' => esc_html__( 'Here you can change the load more text globally but it can always be dynamically altered via a child theme using the "wpex_loadmore_loading_text" filter.', 'total' ),
			],
		],
		[
			'id' => 'loadmore_svg',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_svg_select',
				'label' => esc_html__( 'Loader Icon', 'total' ),
				'choices' => 'TotalTheme\Pagination\Load_More::get_loader_svg_options',
			],
		],
		[
			'id' => 'loadmore_svg_size',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_length_unit',
				'units' => [ 'px' ],
				'label' => esc_html__( 'Load More Loader Icon Size', 'total' ),
				'description' => esc_html__( 'This is the loader icon displayed when clicking to load more posts or when loading posts via infinite scroll.', 'total' ),
				'placeholder' => $legacy_typo ? '20' : '24',
			],
		],
		[
			'id' => 'ajax_loader_svg_size',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_length_unit',
				'units' => [ 'px' ],
				'label' => esc_html__( 'Overlay Loader Icon Size', 'total' ),
				'description' => esc_html__( 'This is the loader icon displayed when loading new posts via an AJAX filter or ajaxed numbered pagination.', 'total' ),
				'placeholder' => '40',
			],
		],
		[
			'id' => 'loadmore_svg_color',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Loader Color', 'total' ),
			],
			'inline_css' => [
				// @todo should this setting also target the icon that shows up over ajaxed content like the ajax filter or numbered pagination?
				'target' => '.wpex-load-more-spinner,.vcex-loadmore-spinner',
				'alter' => 'color'
			],
		],
	],
];
