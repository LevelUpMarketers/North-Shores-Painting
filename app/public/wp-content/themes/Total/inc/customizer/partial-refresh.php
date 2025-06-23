<?php

defined( 'ABSPATH' ) || exit;

// Page Header.
$wp_customize->selective_refresh->add_partial( 'page_header', array(
	'selector' => '.page-header',
	'settings' => array(
		//'page_header_style', // this setting uses full page refresh
		//'page_header_full_width', // won't work causes bugs with alignment.
		'page_header_breakpoint',
		'page_header_background_img_style',
		'page_header_align_items',
		'page_header_overlay_opacity',
	),
	'primarySetting' => 'page_header_breakpoint',
	'container_inclusive' => true,
	'fallback_refresh' => false,
	'render_callback' => function() {
		wpex_get_template_part( 'page_header' );
	},
) );

// Author Bio.
$wp_customize->selective_refresh->add_partial( 'author_box_avatar_size', array(
	'selector' => '.author-bio',
	'settings' => array(
		'author_box_heading_tag',
		'author_box_avatar_size',
		'author_box_gap',
		'author_box_social_style',
		'author_box_avatar_border_radius',
	),
	'fallback_refresh' => false,
	'primarySetting' => 'author_box_avatar_size',
	'container_inclusive' => true,
	'render_callback' => function() {
		wp_reset_postdata();
		wpex_get_template_part( 'author_bio' );
	},
) );

// Social Sharing.
$wp_customize->selective_refresh->add_partial( 'social_share_sites', array(
	'selector' => '.wpex-social-share',
	'settings' => array(
		'social_share_sites',
		'social_share_position',
		'social_share_style',
		'social_share_shortcode',
		'social_share_heading',
		'social_share_heading_tag',
		'social_share_label',
		'social_share_link_border_radius',
		'social_share_stretch_items',
		'social_share_align',
	),
	'primarySetting' => 'social_share_sites',
	'container_inclusive' => true,
	'render_callback' => function() {
		wpex_get_template_part( 'social_share' );
	},
) );

// Breadcrumbs.
$wp_customize->selective_refresh->add_partial( 'breadcrumbs', array(
	'selector' => '.site-breadcrumbs',
	'settings' => array(
		'breadcrumbs_py',
		'breadcrumbs_mt',
		'breadcrumbs_mb',
	),
	'primarySetting' => 'breadcrumbs',
	'container_inclusive' => true,
	'render_callback' => function() {
		wpex_get_template_part( 'breadcrumbs' );
	},
) );

// Topbar Content.
$wp_customize->selective_refresh->add_partial( 'top_bar_content', array(
	'id' => 'top_bar_content',
	'selector' => '#top-bar-wrap',
	'settings' => array(
		'top_bar_content',
		'top_bar_style',
		'top_bar_social_alt',
	),
	'primarySetting' => 'top_bar_content',
	'container_inclusive' => true,
	'render_callback' => function() {
		wpex_get_template_part( 'topbar' );
	},
) );

// Flex header aside
$wp_customize->selective_refresh->add_partial( 'header_flex_aside_content', array(
	'id' => 'header_flex_aside_content',
	'selector' => '#site-header-flex-aside',
	'settings' => array(
		'header_flex_aside_content',
		'header_flex_aside_visibility',
	),
	'container_inclusive' => true,
	'render_callback' => function() {
		if ( totaltheme_call_static( 'Header\Core', 'has_flex_container' ) ) {
			wpex_get_template_part( 'header_flex_aside' );
		}
	},
) );

// Topbar Social.
$wp_customize->selective_refresh->add_partial( 'top_bar_social', array(
	'selector' => '#top-bar-wrap',
	'settings' => array(
		'top_bar_social_style',
		'top_bar_social_gap',
		'top_bar_social_profiles',
	),
	'primarySetting' => 'top_bar_social',
	'container_inclusive' => true,
	'render_callback' => function() {
		wpex_get_template_part( 'topbar' );
	},
) );

// Post Series.
$wp_customize->selective_refresh->add_partial( 'post_series', array(
	'id' => 'post_series_heading',
	'selector' => '#post-series',
	'settings' => array( 'post_series_heading' ),
	'primarySetting' => 'post_series_heading',
	'container_inclusive' => true,
	'fallback_refresh' => false,
	'render_callback' => function() {
		wpex_get_template_part( 'post_series' );
	},
) );

// Header Aside Content.
$wp_customize->selective_refresh->add_partial( 'header_aside', array(
	'id' => 'header_aside',
	'selector' => '#header-aside',
	'settings' => array(
		'header_aside',
		'header_aside_search',
		'header_aside_visibility',
	),
	'primarySetting' => 'header_aside',
	'container_inclusive' => true,
	'render_callback' => function() {
		wpex_get_template_part( 'header_aside' );
	},
) );

// Callout.
$wp_customize->selective_refresh->add_partial( 'callout_text', array(
	'selector' => '#footer-callout-wrap',
	'primarySetting' => 'callout_text',
	'container_inclusive' => true,
	'settings' => array(
		'callout',
		'callout_text',
		'callout_link',
		'callout_button_icon',
		'callout_button_style',
		'callout_button_color',
		'callout_button_icon_position',
		'callout_link_txt',
		'callout_visibility',
		'footer_callout_breakpoint',
		'footer_callout_bg_img_style',
	),
	'render_callback' => function() {
		wpex_get_template_part( 'footer_callout' );
	},
) );

// Footer Bottom.
$wp_customize->selective_refresh->add_partial( 'footer_bottom', [
	'selector' => '#footer-bottom',
	'settings' => [ 'bottom_footer_text_align', 'footer_copyright_text', 'bottom_footer_stack_align', 'bottom_footer_breakpoint' ],
	'primarySetting' => 'footer_bottom',
	'container_inclusive' => true,
	'render_callback' => function() {
		wpex_get_template_part( 'footer_bottom' );
	},
] );
