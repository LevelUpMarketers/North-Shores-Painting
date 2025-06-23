<?php

defined( 'ABSPATH' ) || exit;

// Archives.
$post_type_obj = get_post_type_object( 'staff' );

if ( ! empty( $post_type_obj->has_archive ) || ( is_taxonomy_viewable( 'staff_category' ) || is_taxonomy_viewable( 'staff_tag' ) ) ) {
	$this->sections['wpex_staff_archives'] = [
		'title' => esc_html__( 'Archives', 'total' ),
		'panel' => 'wpex_staff',
		'description' => esc_html__( 'The following options are for the post type category and tag archives.', 'total' ),
		'settings' => [
			[
				'id' => 'staff_archive_has_page_header',
				'default' => true,
				'control' => [
					'label' => esc_html__( 'Page Header Title', 'total' ),
					'type' => 'totaltheme_toggle',
					'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::has_page_header',
				],
			],
			[
				'id' => 'staff_archive_layout',
				'default' => 'full-width',
				'control' => [
					'label' => esc_html__( 'Page Layout', 'total' ),
					'type' => 'select',
					'choices' => 'post_layout',
				],
			],
			[
				'id' => 'staff_pagination_style',
				'control' => [
					'label' => esc_html__( 'Pagination Style', 'total' ),
					'type' => 'select',
					'choices' => 'TotalTheme\Pagination\Core::choices',
					'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::hasnt_archive_template',
				],
			],
			[
				'id' => 'staff_archive_template_id',
				'control' => [
					'label' => esc_html__( 'Dynamic Template', 'total' ),
					'type' => 'totaltheme_template_select',
					'template_type' => 'archive',
					'description' => esc_html__( 'Select a template to override the default output for the post type archive, category and tag entries.', 'total' ),
				],
			],
			[
				'id' => 'staff_entry_card_style',
				'control' => [
					'label' => esc_html__( 'Card Style', 'total' ),
					'type' => 'totaltheme_card_select',
					'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::hasnt_archive_template',
				],
			],
			[
				'id' => 'staff_archive_grid_style',
				'default' => 'fit-rows',
				'control' => [
					'label' => esc_html__( 'Grid Style', 'total' ),
					'type' => 'select',
					'choices' => [
						'fit-rows' => esc_html__( 'Fit Rows','total' ),
						'masonry' => esc_html__( 'Masonry','total' ),
						'no-margins' => esc_html__( 'No Margins','total' ),
					],
					'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::hasnt_archive_template',
				],
			],
			[
				'id' => 'staff_entry_columns',
				'default' => '3',
				'control' => [
					'label' => esc_html__( 'Columns', 'total' ),
					'type' => 'wpex-columns',
					'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::hasnt_archive_template',
				],
			],
			[
				'id' => 'staff_archive_grid_gap',
				'control' => [
					'label' => esc_html__( 'Gap', 'total' ),
					'type' => 'select',
					'choices' => 'column_gap',
					'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::hasnt_archive_template',
				],
			],
			[
				'id' => 'staff_archive_posts_per_page',
				'default' => '12',
				'control' => [
					'label' => esc_html__( 'Posts Per Page', 'total' ),
					'type' => 'text',
				],
			],
			[
				'id' => 'staff_entry_overlay_style',
				'default' => '',
				'control' => [
					'label' => esc_html__( 'Image Overlay', 'total' ),
					'type' => 'select',
					'choices' => 'overlay',
					'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::hasnt_archive_template',
				],
			],
			[
				'id' => 'staff_entry_image_hover_animation',
				'control' => [
					'label' => esc_html__( 'Image Hover Animation', 'total' ),
					'type' => 'select',
					'choices' => wpex_image_hovers(),
					'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::hasnt_archive_template',
				],
			],
			[
				'id' => 'staff_archive_grid_equal_heights',
				'default' => false,
				'control' => [
					'label' => esc_html__( 'Equal Heights', 'total' ),
					'description' => esc_html__( 'If enabled it will set each entry so they are the same height.', 'total' ),
					'type' => 'totaltheme_toggle',
					'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::entry_supports_equal_heights',
				],
			],
			[
				'id' => 'staff_entry_details',
				'default' => true,
				'control' => [
					'label' => esc_html__( 'Entry Details', 'total' ),
					'type' => 'totaltheme_toggle',
					'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::hasnt_entry_card_style_or_archive_template',
				],
			],
			[
				'id' => 'staff_entry_position',
				'default' => true,
				'control' => [
					'label' => esc_html__( 'Company Position', 'total' ),
					'type' => 'totaltheme_toggle',
					'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::hasnt_entry_card_style_or_archive_template',
				],
			],
			[
				'id' => 'staff_entry_excerpt_length',
				'default' => '20',
				'control' => [
					'label' => esc_html__( 'Excerpt length', 'total' ),
					'type' => 'text',
					'description' => esc_html__( 'Enter 0 or leave blank to disable, enter -1 to display the full post content.', 'total' ),
					'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::hasnt_archive_template',
				],
			],
			[
				'id' => 'staff_entry_social',
				'default' => true,
				'control' => [
					'label' => esc_html__( 'Social Links', 'total' ),
					'type' => 'totaltheme_toggle',
					'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::hasnt_entry_card_style_or_archive_template',
				],
			],
		],
	];
}

// Single.
if ( is_post_type_viewable( 'staff' ) ) {
	$this->sections['wpex_staff_single'] = [
		'title' => esc_html__( 'Single Post', 'total' ),
		'panel' => 'wpex_staff',
		'settings' => [
			[
				'id' => 'staff_singular_page_title',
				'default' => true,
				'control' => [
					'label' => esc_html__( 'Page Header Title', 'total' ),
					'type' => 'totaltheme_toggle',
					'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::has_page_header',
				],
			],
			[
				'id' => 'staff_next_prev',
				'default' => true,
				'control' => [
					'label' => esc_html__( 'Next/Previous Links', 'total' ),
					'type' => 'totaltheme_toggle',
				],
			],
			[
				'id' => 'staff_single_header_position',
				'default' => true,
				'control' => [
					'label' => esc_html__( 'Company Position', 'total' ),
					'type' => 'totaltheme_toggle',
					'description' => esc_html__( 'Enable to display the staff member company position in the page header title subheading region.', 'total' ),
				],
			],
			[
				'id' => 'staff_single_layout',
				'control' => [
					'label' => esc_html__( 'Page Layout', 'total' ),
					'type' => 'select',
					'choices' => 'post_layout',
				],
			],
			[
				'id' => 'staff_single_header',
				'default' => 'post_type_name',
				'control' => [
					'label' => esc_html__( 'Page Header Title Displays', 'total' ),
					'description' => esc_html__( 'Important: The page header title will fallback to the post title if the "Post Title" block is not added to the "Post Layout Elements".', 'total' ),
					'type' => 'select',
					'choices' => [
						'post_type_name' => esc_html__( 'Default', 'total' ),
						'post_title' => esc_html__( 'Post Title','total' ),
						'first_category' => esc_html__( 'First Category','total' ),
						'custom_text' => esc_html__( 'Custom Text','total' ),
					],
				],
				'control_display' => [
					'check' => 'staff_singular_page_title',
					'value' => 'true',
				],
			],
			[
				'id' => 'staff_single_header_custom_text',
				'sanitize_callback' => 'wp_kses_post',
				'control' => [
					'label' => esc_html__( 'Page Header Title Custom Text', 'total' ),
					'type' => 'text',
					'description' => \sprintf( esc_html__( 'This field supports %sdynamic variables%s', 'total-theme-core' ), '<a href="https://totalwptheme.com/docs/dynamic-variables/" target="_blank" rel="noopener noreferrer">', '&#8599;</a>' ),
					'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::can_page_header_custom_text',
				],
			],
			[
				'id' => 'staff_singular_template',
				'default' => '',
				'control' => [
					'label' => esc_html__( 'Dynamic Template', 'total' ),
					'type' => 'totaltheme_template_select',
					'template_type' => 'single',
				],
			],
			[
				'id' => 'staff_post_composer',
				'default' => 'content,related',
				'control' => [
					'label' => esc_html__( 'Post Layout Elements', 'total' ),
					'type' => 'totaltheme_blocks',
					'choices' => 'TotalTheme\Staff\Single_Blocks::choices',
					'description' => esc_html__( 'Used when displaying the default (non-dynamic) post template.', 'total' ),
					'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::hasnt_single_template',
				],
			],
		],
	];

	// Related
	$this->sections['wpex_staff_single_related'] = [
		'title' => esc_html__( 'Related Posts', 'total' ),
		'panel' => 'wpex_staff',
		'description' => esc_html__( 'The related posts section displays at the bottom of the post content and can be enabled/disabled via the Post Layout Elements setting under the "Single Post" tab.', 'total' ),
		'settings' => [
			[
				'id' => 'staff_related_title',
				'default' => esc_html__( 'Related Staff', 'total' ),
				'transport' => 'postMessage',
				'sanitize_callback' => 'wp_kses_post',
				'control' => [
					'label' => esc_html__( 'Related Posts Title', 'total' ),
					'type' => 'text',
				],
			],
			[
				'id' => 'staff_related_entry_card_style',
				'default' => '',
				'control' => [
					'label' => esc_html__( 'Card Style', 'total' ),
					'type' => 'totaltheme_card_select',
				],
			],
			[
				'id' => 'staff_related_count',
				'default' => '3',
				'control' => [
					'label' => esc_html__( 'Post Count', 'total' ),
					'type' => 'text',
				],
			],
			[
				'id' => 'staff_related_taxonomy',
				'default' => taxonomy_exists( 'staff_category' ) ? 'staff_category' : 'none',
				'control' => [
					'label' => esc_html__( 'Related By', 'total' ),
					'type' => 'select',
					'choices' => 'staff_taxonomies',
				],
			],
			[
				'id' => 'staff_related_order',
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
				'id' => 'staff_related_orderby',
				'default' => 'date',
				'control' => [
					'label' => esc_html__( 'Order By', 'total' ),
					'type' => 'select',
					'choices' => [
						'date'          => esc_html__( 'Date', 'total' ),
						'title'         => esc_html__( 'Title', 'total' ),
						'modified'      => esc_html__( 'Modified', 'total' ),
						'author'        => esc_html__( 'Author', 'total' ),
						'rand'          => esc_html__( 'Random', 'total' ),
						'comment_count' => esc_html__( 'Comment Count', 'total' ),
					],
				],
			],
			[
				'id' => 'staff_related_columns',
				'default' => '3',
				'control' => [
					'label' => esc_html__( 'Columns', 'total' ),
					'type' => 'wpex-columns',
				],
			],
			[
				'id' => 'staff_related_gap',
				'control' => [
					'label' => esc_html__( 'Gap', 'total' ),
					'type' => 'select',
					'choices' => 'column_gap',
				],
			],
			[
				'id' => 'staff_related_entry_overlay_style',
				'default' => '',
				'control' => [
					'label' => esc_html__( 'Image Overlay', 'total' ),
					'type' => 'select',
					'choices' => 'overlay'
				],
			],
			[
				'id' => 'staff_related_excerpts',
				'default' => true,
				'control' => [
					'label' => esc_html__( 'Entry Details', 'total' ),
					'type' => 'totaltheme_toggle',
					'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::hasnt_related_card',
				],
			],
			[
				'id' => 'staff_related_entry_excerpt_length',
				'default' => '20',
				'control' => [
					'label' => esc_html__( 'Excerpt length', 'total' ),
					'type' => 'text',
					'description' => esc_html__( 'Enter 0 or leave blank to disable, enter -1 to display the full post content.', 'total' ),
				],
			],
		],
	];

}

// Social Links
$this->sections['wpex_staff_social_links'] = [
	'title' => esc_html__( 'Social Links', 'total' ),
	'panel' => 'wpex_staff',
	'settings' => [
		[
			'id' => 'staff_social_show_icons',
			'default' => true,
			'control' => [
				'label' => esc_html__( 'Icons', 'total' ),
				'description' => esc_html__( 'If disabled it will display text links.', 'total' ),
				'type' => 'totaltheme_toggle',
			],
		],
		[
			'id' => 'staff_social_default_style',
			'default' => 'minimal-round',
			'control' => [
				'label' => esc_html__( 'Default Social Style', 'total' ),
				'type' => 'select',
				'choices' => 'social_styles',
			],
			'control_display' => [
				'check' => 'staff_social_show_icons',
				'value' => 'true',
			],
		],
		[
			'id' => 'staff_social_link_target',
			'default' => 'blank',
			'control' => [
				'label' => esc_html__( 'Link Target', 'total' ),
				'type' => 'select',
				'choices' => [
					'blank' => esc_html__( 'Blank', 'total' ),
					'self' => esc_html__( 'Self', 'total' ),
				],
			],
		],
		[
			'id' => 'staff_social_font_size',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_length_unit',
				'allow_numeric' => false,
				'label' => esc_html__( 'Icon Size', 'total' ),
			],
			'inline_css' => [
				'target' => '.staff-social',
				'alter' => 'font-size',
			],
		],
	],
];
