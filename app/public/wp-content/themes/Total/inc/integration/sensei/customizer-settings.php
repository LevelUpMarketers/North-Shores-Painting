<?php
/**
 * Sensei Customizer Settings.
 */

defined( 'ABSPATH' ) || exit;

// Global settings.
$this->sections['wpex_sensei_general'] = [
	'title' => esc_html__( 'General', 'total' ),
	'panel' => 'wpex_sensei',
	'settings' => [
		[
			'id' => 'sensei_page_layout',
			'control' => [
				'label' => esc_html__( 'Global Layout', 'total' ),
				'type' => 'select',
				'choices' => 'post_layout',
				'description' => esc_html__( 'Set your default layout for all Sensie pages.', 'total' ),
			],
		],
		[
			'id' => 'sensei_learner_profile_layout',
			'control' => [
				'label' => esc_html__( 'Learner Profile Layout', 'total' ),
				'type' => 'select',
				'choices' => 'post_layout',
			],
		],
		[
			'id' => 'sensei_quiz_layout',
			'control' => [
				'label' => esc_html__( 'Quiz Layout', 'total' ),
				'type' => 'select',
				'choices' => 'post_layout',
			],
		],
	],
];

// Post type settings.

$single_blocks = [
	'media'       => esc_attr__( 'Media (Thumbnail, Slider, Video)', 'total' ),
	'title'       => esc_attr__( 'Title', 'total' ),
	'meta'        => esc_attr__( 'Meta', 'total' ),
	'content'     => esc_attr__( 'Content', 'total' ),
	'share'       => esc_attr__( 'Social Share', 'total' ),
];

$single_defaults = array_keys( $single_blocks );

$post_types = [
	'course' => esc_html__( 'Courses', 'total' ),
	'lesson' => esc_html__( 'Lessons', 'total' ),
];

foreach ( $post_types as $post_type => $post_type_name ) {

	$settings = [];

	// Bail if post type not registered
	if ( ! post_type_exists( $post_type ) ) {
		continue;
	}


	if ( 'course' === $post_type || 'lesson' === $post_type || 'question' === $post_type ) {

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
			'id' => "{$post_type}_archive_has_page_header",
			'default' => true,
			'control' => [
				'label' => esc_html__( 'Display Page Header Title?', 'total' ),
				'type' => 'checkbox',
			],
			/*'control_display' => [
				'check' => 'page_header_style',
				'vale' => 'hidden',
				'compare' => 'not_equal',
			],*/
		];

	}

	$settings[] = [
		'id' => "{$post_type}_single_heading",
		'default' => true,
		'control' => [
			'type' => 'totaltheme_heading',
			'label' => esc_html__( 'Post Settings', 'total' ),
		],
	];

	$settings[] = [
		'id' => "{$post_type}_singular_page_title",
		'default' => true,
		'control' => [
			'label' => esc_html__( 'Display Page Header Title?', 'total' ),
			'type' => 'checkbox',
		],
		/*'control_display' => [
			'check' => 'page_header_style',
			'vale' => 'hidden',
			'compare' => 'not_equal',
		],*/
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

	if ( 'lesson' !== $post_type ) {
		$settings[] = [
			'id' => "{$post_type}_single_blocks",
			'default' => $single_defaults,
			'control' => [
				'label' => esc_html__( 'Single Blocks', 'total' ),
				'type' => 'totaltheme_multi_select',
				'choices' => $single_blocks,
			],
		];
	}

	$this->sections[ "wpex_sensei_{$post_type}" ] = [
		'title' => $post_type_name,
		'panel' => 'wpex_sensei',
		'settings' => $settings,
	];

}
