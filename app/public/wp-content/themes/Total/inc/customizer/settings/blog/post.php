<?php

defined( 'ABSPATH' ) || exit;

$this->sections['wpex_blog_single'] = [
	'title' => esc_html__( 'Single Post', 'total' ),
	'panel' => 'wpex_blog',
	'settings' => [
		[
			'id' => 'post_singular_page_title',
			'default' => true,
			'control' => [
				'label' => esc_html__( 'Page Header Title', 'total' ),
				'type' => 'totaltheme_toggle',
				'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::has_page_header',
			],
		],
		[
			'id' => 'blog_single_layout',
			'control' => [
				'label' => esc_html__( 'Page Layout', 'total' ),
				'type' => 'select',
				'choices' => 'post_layout',
			],
		],
		[
			'id' => 'blog_single_header',
			'default' => 'custom_text',
			'control' => [
				'label' => esc_html__( 'Page Header Title Displays', 'total' ),
				'type' => 'select',
				'choices' => [
					'custom_text' => esc_html__( 'Custom Text','total' ),
					'post_title' => esc_html__( 'Post Title','total' ),
					'first_category' => esc_html__( 'First Category','total' ),
				],
			],
			'control_display' => [
				'check' => 'post_singular_page_title',
				'value' => 'true',
			],
		],
		[
			'id' => 'blog_single_header_custom_text',
			'default' => esc_html__( 'Blog', 'total' ),
			'sanitize_callback' => 'wp_kses_post',
			'control' => [
				'label' => esc_html__( 'Page Header Title Custom Text', 'total' ),
				'type' => 'text',
				'description' => \sprintf( esc_html__( 'This field supports %sdynamic variables%s', 'total-theme-core' ), '<a href="https://totalwptheme.com/docs/dynamic-variables/" target="_blank" rel="noopener noreferrer">', '&#8599;</a>' ),
				'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::can_page_header_custom_text',
			],
		],
		// Post Layout
		[
			'id' => 'blog_single_post_layout_heading',
			'control' => [
				'type' => 'totaltheme_heading',
				'label' => esc_html__( 'Post Layout', 'total' ),
			],
		],
		[
			'id' => 'post_singular_template',
			'default' => '',
			'control' => [
				'label' => esc_html__( 'Dynamic Template', 'total' ),
				'type' => 'totaltheme_template_select',
				'template_type' => 'single',
			],
		],
		[
			'id' => 'blog_single_composer',
			'default' => 'featured_media,title,meta,post_series,the_content,post_tags,social_share,author_bio,related_posts,comments',
			'control' => [
				'label' => esc_html__( 'Single Layout Elements', 'total' ),
				'type' => 'totaltheme_blocks',
				'choices' => 'TotalTheme\Blog\Single_Blocks::choices',
				'description' => esc_html__( 'Used when displaying the default (non-dynamic) post template.', 'total' ),
				'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::hasnt_single_template',
			],
		],
		// Post Settings
		[
			'id' => 'blog_single_post_settings_heading',
			'control' => [
				'type' => 'totaltheme_heading',
				'label' => esc_html__( 'Post Settings', 'total' ),
				'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::hasnt_single_template',
			],
		],
		[
			'id' => 'blog_post_media_position_above',
			'control' => [
				'label' => esc_html__( 'Large Featured Image', 'total' ),
				'type' => 'totaltheme_toggle',
				'description' => esc_html__( 'Enable to display your featured image above your post content and sidebar.', 'total' ),
			//	'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::hasnt_single_template', // this will still show even if you have a dynamic template.
			],
		],
		[
			'id' => 'blog_post_image_lightbox',
			'control' => [
				'label' => esc_html__( 'Thumbnail Lightbox', 'total' ),
				'type' => 'totaltheme_toggle',
				'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::hasnt_single_template',
			],
		],
		[
			'id' => 'blog_thumbnail_caption',
			'control' => [
				'label' => esc_html__( 'Thumbnail Caption', 'total' ),
				'type' => 'totaltheme_toggle',
				'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::hasnt_single_template',
			],
		],
		[
			'id' => 'blog_next_prev',
			'default' => true,
			'control' => [
				'label' => esc_html__( 'Next/Previous Links', 'total' ),
				'type' => 'totaltheme_toggle',
			],
		],
		[
			'id' => 'blog_post_meta_sections',
			'default' => 'date,author,categories,comments',
			'control' => [
				'label' => esc_html__( 'Meta Sections', 'total' ),
				'type' => 'totaltheme_blocks',
				'choices' => 'TotalTheme\Meta::registered_blocks',
				'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::has_blog_single_meta',
			],
		],
	],
];
