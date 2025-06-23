<?php

defined( 'ABSPATH' ) || exit;

$this->sections['wpex_search'] = [
	'title'  => esc_html__( 'Search Results Page', 'total' ),
	'panel'  => 'wpex_general',
	'settings' => [
		[
			'id' => 'search_has_page_header',
			'default' => true,
			'control' => [
				'label' => esc_html__( 'Page Header Title', 'total' ),
				'type' => 'totaltheme_toggle',
				'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::has_page_header',
			],
		],
		[
			'id' => 'search_custom_sidebar',
			'default' => true,
			'control' => [
				'label' => esc_html__( 'Custom Sidebar', 'total' ),
				'type' => 'totaltheme_toggle',
			],
		],
		[
			'id' => 'search_standard_posts_only',
			'control' => [
				'label' => esc_html__( 'Standard Posts Only', 'total' ),
				'type' => 'totaltheme_toggle',
			],
		],
		[
			'id' => 'search_results_cpt_loops',
			'default' => true,
			'control' => [
				'label' => esc_html__( 'Post Type Query Var', 'total' ),
				'type' => 'totaltheme_toggle',
				'description' => esc_html__( 'When enabled, using ?post_type={post_type_name} in the search URL will return that post type archive design as opposed to the general search archive design.', 'total' ),
				'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::hasnt_archive_template',
			],
		],
		[
			'id' => 'search_posts_per_page',
			'default' => '10',
			'control' => [
				'label' => esc_html__( 'Posts Per Page', 'total' ),
				'type' => 'text',
			],
		],
		[
			'id' => 'search_layout',
			'control' => [
				'label' => esc_html__( 'Layout', 'total' ),
				'type' => 'select',
				'choices' => 'post_layout',
			],
		],
		[
			'id' => 'search_archive_template_id',
			'control' => [
				'label' => esc_html__( 'Dynamic Template', 'total' ),
				'type' => 'totaltheme_template_select',
				'template_type' => 'search',
				'description' => esc_html__( 'Select a template to override the default output for your search results.', 'total' ),
			],
		],
		[
			'id' => 'search_style',
			'default' => 'default',
			'control' => [
				'label' => esc_html__( 'Style', 'total' ),
				'type' => 'select',
				'choices' => [
					'default' => esc_html__( 'Left Thumbnail', 'total' ),
					'blog' => esc_html__( 'Inherit From Blog','total' ),
				],
				'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::hasnt_entry_card_style_or_archive_template',
			],
		],
		[
			'id' => 'search_entry_card_style',
			'control' => [
				'label' => esc_html__( 'Card Style', 'total' ),
				'type' => 'totaltheme_card_select',
				'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::hasnt_archive_template',
			],
		],
		[
			'id' => 'search_archive_grid_style',
			'default' => 'fit-rows',
			'control' => [
				'label' => esc_html__( 'Grid Style', 'total' ),
				'type' => 'select',
				'choices' => [
					'fit-rows' => esc_html__( 'Fit Rows','total' ),
					'masonry' => esc_html__( 'Masonry','total' ),
				],
				'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::has_entry_card_style',
			],
		],
		[
			'id' => 'search_entry_columns',
			'default' => '2',
			'control' => [
				'label' => esc_html__( 'Columns', 'total' ),
				'type' => 'wpex-columns',
				'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::has_entry_card_style',
			],
		],
		[
			'id' => 'search_archive_grid_gap',
			'control' => [
				'label' => esc_html__( 'Gap', 'total' ),
				'type' => 'select',
				'choices' => 'column_gap',
				'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::has_entry_card_style',
			],
		],
		[
			'id' => 'search_entry_excerpt_length',
			'default' => '30',
			'control' => [
				'label' => esc_html__( 'Excerpt length', 'total' ),
				'type' => 'text',
				'description' => esc_html__( 'Enter 0 or leave blank to disable, enter -1 to display the full post content.', 'total' ),
				'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::hasnt_archive_template',
			],
		],
	],
];
