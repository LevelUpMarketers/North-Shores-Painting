<?php

defined( 'ABSPATH' ) || exit;

$allowed_html = [
	'a' => [
		'href'   => [],
		'rel'    => [],
		'target' => [],
	],
];

$this->sections['wpex_footer_widgets'] = [
	'settings' => [
		[
			'id' => 'footer_builder_notice',
			'control' => [
				'description' => wp_kses( sprintf(
					__( 'Your site is using the <a href="%s" target="_blank" rel="noopener noreferrer">Footer Builder &#8599;</a>', 'total' ),
					esc_url( admin_url( 'admin.php?page=wpex-panel-footer-builder' )
				) ), $allowed_html ),
				'type' => 'totaltheme_notice',
			],
		]
	],
];
