<?php

defined( 'ABSPATH' ) || exit;

$legacy_typo = $legacy_typo ?? totaltheme_has_classic_styles();

// General.
$this->sections['wpex_testimonials_general'] = [
	'title' => esc_html__( 'General', 'total' ),
	'description' => esc_html__( 'The following options are used for the default testimonial design and will not affect cards.', 'total' ),
	'panel' => 'wpex_testimonials',
	'settings' => [
		[
			'id' => 'testimonials_entry_img_size',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_length_unit',
				'units' => [ 'px' ],
				'label' => esc_html__( 'Entry Image Size', 'total' ),
				'placeholder' => '45',
			],
			'inline_css' => [
				'target' => '.testimonial-entry-thumb.default-dims img',
				'alter' => [ 'width', 'height' ],
				'sanitize' => 'px',
			],
		],
		[
			'id' => 'testimonial_entry_bg',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Entry Background', 'total' ),
			],
			'inline_css' => [
				'target' => '.testimonial-entry-content',
				'alter' => 'background',
			],
		],
		[
			'id' => 'testimonial_entry_pointer_bg',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Entry Pointer Background', 'total' ),
			],
			'inline_css' => [
				'target' => '.testimonial-caret',
				'alter' => 'border-top-color',
			],
		],
		[
			'id' => 'testimonial_entry_color',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Entry Color', 'total' ),
			],
			'inline_css' => [
				'target' => '.testimonial-entry-content',
				'alter' => 'color',
			],
		],
	],
];

// Archives.
$post_type_obj = get_post_type_object( 'testimonials' );

if ( ! empty( $post_type_obj->has_archive ) || is_taxonomy_viewable( 'testimonials_category' ) ) {
	$this->sections['wpex_testimonials_archives'] = [
		'title' => esc_html__( 'Archives & Entries', 'total' ),
		'panel' => 'wpex_testimonials',
		'description' => esc_html__( 'The following options are for the post type category and tag archives.', 'total' ),
		'settings' => [
			[
				'id' => 'testimonials_archive_has_page_header',
				'default' => true,
				'control' => [
					'label' => esc_html__( 'Page Header Title', 'total' ),
					'type' => 'totaltheme_toggle',
					'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::has_page_header',
				],
			],
			[
				'id' => 'testimonials_archive_layout',
				'default' => 'full-width',
				'control' => [
					'label' => esc_html__( 'Page Layout', 'total' ),
					'type' => 'select',
					'choices' => 'post_layout',
				],
			],
			[
				'id' => 'testimonials_pagination_style',
				'control' => [
					'label' => esc_html__( 'Pagination Style', 'total' ),
					'type' => 'select',
					'choices' => 'TotalTheme\Pagination\Core::choices',
					'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::hasnt_archive_template',
				],
			],
			[
				'id' => 'testimonials_archive_template_id',
				'control' => [
					'label' => esc_html__( 'Dynamic Template', 'total' ),
					'type' => 'totaltheme_template_select',
					'template_type' => 'archive',
					'description' => esc_html__( 'Select a template to override the default output for the post type archive, category and tag entries.', 'total' ),
				],
			],
			[
				'id' => 'testimonials_entry_card_style',
				'control' => [
					'label' => esc_html__( 'Card Style', 'total' ),
					'type' => 'totaltheme_card_select',
					'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::hasnt_archive_template',
				],
			],
			[
				'id' => 'testimonials_entry_columns',
				'default' => $legacy_typo ? '4' : '3',
				'control' => [
					'label' => esc_html__( 'Columns', 'total' ),
					'type' => 'wpex-columns',
					'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::hasnt_archive_template',
				],
			],
			[
				'id' => 'testimonials_archive_grid_style',
				'default' => 'fit-rows',
				'control' => [
					'label' => esc_html__( 'Grid Style', 'total' ),
					'type' => 'select',
					'choices'   => [
						'fit-rows' => esc_html__( 'Fit Rows','total' ),
						'masonry' => esc_html__( 'Masonry','total' ),
					],
					'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::hasnt_archive_template',
				],
			],
			[
				'id' => 'testimonials_archive_grid_gap',
				'control' => [
					'label' => esc_html__( 'Gap', 'total' ),
					'type' => 'select',
					'choices' => 'column_gap',
					'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::hasnt_archive_template',
				],
			],
			[
				'id' => 'testimonials_archive_posts_per_page',
				'default' => '12',
				'control' => [
					'label' => esc_html__( 'Posts Per Page', 'total' ),
					'type' => 'number',
				],
			],
			[
				'id' => 'testimonials_entry_excerpt_length',
				'default' => '-1',
				'control' => [
					'label' => esc_html__( 'Excerpt length', 'total' ),
					'type' => 'text',
					'description' => esc_html__( 'Enter 0 or leave blank to disable, enter -1 to display the full post content.', 'total' ),
					'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::has_entry_card_style',
				],
			],
			[
				'id' => 'testimonial_entry_title',
				'control' => [
					'label' => esc_html__( 'Entry Title', 'total' ),
					'type' => 'totaltheme_toggle',
					'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::hasnt_entry_card_style_or_archive_template',
				],
			],
		],
	];
}

// Single.
if ( is_post_type_viewable( 'testimonials' ) ) {
	$this->sections['wpex_testimonials_single'] = [
		'title' => esc_html__( 'Single Post', 'total' ),
		'panel' => 'wpex_testimonials',
		'settings' => [
			[
				'id' => 'testimonials_singular_page_title',
				'default' => true,
				'control' => [
					'label' => esc_html__( 'Page Header Title', 'total' ),
					'type' => 'totaltheme_toggle',
					'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::has_page_header',
				],
			],
			[
				'id' => 'testimonials_comments',
				'control' => [
					'label' => esc_html__( 'Comments', 'total' ),
					'type' => 'totaltheme_toggle',
					'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::hasnt_single_template',
				],
			],
			[
				'id' => 'testimonials_next_prev',
				'default' => 1,
				'control' => [
					'label' => esc_html__( 'Next/Previous Links', 'total' ),
					'type' => 'totaltheme_toggle',
				],
			],
			[
				'id' => 'testimonials_single_layout',
				'control' => [
					'label' => esc_html__( 'Single Layout', 'total' ),
					'type' => 'select',
					'choices' => 'post_layout',
				],
			],
			[
				'id' => 'testimonials_singular_template',
				'default' => '',
				'control' => [
					'label' => esc_html__( 'Dynamic Template', 'total' ),
					'type' => 'totaltheme_template_select',
					'template_type' => 'single',
				],
			],
			[
				'id' => 'testimonial_post_style',
				'default' => 'blockquote',
				'control' => [
					'label' => esc_html__( 'Single Style', 'total' ),
					'type' => 'select',
					'choices' => [
						'blockquote' => esc_html__( 'Testimonial', 'total' ),
						'standard' => esc_html__( 'Standard', 'total' ),
					],
					'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::hasnt_single_template',
				],
			],
		],
	];
}
