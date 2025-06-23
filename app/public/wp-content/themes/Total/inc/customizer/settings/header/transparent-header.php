<?php

defined( 'ABSPATH' ) || exit;

$fields = [];

$fields[] = [
	'id' => 'overlay_header',
	'transport' => 'refresh',
	'control' => [
		'label' => esc_html__( 'Transparent Header', 'total' ),
		'type' => 'totaltheme_toggle',
		'description' => esc_html__( 'When enabled your header will be placed over your site content. Note: This functionality may not work well with all header styles so you may want to check your selected header style if things don\'t look quite right.', 'total' ),
	],
];

$fields[] = [
	'id' => 'overlay_header_breakpoint',
	'control' => [
		'label' => esc_html__( 'Breakpoint', 'total' ),
		'type' => 'totaltheme_length_unit',
		'units' => [ 'px' ],
		'description' => esc_html__( 'Enter a custom viewport width in pixels for when the header will become transparent. By default the transparent header will take affect at all screen sizes.', 'total' ),
	],
];

$fields[] = [
	'id' => 'overlay_header_mobile_first',
	'control' => [
		'label' => esc_html__( 'Mobile First?', 'total' ),
		'type' => 'totaltheme_toggle',
		'description' => esc_html__( 'Enable this option if you want the transparent header to display on small screens and then disable at the breakpoint.', 'total' ),
	],
	'control_display' => [
		'check' => 'overlay_header_breakpoint',
		'value' => 'not_empty',
	],
];

if ( ! isset( $header_is_custom ) || ! $header_is_custom ) {
	$fields[] = [
		'id' => 'overlay_header_style',
		'transport' => 'refresh',
		'control' => [
			'label' => esc_html__( 'Style', 'total' ),
			'type' => 'select',
			'choices' => 'TotalTheme\Header\Overlay::style_choices',
			'description' => esc_html__( 'By default the overlay header makes your menu items white and excludes certain customizer options to prevent design issues. However all the default header and menu settings will be used when your header becomes fixed/sticky. If you wish to include all customizer modifications made to the header and menu for the Overlay/Transparent header simply select the "Core Styles" option.', 'total' ),
		],
	];
}

$fields[] = [
	'id' => 'overlay_header_template',
	'transport' => 'refresh',
	'control' => [
		'label' => esc_html__( 'Background Template Part', 'total' ),
		'type' => 'totaltheme_template_select',
		'template_type' => 'part',
		'description' => esc_html__( 'If you wish to display a Dynamic Template "Part" beneath your header you can select it here.', 'total' ),
	],
];

$fields[] = [
	'id' => 'overlay_header_condition',
	'sanitize_callback' => 'sanitize_text_field', // stops issues with WP storing '&amp;' instead of &.
	'control' => [
		'type' => 'textarea',
		'label' => esc_html__( 'Conditional Logic', 'total' ),
		'description' => sprintf( esc_html__( 'This field allows you to use %sconditional tags%s to limit the functionality to specific areas of the site via a query string. For example to limit by posts and pages you can use "is_page&is_single" or "is_singular=post,page". Separate conditions with an ampersand and use a comma seperated string for arrays.', 'total' ), '<a href="https://codex.wordpress.org/Conditional_Tags" target="_blank" rel="noopener noreferrer">', '</a>' ) . ' ' . sprintf( esc_html__( 'You can create and use your own conditional functions but they must be %swhitelisted%s', 'total' ), '<a href="https://totalwptheme.com/docs/snippets/conditional-logic-whitelist/" target="_blank" rel="noopener noreferrer">', '</a>' ),
	],
	'control_display' => [
		'check' => 'overlay_header',
		'value' => 'true',
	],
];

$fields[] = [
	'id' => 'overlay_header_logo',
	'sanitize_callback' => 'absint',
	'control' => [
		'label' => esc_html__( 'Custom Logo', 'total' ),
		'type' => 'media',
		'mime_type' => 'image',
		'description' => esc_html__( 'Used when conditionally displaying the Overlay Header either via the field above or via the Theme Settings post metabox.', 'total' ),
	],
];

$fields[] = [
	'id' => 'overlay_header_logo_retina',
	'sanitize_callback' => 'absint',
	'control' => [
		'label' => esc_html__( 'Custom Logo Retina', 'total' ),
		'type' => 'media',
		'mime_type' => 'image',
	],
	'control_display' => [
		'check' => 'overlay_header_logo',
		'value' => 'not_empty',
	],
];

$this->sections['wpex_header_overlay'] = [
	'title'    => esc_html__( 'Transparent Header', 'total' ),
	'panel'    => 'wpex_header',
	'settings' => $fields,
];

unset( $fields );
