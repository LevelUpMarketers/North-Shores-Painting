<?php

defined( 'ABSPATH' ) || exit;

$this->sections['wpex_local_scroll'] = [
	'title'  => esc_html__( 'Local Scroll Links', 'total' ),
	'panel'  => 'wpex_general',
	'settings' => [
		[
			'id' => 'local_scroll_offset',
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'Local Scroll Offset', 'total' ),
				'type' => 'totaltheme_length_unit',
				'units' => [ 'px' ],
				'placeholder' => '0',
				'description' => esc_html__( 'When scrolling to a local section, the browser will position the view at the very top of the targeted element. You can use this setting to add extra space between the section and the top of the viewport or sticky header.', 'total' ),
			],
		],
		[
			'id' => 'local_scroll_find_links',
			'default' => true,
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'Automatic Local Links', 'total' ),
				'type' => 'totaltheme_toggle',
				'description' => esc_html__( 'When enabled the theme will try and automatically find local links and apply the local-scroll-link class to them for a smooth scroll affect when clicked.', 'total' ),
			],
		],
		[
			'id' => 'scroll_to_hash',
			'default' => true,
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'Scroll To URL Hash', 'total' ),
				'type' => 'totaltheme_toggle',
				'description' => esc_html__( 'When enabled the site will scroll to a local section on page load if the local section ID is in the url using the format site.com/#local-section-id.', 'total' ),
			],
		],
		[
			'id' => 'scroll_to_hash_timeout',
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'Scroll To Hash Timeout', 'total' ),
				'type' => 'text',
				'input_attrs' => [
					'placeholder' => '500',
				],
				'description' => esc_html__( 'Time in milliseconds to wait before scrolling.', 'total' ),
			],
			'control_display' => [
				'check' => 'scroll_to_hash',
				'value' => 'true',
			],
		],
		[
			'id' => 'local_scroll_highlight',
			'transport' => 'postMessage',
			'default' => true,
			'control' => [
				'label' => esc_html__( 'Highlight Links', 'total' ),
				'type' => 'totaltheme_toggle',
				'description' => esc_html__( 'When enabled local scroll links will receive an active state when the local scroll section is currently in view.', 'total' ),
			],
		],
		[
			'id' => 'local_scroll_update_hash',
			'transport' => 'postMessage',
			'default' => true,
			'control' => [
				'label' => esc_html__( 'Update URL Hash', 'total' ),
				'type' => 'totaltheme_toggle',
			],
		],
		[
			'id' => 'scroll_to_easing',
			'default' => false,
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'jQuery Easing', 'total' ),
				'type' => 'totaltheme_toggle',
				'description' => esc_html__( 'When enabled the theme will load the easing.js script and use jQuery animations for local scroll links. If disabled the theme will use the native window.scrollTo browser function.', 'total' ),
			],
		],
		[
			'id' => 'local_scroll_speed',
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'Local Scroll Speed in Milliseconds', 'total' ),
				'type' => 'text',
				'input_attrs' => [
					'placeholder' => '1000',
				],
			],
			'control_display' => [
				'check' => 'scroll_to_easing',
				'value' => 'true',
			],
		],
		[
			'id' => 'local_scroll_behaviour', // @todo fix typo?
			'transport' => 'postMessage',
			'default' => 'smooth',
			'control' => [
				'label' => esc_html__( 'Local Scroll Behavior', 'total' ),
				'type' => 'select',
				'choices' => [
					'smooth' => esc_html__( 'Smooth', 'total' ),
					'instant' => esc_html__( 'Instant', 'total' ),
					'auto' => esc_html__( 'Auto', 'total' ),
				],
			],
			'control_display' => [
				'check' => 'scroll_to_easing',
				'value' => 'false',
			],
		],
	],
];
