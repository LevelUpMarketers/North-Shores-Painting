<?php

defined( 'ABSPATH' ) || exit;

// General
$this->sections['wpex_tribe_events'] = [
	'settings' => [
		[
			'id' => 'tribe_events_total_styles',
			'default' => true,
			'control' => [
				'label' => esc_html__( 'Theme Styles', 'total' ),
				'type' => 'totaltheme_toggle',
				'description' => esc_html__( 'Enables theme styles. Disable to use the plugin with it\'s default styling only.', 'total' ),
			],
		],
		[
			'id' => 'tribe_events_page_header_details',
			'default' => true,
			'control' => [
				'label' => esc_html__( 'Page Header Event Details', 'total' ),
				'type' => 'totaltheme_toggle',
				'description' => esc_html__( 'If the page header is disabled the event details will display in the default location.', 'total' ),
			],
		],
		[
			'id' => 'tribe_events_main_page',
			'control' => [
				'label' => esc_html__( 'Events Page', 'total' ),
				'type' => 'wpex-dropdown-pages',
				'description' => esc_html__( 'Select the page being used as your main Events page.', 'total' ),
			],
		],
		[
			'id' => 'tribe_events_archive_layout',
			'default' => 'full-width',
			'control' => [
				'label' => esc_html__( 'Archives Layout', 'total' ),
				'type' => 'select',
				'choices' => 'post_layout',
			],
		],
		[
			'id' => 'tribe_events_single_layout',
			'default' => 'full-width',
			'control' => [
				'label' => esc_html__( 'Single Layout', 'total' ),
				'type' => 'select',
				'choices' => 'post_layout',
			],
		],
		/*
		@todo Can't implement yet do to bugs in the plugin.
		[
			'id' => 'tribe_events_singular_template',
			'control' => [
				'label' => esc_html__( 'Single Event Template', 'total' ),
				'type' => 'totaltheme_template_select',
			],
		],
		*/
	],
];
