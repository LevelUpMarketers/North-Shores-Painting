<?php

defined( 'ABSPATH' ) || exit;

$social_share_items = (array) wpex_social_share_items();

if ( $social_share_items ) {

	$social_share_choices = [];

	foreach ( $social_share_items as $k => $v ) {
		$social_share_choices[ $k ] = $v['site'];
	}

	$settings = [
		[
			'id' => 'social_share_shortcode',
			'transport' => 'partialRefresh',
			'control' => [
				'label' => esc_html__( 'Alternative Shortcode', 'total' ),
				'type' => 'text',
				'description' => esc_html__( 'Override the theme default social share with your custom social sharing shortcode.', 'total' ),
			],
		],
		[
			'id'  => 'social_share_sites',
			'transport' => 'partialRefresh',
			'default' => 'twitter,facebook,linkedin,email',
			'control' => [
				'label'  => esc_html__( 'Sites', 'total' ),
				'type' => 'totaltheme_blocks',
				'choices' => $social_share_choices,
			],
		],
		[
			'id' => 'social_share_heading',
			'transport' => 'partialRefresh',
			'default' => esc_html__( 'Share This', 'total' ),
			'control' => [
				'label' => esc_html__( 'Horizontal Position Heading', 'total' ),
				'type'  => 'text',
				'description' => esc_html__( 'Leave blank to disable.', 'total' ),
			],
		],
		[
			'id' => 'social_share_heading_tag',
			'default' => 'h4',
			'transport' => 'partialRefresh',
			'control' => [
				'label' => esc_html__( 'Heading Tag', 'total' ),
				'type' => 'select',
				'choices' => [
					'div' => 'div',
					'h2' => 'h2',
					'h3' => 'h3',
					'h4' => 'h4',
					'h5' => 'h5',
					'h6' => 'h6',
				],
			],
			'control_display' => [
				'check' => 'social_share_heading',
				'value' => 'not_empty',
			],
		],
		[
			'id' => 'social_share_twitter_handle',
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'Twitter Handle', 'total' ),
				'type' => 'text',
			],
		],
		[
			'id' => 'social_share_position',
			'transport' => 'partialRefresh',
			'default' => 'horizontal',
			'control' => [
				'label' => esc_html__( 'Position', 'total' ),
				'type' => 'select',
				'choices' => [
					'horizontal' => esc_html__( 'Horizontal', 'total' ),
					'vertical' => esc_html__( 'Vertical (Fixed)', 'total' ),
				],
			],
			'control_display' => [
				'check' => 'social_share_style',
				'value' => [ 'flat', 'minimal', 'three-d', 'rounded', 'custom' ],
			],
		],
		[
			'id' => 'social_share_align',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'select',
				'label' => esc_html__( 'Alignment', 'total' ),
				'choices' => [
					'' => esc_html__( 'Default', 'total' ),
					'left' => esc_html__( 'Left', 'total' ),
					'center' => esc_html__( 'Center', 'total' ),
					'right' => esc_html__( 'Right', 'total' ),
				],
			],
			'control_display' => [
				'check' => 'social_share_position',
				'value' => 'horizontal',
			],
		],
		[
			'id' => 'social_share_style',
			'transport' => 'partialRefresh',
			'default' => 'flat',
			'control' => [
				'label' => esc_html__( 'Style', 'total' ),
				'type'  => 'select',
				'choices' => [
					'flat' => esc_html__( 'Flat', 'total' ),
					'minimal' => esc_html__( 'Minimal', 'total' ),
					'three-d' => esc_html__( '3D', 'total' ),
					'rounded' => esc_html__( 'Rounded', 'total' ),
					'mag' => esc_html__( 'Magazine', 'total' ),
					'custom' => esc_html__( 'Custom', 'total' ),
				],
			],
		],
		[
			'id' => 'social_share_link_color',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Link Color', 'total' ),
			],
			'control_display' => [
				'check' => 'social_share_style',
				'value' => 'custom',
			],
			'inline_css' => [
				'target' => '.style-custom .wpex-social-share__link',
				'alter' => 'color',
			],
		],
		[
			'id' => 'social_share_link_bg_color',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Background', 'total' ),
			],
			'control_display' => [
				'check' => 'social_share_style',
				'value' => 'custom',
			],
			'inline_css' => [
				'target' => '.style-custom  .wpex-social-share__link',
				'alter' => 'background-color',
			],
		],
		[
			'id' => 'social_share_link_bg_color_hover',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Background: Hover', 'total' ),
			],
			'control_display' => [
				'check' => 'social_share_style',
				'value' => 'custom',
			],
			'inline_css' => [
				'target' => '.style-custom  .wpex-social-share__link:hover',
				'alter' => 'background-color',
			],
		],
		[
			'id' => 'social_share_link_border_radius',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'select',
				'label' => esc_html__( 'Border Radius', 'total' ),
				'choices' => 'border_radius',
			],
			'control_display' => [
				'check' => 'social_share_style',
				'value' => 'custom',
			],
		],
		[
			'id' => 'social_share_label',
			'transport' => 'partialRefresh',
			'default' => true,
			'control' => [
				'label' => esc_html__( 'Labels', 'total' ),
				'type' => 'totaltheme_toggle',
			],
			'control_display' => [
				'check' => 'social_share_style',
				'value' => [ 'flat', 'minimal', 'three-d', 'rounded', 'custom' ],
			],
		],
		[
			'id' => 'social_share_stretch_items',
			'transport' => 'partialRefresh',
			'control' => [
				'label' => esc_html__( 'Stretch Links', 'total' ),
				'type' => 'totaltheme_toggle',
				'description' => esc_html__( 'Will stretch the links to fill up the space for the horizontal style.', 'total' ),
			],
		],
		[
			'id' => 'social_share_link_dims',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_length_unit',
				'units' => [ 'px', 'em', 'rem' ],
				'label' => esc_html__( 'Dimensions', 'total' ),
				'description' => esc_html__( 'This field is used for the height and width of each social link when displaying the icon only without labels.', 'total' ),
			],
			'inline_css' => [
				'target' => ':root',
				'alter' => '--wpex-social-share-link-sq-dims',
				'sanitize' => 'fallback_px',
			],
		],
		[
			'id' => 'social_share_font_size',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_length_unit',
				'label' => esc_html__( 'Font Size', 'total' ),
			],
			'inline_css' => [
				'target' => '.wpex-social-share__link',
				'alter' => 'font-size',
			],
		],
		[
			'id' => 'social_share_labels_heading',
			'control' => [
				'type' => 'totaltheme_heading',
				'label' => esc_html__( 'Labels', 'total' ),
			],
		],
	];

	foreach ( $social_share_items as $k => $v ) {
		$settings[] = [
			'id' => "social_share_{$k}_label",
			'transport' => 'refresh',
			'control' => [
				'type' => 'text',
				'label' => sprintf( esc_html_x( 'Label for %s', 'Customizer Social Share Label Settings', 'total' ), $v['site'] ),
			],
		];
	}

	$this->sections['wpex_social_sharing'] = [
		'title' => esc_html__( 'Social Share Buttons', 'total' ),
		'panel' => 'wpex_general',
		'settings' => $settings,
	];

}
