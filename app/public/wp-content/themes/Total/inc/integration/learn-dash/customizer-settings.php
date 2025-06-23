<?php

defined( 'ABSPATH' ) || exit;

// General
$this->sections['wpex_learndash_general'] = [
	'title' => esc_html__( 'General', 'total' ),
	'panel' => 'wpex_learndash',
	'settings' => [
		[
			'id' => 'learndash_wpex_metabox',
			'default' => true,
			'control' => [
				'label' => esc_html__( 'Enable Editor Settings Metabox?', 'total' ),
				'type' => 'checkbox',
			],
		],
		[
			'id' => 'learndash_breadcrumbs',
			'default' => true,
			'control' => [
				'label' => esc_html__( 'Show Breadcrumbs?', 'total' ),
				'type' => 'checkbox',
			],
		],
		[
			'id' => 'learndash_layout',
			'control' => [
				'label' => esc_html__( 'Default LearnDash Layout', 'total' ),
				'type' => 'select',
				'choices' => 'post_layout',
			],
		],
	],
];

$sections = [
	'courses'      => esc_html__( 'Courses', 'total' ),
	'lessons'      => esc_html__( 'Lessons', 'total' ),
	'topic'        => esc_html__( 'Topics', 'total' ),
	'quiz'         => esc_html__( 'Quizzes', 'total' ),
	'question'     => esc_html__( 'Questions', 'total' ),
	'certificates' => esc_html__( 'Certificates', 'total' ),
	'assignment'   => esc_html__( 'Assignments', 'total' ),
];

$entry_blocks = [
	'media'    => esc_attr__( 'Media (Thumbnail, Slider, Video)', 'total' ),
	'title'    => esc_attr__( 'Title', 'total' ),
	'meta'     => esc_attr__( 'Meta', 'total' ),
	'content'  => esc_attr__( 'Content', 'total' ),
	'readmore' => esc_attr__( 'Readmore', 'total' ),
];

$entry_defaults = array_keys( $entry_blocks );

$single_blocks = [
	'media'       => esc_attr__( 'Media (Thumbnail, Slider, Video)', 'total' ),
	'title'       => esc_attr__( 'Title', 'total' ),
	'meta'        => esc_attr__( 'Meta', 'total' ),
	'content'     => esc_attr__( 'Content', 'total' ),
	'page-links'  => esc_attr__( 'Page Links', 'total' ),
	'share'       => esc_attr__( 'Social Share', 'total' ),
	'comments'    => esc_attr__( 'Comments', 'total' ),
];

$single_defaults = array_keys( $single_blocks );

foreach ( $sections as $section => $title ) {

	$settings = [];

	$post_type = "sfwd-{$section}";

	// Bail if post type not registered
	if ( ! post_type_exists( $post_type ) ) {
		continue;
	}

	// Archive Settings (some types don't have archives ).
	if ( 'courses' === $section || 'lessons' === $section || 'question' === $section ) {

		$settings[] = [
			'id' => "{$post_type}_archive_heading",
			'default' => true,
			'control' => [
				'type' => 'totaltheme_heading',
				'label' => esc_html__( 'Archive Settings', 'total' ),
			],
		];

		$settings[] = [
			'id' => "{$post_type}_archives_layout",
			'control' => [
				'label' => esc_html__( 'Archives Layout', 'total' ),
				'type' => 'select',
				'choices' => 'post_layout',
			],
		];

		$settings[] = [
			'id' => "{$post_type}_grid_entry_columns",
			'default' => '1',
			'control' => [
				'label' => esc_html__( 'Archive Grid Columns', 'total' ),
				'type' => 'select',
				'choices' => 'wpex_grid_columns',
			],
		];

		$settings[] = [
			'id' => "{$post_type}_readmore_text",
			'control' => [
				'label' => esc_html__( 'Custom Read More Text', 'total' ),
				'type' => 'text',
			],
		];

		$settings[] = [
			'id' => "{$post_type}_entry_blocks",
			'default' => $entry_defaults,
			'control' => [
				'label' => esc_html__( 'Entry Blocks', 'total' ),
				'type' => 'totaltheme_multi_select',
				'choices' => $entry_blocks,
			],
		];

	}

	// Single settings.
	if ( 'courses' === $section || 'lessons' === $section || 'question' === $section ) {
		$settings[] = [
			'id' => "{$post_type}_single_heading",
			'default' => true,
			'control' => [
				'type' => 'totaltheme_heading',
				'label' => esc_html__( 'Post Settings', 'total' ),
			],
		];
	}
	$settings[] = [
		'id' => "{$post_type}_singular_page_title",
		'default' => true,
		'control' => [
			'label' => esc_html__( 'Display Page Header Title?', 'total' ),
			'type' => 'checkbox',
			'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::has_page_header',
		],
	];

	$settings[] = [
		'id' => "{$post_type}_single_layout",
		'control' => [
			'label' => esc_html__( 'Single Layout', 'total' ),
			'type' => 'select',
			'choices' => 'post_layout',
		],
	];

	$settings[] = [
		'id' => "{$post_type}_single_header",
		'control' => [
			'label' => esc_html__( 'Single Header Display', 'total' ),
			'type' => 'select',
			'choices' => [
				'' => esc_html__( 'Default','total' ),
				'post_title' => esc_html__( 'Post Title','total' ),
			],
		],
	];

	$settings[] = [
		'id' => "{$post_type}_singular_template",
		'control' => [
			'label' => esc_html__( 'Single Dynamic Template', 'total' ),
			'type' => 'totaltheme_template_select',
			'template_type' => 'single',
		],
	];

	$settings[] = [
		'id' => "{$post_type}_single_blocks",
		'default' => $single_defaults,
		'control' => [
			'label' => esc_html__( 'Single Blocks', 'total' ),
			'type' => 'totaltheme_multi_select',
			'choices' => $single_blocks,
		],
		'control_display' => [
			'check' => "{$post_type}_singular_template",
			'value' => '',
		],
	];

	$this->sections[ "wpex_learndash_{$section}" ] = [
		'title' => $title,
		'panel' => 'wpex_learndash',
		'settings' => $settings,
	];

}
