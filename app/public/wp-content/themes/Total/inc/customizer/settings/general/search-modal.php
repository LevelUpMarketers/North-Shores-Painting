<?php

defined( 'ABSPATH' ) || exit;

$this->sections['wpex_search_modal'] = [
	'title'  => esc_html__( 'Search Modal', 'total' ),
	'description' => esc_html__( 'The modal search is a global feature that can be enabled through various methods, including the preset header style\'s menu search, the Horizontal Menu element, Off-Canvas Menu element, Theme Button element, and Icon element.', 'total' ),
	'panel'  => 'wpex_general',
	'settings' => [
		[
			'id' => 'search_modal[use_ajax]',
			'default' => true,
			'control' => [
				'label' => esc_html__( 'Live Results', 'total' ),
				'type' => 'totaltheme_toggle',
				'description' => esc_html__( 'Enable to display results in the same modal window without refreshing the page. Disable to redirect to the default WordPress search results archive.', 'total' ),
			],
		],
		[
			'id' => 'search_modal[input_placeholder]',
			'control' => [
				'label' => esc_html__( 'Placeholder Text', 'total' ),
				'type' => 'text',
				'input_attrs' => [
					'placeholder' => esc_html__( 'What are you looking for?', 'total' ),
				],
			],
		],
		[
			'id' => 'search_modal[width]',
			'control' => [
				'label' => esc_html__( 'Width', 'total' ),
				'type' => 'totaltheme_length_unit',
				'placeholder' => '800',
			],
			'inline_css' => [
				'target' => '#wpex-search-modal',
				'alter' => 'width',
				'sanitize' => 'px',
			],
		],
	],
];
