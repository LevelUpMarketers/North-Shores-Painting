<?php

defined( 'ABSPATH' ) || exit;

$post_type_obj = get_post_type_object( 'portfolio' );

if ( ! empty( $post_type_obj->has_archive ) || ( is_taxonomy_viewable( 'portfolio_category' ) || is_taxonomy_viewable( 'portfolio_tag' ) ) ) {
	$this->sections['wpex_portfolio_archives'] = [
		'title' => esc_html__( 'Archives & Entries', 'total' ),
		'panel' => 'wpex_portfolio',
		'description' => esc_html__( 'The following options are for the post type category and tag archives.', 'total' ),
		'settings' => [
			[
				'id' => 'portfolio_archive_has_page_header',
				'default' => true,
				'control' => [
					'label' => esc_html__( 'Page Header Title', 'total' ),
					'type' => 'totaltheme_toggle',
				],
				'control_display' => [
					'check' => 'page_header_style',
					'value' => 'hidden',
					'compare' => 'not_equal',
				],
			],
			[
				'id' => 'portfolio_archive_layout',
				'default' => 'full-width',
				'control' => [
					'label' => esc_html__( 'Page Layout', 'total' ),
					'type' => 'select',
					'choices' => 'post_layout',
				],
			],
			[
				'id' => 'portfolio_pagination_style',
				'control' => [
					'label' => esc_html__( 'Pagination Style', 'total' ),
					'type' => 'select',
					'choices' => 'TotalTheme\Pagination\Core::choices',
					'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::hasnt_archive_template',
				],
			],
			[
				'id' => 'portfolio_archive_template_id',
				'control' => [
					'label' => esc_html__( 'Dynamic Template', 'total' ),
					'type' => 'totaltheme_template_select',
					'template_type' => 'archive',
					'description' => esc_html__( 'Select a template to override the default output for the post type archive, category and tag entries.', 'total' ),
				],
			],
			[
				'id' => 'portfolio_entry_card_style',
				'control' => [
					'label' => esc_html__( 'Card Style', 'total' ),
					'type' => 'totaltheme_card_select',
					'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::hasnt_archive_template',
				],
			],
			[
				'id' => 'portfolio_archive_grid_style',
				'default' => 'fit-rows',
				'control' => [
					'label' => esc_html__( 'Grid Style', 'total' ),
					'type' => 'select',
					'choices'   => [
						'fit-rows' => esc_html__( 'Fit Rows','total' ),
						'masonry' => esc_html__( 'Masonry','total' ),
						'no-margins' => esc_html__( 'No Margins','total' ),
					],
					'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::hasnt_archive_template',
				],
			],
			[
				'id' => 'portfolio_entry_columns',
				'default' => '4',
				'control' => [
					'label' => esc_html__( 'Columns', 'total' ),
					'type' => 'wpex-columns',
					'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::hasnt_archive_template',
				],
			],
			[
				'id' => 'portfolio_archive_grid_gap',
				'control' => [
					'label' => esc_html__( 'Gap', 'total' ),
					'type' => 'select',
					'choices' => 'column_gap',
					'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::hasnt_archive_template',
				],
			],
			[
				'id' => 'portfolio_archive_posts_per_page',
				'default' => '12',
				'control' => [
					'label' => esc_html__( 'Posts Per Page', 'total' ),
					'type' => 'text',
				],
			],
			[
				'id' => 'portfolio_entry_overlay_style',
				'default' => '',
				'control' => [
					'label' => esc_html__( 'Image Overlay', 'total' ),
					'type' => 'select',
					'choices' => 'overlay',
					'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::hasnt_archive_template',
				],
			],
			[
				'id' => 'portfolio_entry_image_hover_animation',
				'control' => [
					'label' => esc_html__( 'Image Hover Animation', 'total' ),
					'type' => 'select',
					'choices' => wpex_image_hovers(),
					'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::hasnt_archive_template',
				],
			],
			[
				'id' => 'portfolio_archive_grid_equal_heights',
				'default' => false,
				'control' => [
					'label' => esc_html__( 'Equal Heights', 'total' ),
					'description' => esc_html__( 'If enabled it will set each entry so they are the same height.', 'total' ),
					'type' => 'totaltheme_toggle',
					'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::entry_supports_equal_heights',
				],
			],
			[
				'id' => 'portfolio_entry_details',
				'default' => true,
				'control' => [
					'label' => esc_html__( 'Entry Details', 'total' ),
					'type' => 'totaltheme_toggle',
					'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::hasnt_entry_card_style_or_archive_template',
				],
			],
			[
				'id' => 'portfolio_entry_excerpt_length',
				'default' => '20',
				'control' => [
					'label' => esc_html__( 'Excerpt length', 'total' ),
					'type' => 'text',
					'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::hasnt_archive_template',
				],
			],
		],
	];
}

// Single.
if ( is_post_type_viewable( 'portfolio' ) ) {
	$this->sections['wpex_portfolio_single'] = [
		'title' => esc_html__( 'Single Post', 'total' ),
		'panel' => 'wpex_portfolio',
		'settings' => [
			[
				'id' => 'portfolio_singular_page_title',
				'default' => true,
				'control' => [
					'label' => esc_html__( 'Page Header Title', 'total' ),
					'type' => 'totaltheme_toggle',
					'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::has_page_header',
				],
			],
			[
				'id' => 'portfolio_next_prev',
				'default' => true,
				'control' => [
					'label' => esc_html__( 'Next/Previous Links', 'total' ),
					'type' => 'totaltheme_toggle',
				],
			],
			[
				'id' => 'portfolio_single_layout',
				'default' => 'full-width',
				'control' => [
					'label' => esc_html__( 'Page Layout', 'total' ),
					'type' => 'select',
					'choices' => 'post_layout',
				],
			],
			[
				'id' => 'portfolio_single_header',
				'default' => 'post_type_name',
				'control' => [
					'label' => esc_html__( 'Page Header Title Displays', 'total' ),
					'type' => 'select',
					'choices' => [
						'post_type_name' => esc_html__( 'Default', 'total' ),
						'post_title' => esc_html__( 'Post Title','total' ),
						'first_category' => esc_html__( 'First Category','total' ),
						'custom_text' => esc_html__( 'Custom Text','total' ),
					],
				],
				'control_display' => [
					'check' => 'portfolio_singular_page_title',
					'value' => 'true',
				],
			],
			[
				'id' => 'portfolio_single_header_custom_text',
				'sanitize_callback' => 'wp_kses_post',
				'control' => [
					'label' => esc_html__( 'Page Header Title Custom Text', 'total' ),
					'type' => 'text',
					'description' => \sprintf( esc_html__( 'This field supports %sdynamic variables%s', 'total-theme-core' ), '<a href="https://totalwptheme.com/docs/dynamic-variables/" target="_blank" rel="noopener noreferrer">', '&#8599;</a>' ),
					'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::can_page_header_custom_text',
				],
			],
			[
				'id' => 'portfolio_singular_template',
				'default' => '',
				'control' => [
					'label' => esc_html__( 'Dynamic Template', 'total' ),
					'type' => 'totaltheme_template_select',
					'template_type' => 'single',
				],
			],
			[
				'id' => 'portfolio_post_composer',
				'default' => 'content,share,related',
				'control' => [
					'label' => esc_html__( 'Post Layout Elements', 'total' ),
					'type' => 'totaltheme_blocks',
					'choices' => 'TotalTheme\Portfolio\Single_Blocks::choices',
					'description' => esc_html__( 'Used when displaying the default (non-dynamic) post template.', 'total' ),
					'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::hasnt_single_template',
				],
			],
		],
	];

	// Related
	$this->sections['wpex_portfolio_related'] = [
		'title' => esc_html__( 'Related Posts', 'total' ),
		'panel' => 'wpex_portfolio',
		'description' => esc_html__( 'The related posts section displays at the bottom of the post content and can be enabled/disabled via the Post Layout Elements setting under the "Single Post" tab.', 'total' ),
		'settings' => [
			[
				'id' => 'portfolio_related_title',
				'transport' => 'postMessage',
				'default' => esc_html__( 'Related Projects', 'total' ),
				'sanitize_callback' => 'wp_kses_post',
				'control' => [
					'label' => esc_html__( 'Related Posts Title', 'total' ),
					'type' => 'text',
				],
			],
			[
				'id' => 'portfolio_related_entry_card_style',
				'default' => '',
				'control' => [
					'label' => esc_html__( 'Card Style', 'total' ),
					'type' => 'totaltheme_card_select',
				],
			],
			[
				'id' => 'portfolio_related_count',
				'default' => 4,
				'control' => [
					'label' => esc_html__( 'Post Count', 'total' ),
					'type' => 'number',
				],
			],
			[
				'id' => 'portfolio_related_taxonomy',
				'default' => taxonomy_exists( 'portfolio_category' ) ? 'portfolio_category' : 'none',
				'control' => [
					'label' => esc_html__( 'Related By', 'total' ),
					'type' => 'select',
					'choices' => 'portfolio_taxonomies',
				],
			],
			[
				'id' => 'portfolio_related_order',
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
				'id' => 'portfolio_related_orderby',
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
				'id' => 'portfolio_related_columns',
				'default' => '4',
				'control' => [
					'label' => esc_html__( 'Columns', 'total' ),
					'type' => 'wpex-columns',
				],
			],
			[
				'id' => 'portfolio_related_gap',
				'control' => [
					'label' => esc_html__( 'Gap', 'total' ),
					'type' => 'select',
					'choices' => 'column_gap',
				],
			],
			[
				'id' => 'portfolio_related_entry_overlay_style',
				'default' => '',
				'control' => [
					'label' => esc_html__( 'Image Overlay', 'total' ),
					'type' => 'select',
					'choices' => 'overlay',
				],
			],
			[
				'id' => 'portfolio_related_entry_excerpt_length',
				'default' => '20',
				'control' => [
					'label' => esc_html__( 'Excerpt length', 'total' ),
					'type' => 'text',
					'description' => esc_html__( 'Enter 0 or leave blank to disable, enter -1 to display the full post content.', 'total' ),
				],
			],
			[
				'id' => 'portfolio_related_excerpts',
				'default' => true,
				'control' => [
					'label' => esc_html__( 'Entry Details', 'total' ),
					'type' => 'totaltheme_toggle',
					'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::hasnt_related_card',
				],
			],
		],
	];
}
