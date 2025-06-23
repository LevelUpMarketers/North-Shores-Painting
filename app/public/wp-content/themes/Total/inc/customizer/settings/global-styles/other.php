<?php
defined( 'ABSPATH' ) || exit;

$this->sections['wpex_global_styles_other'] = [
	'title' => esc_html__( 'Other', 'total' ),
	'panel' => 'wpex_global_styles',
	'settings' => [
		[
			'id' => 'p_margin_bottom',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_length_unit',
				'label' => esc_html__( 'Paragraph Bottom Margin', 'total' ),
				'description' => esc_html__( 'Used to alter the default margin applied to <p> tags.', 'total' ),
			],
			'inline_css' => [
				'target' => 'p',
				'alter' => '--wpex-el-margin-bottom',
			],
		],
	],
];
