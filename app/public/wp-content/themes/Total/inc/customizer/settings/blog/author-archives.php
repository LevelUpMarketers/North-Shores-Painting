<?php

defined( 'ABSPATH' ) || exit;

$this->sections['wpex_author_archives'] = [
	'title' => esc_html__( 'Author Archives', 'total' ),
	'panel' => 'wpex_blog',
	'settings' => [
		[
			'id' => 'author_layout',
			'control' => [
				'label' => esc_html__( 'Layout', 'total' ),
				'type' => 'select',
				'choices' => 'post_layout',
			],
		],
		[
			'id' => 'author_archive_template_id',
			'control' => [
				'label' => esc_html__( 'Dynamic Template', 'total' ),
				'type' => 'totaltheme_template_select',
				'template_type' => 'archive',
				'description' => esc_html__( 'Select a template to override the default output of your author pages.', 'total' ),
			],
		],
	],
];
