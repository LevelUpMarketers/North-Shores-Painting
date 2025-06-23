<?php

defined( 'ABSPATH' ) || exit;

$this->sections['wpex_blog_general'] = [
	'title' => esc_html__( 'General', 'total' ),
	'panel' => 'wpex_blog',
	'settings' => [
		[
			'id' => 'blog_custom_sidebar',
			'control' => [
				'label' => esc_html__( 'Custom Sidebar', 'total' ),
				'type' => 'totaltheme_toggle',
				'description' => esc_html__( 'After enabling you can go to the main Widgets admin dashboard to add widgets to your blog sidebar or you can refresh the Customizer to access the new widget area here.', 'total' ),
			],
		],
		[
			'id' => 'blog_page',
			'control' => [
				'label' => esc_html__( 'Main Page', 'total' ),
				'type' => 'wpex-dropdown-pages',
				'description' => esc_html__( 'This setting is used for breadcrumbs when your main blog page is not the homepage.', 'total' ),
			],
		],
		[
			'id' => 'blog_cats_exclude',
			'control' => [
				'label' => esc_html__( 'Exclude Categories From Blog', 'total' ),
				'type' => 'text',
				'description' => esc_html__( 'Enter the ID\'s of categories to exclude from the blog template or homepage blog seperated by a comma (no spaces).', 'total' ),
			],
		],
	],
];
