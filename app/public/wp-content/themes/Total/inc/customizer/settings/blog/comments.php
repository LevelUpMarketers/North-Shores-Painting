<?php

defined( 'ABSPATH' ) || exit;

$this->sections['wpex_comments'] = [
	'title' => esc_html__( 'Comments', 'total' ),
	'panel' => 'wpex_blog',
	'settings' => [
		[
			'id' => 'comment_form_classic',
			'default' => true,
			'control' => [
				'label' => esc_html__( 'Classic Comment Form', 'total' ),
				'type' => 'totaltheme_toggle',
				'description' => esc_html__( 'Modifies the default WordPress comment form layout so that the comment box is after the name and email fields.', 'total' ),
			],
		],
		[
			'id' => 'bypostauthor_highlight',
			'control' => [
				'label' => esc_html__( 'Comment Author Label', 'total' ),
				'type' => 'totaltheme_toggle',
				'description' => esc_html__( 'Displays an "Author" label next to the name for any comment made by the author of the post.', 'total' ),
			],
		],
		[
			'id' => 'comment_form_title_reply',
			'control' => [
				'label' => esc_html__( 'Comment Form Heading', 'total' ),
				'type' => 'text',
				'input_attrs' => [
					'placeholder' => esc_html__( 'Leave a Reply' ),
				],
			],
		],
		[
			'id' => 'comment_fn_font_size',
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'Author Name Font Size', 'total' ),
				'type' => 'totaltheme_length_unit',
			],
			'inline_css' => [
				'target' => '#comments .comment-meta .fn',
				'alter' => 'font-size',
				'sanitize' => 'font_size',
			],
		],
		[
			'id' => 'comment_avatar_size',
			'control' => [
				'label' => esc_html__( 'Avatar Size', 'total' ),
				'type' => 'totaltheme_length_unit',
				'units' => [ 'px' ],
				'placeholder' => '50',
			],
			'inline_css' => [
				'target' => ':root',
				'alter' => '--wpex-comment-avatar-size',
				'sanitize' => 'px',
			],
		],
		[
			'id' => 'comment_avatar_border_radius',
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'Avatar Border Radius', 'total' ),
				'type' => 'select',
				'choices' => 'border_radius',
				'sanitize' => 'px',
			],
			'inline_css' => [
				'target' => '#comments .comment-author .avatar',
				'alter' => 'border-radius',
				'sanitize' => 'utl_border_radius',
			],
		],
		[
			'id' => 'comment_avatar_margin',
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'Avatar Side Margin', 'total' ),
				'type' => 'totaltheme_length_unit',
				'exclude_units' => [ '%' ],
				'placeholder' => '20',
			],
			'inline_css' => [
				'target' => ':root',
				'alter' => '--wpex-comment-avatar-margin',
				'sanitize' => 'fallback_px',
			],
		],
		[
			'id' => 'comment_spacing',
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'Spacing Between Comments', 'total' ),
				'type' => 'totaltheme_length_unit',
				'exclude_units' => [ '%' ],
				'placeholder' => '20',
				'description' => esc_html__( 'Applies a bottom padding and margin to the comment, so if you set the border width below to 0px you may want to make this value half of what it was.', 'total' ),
			],
			'inline_css' => [
				'target' => '#comments .comment-body',
				'alter' => [
					'margin-block-end',
					'padding-block-end',
				],
				'sanitize' => 'fallback_px',
			],
		],
		[
			'id' => 'comment_body_border_width',
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'Comment Border Width', 'total' ),
				'type' => 'select',
				'choices' => [
					'' => esc_html__( 'Default', 'total' ),
					'0px' => '0px',
					'1px' => '1px',
					'2px' => '2px',
					'3px' => '3px',
					'4px' => '4px',
					'5px' => '5px',
				],
			],
			'inline_css' => [
				'target' => '#comments .comment-body',
				'alter' => 'border-width',
				'sanitize' => 'px',
			],
		],
		[
			'id' => 'comment_body_border_color',
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'Comment Border Color', 'total' ),
				'type' => 'totaltheme_color',
			],
			'inline_css' => [
				'target' => '#comments .comment-body',
				'alter' => 'border-color',
			],
		],
	],
];
