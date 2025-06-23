<?php

defined( 'ABSPATH' ) || exit;

$legacy_typo = $legacy_typo ?? totaltheme_has_classic_styles();

$this->sections['wpex_blog_single_related'] = [
	'title' => esc_html__( 'Related Posts', 'total' ),
	'panel' => 'wpex_blog',
	'description' => esc_html__( 'The related posts section displays at the bottom of the post content and can be enabled/disabled via the Post Layout Elements setting under the "Single Post" tab.', 'total' ),
	'settings' => [
		[
			'id' => 'blog_related_title',
			'transport' => 'postMessage',
			'default' => esc_html__( 'Related Posts', 'total' ),
			'sanitize_callback' => 'wp_kses_post',
			'control' => [
				'label' => esc_html__( 'Related Posts Title', 'total' ),
				'type' => 'text',
			],
		],
		[
			'id' => 'blog_related_entry_card_style',
			'control' => [
				'label' => esc_html__( 'Card Style', 'total' ),
				'type' => 'totaltheme_card_select',
			],
		],
		[
			'id' => 'blog_related_count',
			'default' => $legacy_typo ? 3 : 2,
			'control' => [
				'label' => esc_html__( 'Post Count', 'total' ),
				'type' => 'text',
			],
		],
		[
			'id' => 'blog_related_taxonomy',
			'default' => 'category',
			'control' => [
				'label' => esc_html__( 'Related By', 'total' ),
				'type' => 'select',
				'choices' => 'blog_taxonomies',
			],
		],
		[
			'id' => 'blog_related_order',
			'default' => 'description',
			'control' => [
				'label' => esc_html__( 'Order', 'total' ),
				'type' => 'select',
				'choices' => [
					'description' => esc_html__( 'DESC', 'total' ),
					'asc' => esc_html__( 'ASC', 'total' ),
				],
			],
		],
		[
			'id' => 'blog_related_orderby',
			'default' => 'date',
			'control' => [
				'label' => esc_html__( 'Order By', 'total' ),
				'type' => 'select',
				'choices' => [
					'date' => esc_html__( 'Date', 'total' ),
					'title' => esc_html__( 'Title', 'total' ),
					'modified' => esc_html__( 'Modified', 'total' ),
					'author' => esc_html__( 'Author', 'total' ),
					'rand' => esc_html__( 'Random', 'total' ),
					'comment_count' => esc_html__( 'Comment Count', 'total' ),
				],
			],
		],
		[
			'id' => 'blog_related_columns',
			'default' => $legacy_typo ? '3' : '2',
			'control' => [
				'label' => esc_html__( 'Columns', 'total' ),
				'type' => 'wpex-columns',
			],
		],
		[
			'id' => 'blog_related_gap',
			'control' => [
				'label' => esc_html__( 'Gap', 'total' ),
				'type' => 'select',
				'choices' => 'column_gap',
			],
		],
		[
			'id' => 'blog_related_overlay',
			'control' => [
				'label' => esc_html__( 'Image Overlay', 'total' ),
				'type' => 'select',
				'choices' => 'overlay',
			],
		],
		[
			'id' => 'blog_related_excerpt',
			'default' => 'on',
			'control' => [
				'label' => esc_html__( 'Excerpts', 'total' ),
				'type' => 'totaltheme_toggle',
				'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::hasnt_related_card',
			],
		],
		[
			'id' => 'blog_related_excerpt_length',
			'default' => '15',
			'control' => [
				'label' => esc_html__( 'Excerpt Length', 'total' ),
				'type' => 'text',
			],
		],
	],
];
