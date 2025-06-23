<?php

defined( 'ABSPATH' ) || exit;

$this->sections['wpex_blog_archives'] = [
	'title' => esc_html__( 'Archives & Entries', 'total' ),
	'panel' => 'wpex_blog',
	'settings' => [
		[
			'id' => 'blog_archive_has_page_header',
			'default' => true,
			'control' => [
				'label' => esc_html__( 'Page Header Title', 'total' ),
				'type' => 'totaltheme_toggle',
				'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::has_page_header',
			],
		],
		[
			'id' => 'blog_archives_layout',
			'control' => [
				'label' => esc_html__( 'Page Layout', 'total' ),
				'type' => 'select',
				'choices' => 'post_layout',
			],
		],
		[
			'id' => 'category_description_position',
			'control' => [
				'label' => esc_html__( 'Category & Tag Description Position', 'total' ),
				'type' => 'select',
				'choices' => [
					''			 => esc_html__( 'Default', 'total' ),
					'under_title' => esc_html__( 'Under Title', 'total' ),
					'above_loop' => esc_html__( 'Before Entries', 'total' ),
					'hidden' => esc_html__( 'Hidden', 'total' ),
				],
			],
		],
		[
			'id' => 'blog_pagination_style',
			'control' => [
				'label' => esc_html__( 'Pagination Style', 'total' ),
				'type' => 'select',
				'choices' => 'TotalTheme\Pagination\Core::choices',
				'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::hasnt_archive_template',
			],
		],
		[
			'id' => 'blog_archive_template_id',
			'control' => [
				'label' => esc_html__( 'Dynamic Template', 'total' ),
				'type' => 'totaltheme_template_select',
				'template_type' => 'archive',
				'description' => esc_html__( 'Select a template to override the default output for the main blog page, categories and tags.', 'total' ),
			],
		],
		// Entry Blocks
		[
			'id' => 'blog_archives_heading_blocks',
			'control' => [
				'type' => 'totaltheme_heading',
				'label' => esc_html__( 'Entry Layout', 'total' ),
				'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::hasnt_archive_template',
			],
		],
		[
			'id' => 'blog_entry_card_style',
			'control' => [
				'label' => esc_html__( 'Card Style', 'total' ),
				'type' => 'totaltheme_card_select',
				'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::hasnt_archive_template',
			],
		],
		[
			'id' => 'blog_style',
			'control' => [
				'label' => esc_html__( 'Entry Style', 'total' ),
				'type' => 'select',
				'choices' => [
					'' => esc_html__( 'Default', 'total' ),
					'large-image-entry-style' => esc_html__( 'Large Image','total' ),
					'thumbnail-entry-style' => esc_html__( 'Left Thumbnail','total' ),
					'grid-entry-style' => esc_html__( 'Grid','total' ),
				],
				'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::hasnt_entry_card_style_or_archive_template',
			],
		],
		[
			'id' => 'blog_left_thumbnail_width',
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'Left Thumbnail Width', 'total' ),
				'type' => 'totaltheme_length_unit',
				'default_unit' => '%',
				'placeholder' => '46',
				'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::has_blog_left_thumb',
			],
			'inline_css' => [
				'target' => '.blog-entry',  // note it should only target the blog
				'alter' => '--wpex-entry-left-thumbnail-media-width',
			],
		],
		[
			'id' => 'blog_right_content_width',
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'Right Content Width', 'total' ),
				'type' => 'totaltheme_length_unit',
				'default_unit' => '%',
				'placeholder' => '50',
				'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::has_blog_left_thumb',
			],
			'inline_css' => [
				'target' => '.blog-entry', // note it should only target the blog
				'alter' => '--wpex-entry-left-thumbnail-content-width',
			],
		],
		[
			'id' => 'blog_grid_style',
			'control' => [
				'label' => esc_html__( 'Grid Style', 'total' ),
				'type' => 'select',
				'choices' => [
					'' => esc_html__( 'Default', 'total' ),
					'fit-rows' => esc_html__( 'Fit Rows', 'total' ),
					'masonry' => esc_html__( 'Masonry', 'total' ),
				],
				'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::has_blog_grid',
			],
		],
		[
			'id' => 'blog_grid_columns',
			'control' => [
				'label' => esc_html__( 'Grid Columns', 'total' ),
				'type' => 'select',
				'type' => 'wpex-columns',
				'choices' => [
					''  => esc_html__( 'Default', 'total' ),
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
				],
				'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::has_blog_grid',
			],
		],
		[
			'id' => 'blog_grid_gap',
			'control' => [
				'label' => esc_html__( 'Gap', 'total' ),
				'type' => 'select',
				'choices' => 'column_gap',
				'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::has_blog_grid',
			],
		],
		[
			'id' => 'blog_entry_composer',
			'default' => 'featured_media,title,meta,excerpt_content,readmore',
			'control' => [
				'label' => esc_html__( 'Entry Layout Elements', 'total' ),
				'type' => 'totaltheme_blocks',
				'choices' => 'TotalTheme\Blog\Entry_Blocks::choices',
				'description' => esc_html__( 'Used for the default non-card style layout.', 'total' ),
				'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::hasnt_entry_card_style_or_archive_template',
			],
		],
		// Other
		[
			'id' => 'blog_archives_heading_entry_settings',
			'control' => [
				'type' => 'totaltheme_heading',
				'label' => esc_html__( 'Entry Settings', 'total' ),
				'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::hasnt_entry_card_style_or_archive_template',
			],
		],
		[
			'id' => 'blog_archive_grid_equal_heights',
			'control' => [
				'label' => esc_html__( 'Equal Heights', 'total' ),
				'description' => esc_html__( 'If enabled it will set each entry so they are the same height.', 'total' ),
				'type' => 'totaltheme_toggle',
				'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::entry_supports_equal_heights',
			],
		],
		[
			'id' => 'blog_entry_image_lightbox',
			'control' => [
				'label' => esc_html__( 'Thumbnail Lightbox', 'total' ),
				'type' => 'totaltheme_toggle',
				'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::hasnt_entry_card_style_or_archive_template',
			],
		],
		[
			'id' => 'blog_entry_author_avatar',
			'control' => [
				'label' => esc_html__( 'Author Avatar', 'total' ),
				'description' => esc_html__( 'If enabled it will display the post author avatar next to the entry title.', 'total' ),
				'type' => 'totaltheme_toggle',
				'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::hasnt_entry_card_style_or_archive_template',
			],
		],
		[
			'id' => 'blog_entry_video_output',
			'default' => true,
			'control' => [
				'label' => esc_html__( 'Featured Videos', 'total' ),
				'type' => 'totaltheme_toggle',
				'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::hasnt_entry_card_style_or_archive_template',
			],
		],
		[
			'id' => 'blog_entry_audio_output',
			'control' => [
				'label' => esc_html__( 'Featured Audio', 'total' ),
				'type' => 'totaltheme_toggle',
				'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::hasnt_entry_card_style_or_archive_template',
			],
		],
		[
			'id' => 'blog_entry_gallery_output',
			'default' => true,
			'control' => [
				'label' => esc_html__( 'Gallery Slider', 'total' ),
				'type' => 'totaltheme_toggle',
				'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::hasnt_entry_card_style_or_archive_template',
			],
		],
		[
			'id' => 'blog_exceprt',
			'default' => 'on',
			'control' => [
				'label' => esc_html__( 'Auto Excerpts', 'total' ),
				'description' => esc_html__( 'If enabled the theme will automatically generate an excerpt for your entries based on the post content.', 'total' ),
				'type' => 'totaltheme_toggle',
				'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::hasnt_entry_card_style_or_archive_template',
			],
		],
		[
			'id' => 'blog_entry_overlay',
			'control' => [
				'label' => esc_html__( 'Image Overlay', 'total' ),
				'type' => 'select',
				'choices' => 'overlay',
				'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::hasnt_archive_template',
			],
		],
		[
			'id' => 'blog_entry_image_hover_animation',
			'control' => [
				'label' => esc_html__( 'Image Hover Animation', 'total' ),
				'type' => 'select',
				'choices' => wpex_image_hovers(),
				'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::hasnt_archive_template',
			],
		],
		[
			'id' => 'blog_entry_meta_sections',
			'default' => 'date,author,categories,comments',
			'control' => [
				'label' => esc_html__( 'Meta Sections', 'total' ),
				'type' => 'totaltheme_blocks',
				'choices' => 'TotalTheme\Meta::registered_blocks',
				'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::has_blog_entry_meta',
			],
		],
		[
			'id' => 'blog_excerpt_length',
			'default' => '40',
			'control' => [
				'label' => esc_html__( 'Excerpt length', 'total' ),
				'type' => 'text',
				'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::hasnt_archive_template',
			],
		],
		[
			'id' => 'blog_entry_readmore_text',
			'default' => esc_html__( 'Read more', 'total' ),
			'control' => [
				'label' => esc_html__( 'Read More Button Text', 'total' ),
				'type' => 'text',
				'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::has_blog_entry_readmore',
			],
		],
	],
];
