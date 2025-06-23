<?php

defined( 'ABSPATH' ) || exit;

$this->sections['wpex_pages'] = [
	'title'  => esc_html__( 'Pages', 'total' ),
	'panel'  => 'wpex_general',
	'settings' => [
		[
			'id' => 'page_singular_page_title',
			'default' => true,
			'control' => [
				'label' => esc_html__( 'Page Header Title', 'total' ),
				'type' => 'totaltheme_toggle',
				'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::has_page_header',
			],
		],
		[
			'id' => 'pages_custom_sidebar',
			'default' => true,
			'control' => [
				'label' => esc_html__( 'Custom Sidebar', 'total' ),
				'type' => 'totaltheme_toggle',
			],
		],
		[
			'id' => 'page_single_layout',
			'control' => [
				'label' => esc_html__( 'Page Layout', 'total' ),
				'type' => 'select',
				'choices' => 'post_layout',
			],
		],
		[
			'id' => 'page_singular_template',
			'control' => [
				'label' => esc_html__( 'Dynamic Template', 'total' ),
				'type' => 'totaltheme_template_select',
				'template_type' => 'single',
			],
		],
		[
			'id' => 'page_composer',
			'default' => 'content',
			'control' => [
				'label' => esc_html__( 'Post Layout Elements', 'total' ),
				'type' => 'totaltheme_blocks',
				'choices' => 'TotalTheme\Page\Single_Blocks::choices',
				'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::hasnt_single_template',
			],
		],
	],
];
