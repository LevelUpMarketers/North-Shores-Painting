<?php
/**
 * BuddyPress Customizer Settings.
 */

defined( 'ABSPATH' ) || exit;

// General
$this->sections['wpex_buddypress_general'] = array(
	'title' => esc_html__( 'General', 'total' ),
	'panel' => 'wpex_buddypress',
	'settings' => array(
		array(
			'id' => 'bp_enqueue_theme_styles',
			'default' => true,
			'transport' => 'refresh',
			'control' => array(
				'label' => esc_html__( 'Theme Styles', 'total' ),
				'type' => 'totaltheme_toggle',
				'description' => esc_html__( 'When enabled the theme will load a custom CSS file that tweaks some of the default BuddyPress styling to better match the theme.', 'total' ),
			),
		),
		array(
			'id' => 'bp_layout',
			'transport' => 'refresh',
			'control' => array(
				'label' => esc_html__( 'Layout', 'total' ),
				'type' => 'select',
				'choices' => 'post_layout',
			),
		),
	)
);

// Directories
$this->sections['wpex_buddypress_members'] = array(
	'title' => esc_html__( 'Directories', 'total' ),
	'panel' => 'wpex_buddypress',
	'settings' => array(
		array(
			'id' => 'bp_directory_page_title',
			'default' => true,
			'control' => array(
				'label' => esc_html__( 'Page Header Title', 'total' ),
				'type' => 'totaltheme_toggle',
			),
		),
		array(
			'id' => 'bp_directory_layout',
			'transport' => 'refresh',
			'control' => array(
				'label' => esc_html__( 'Layout', 'total' ),
				'type' => 'select',
				'choices' => 'post_layout',
			),
		),
	)
);

// User Profile
$this->sections['wpex_buddypress_user_profile'] = array(
	'title' => esc_html__( 'Profiles', 'total' ),
	'panel' => 'wpex_buddypress',
	'settings' => array(
		array(
			'id' => 'bp_user_singular_page_title',
			'default' => true,
			'control' => array(
				'label' => esc_html__( 'Page Header Title', 'total' ),
				'type' => 'totaltheme_toggle',
			),
		),
		array(
			'id' => 'bp_user_layout',
			'transport' => 'refresh',
			'control' => array(
				'label' => esc_html__( 'Layout', 'total' ),
				'type' => 'select',
				'choices' => 'post_layout',
			),
		),
	)
);