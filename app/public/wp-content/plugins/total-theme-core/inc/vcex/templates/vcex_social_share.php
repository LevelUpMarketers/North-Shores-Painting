<?php

/**
 * vcex_social_share shortcode.
 *
 * @package Total WordPress Theme
 * @subpackage Total Theme Core
 * @version 2.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'wpex_social_share_list' )
	|| ! function_exists( 'wpex_social_share_data' )
	|| ! function_exists( 'wpex_social_share_items' )
	|| ! function_exists( 'wpex_get_translated_theme_mod' )
) {
	return;
}

if ( ! empty( $atts['sites'] ) ) {
	$sites = (array) vcex_vc_param_group_parse_atts( $atts['sites'] );
}

if ( empty( $sites ) || ! is_array( $sites ) ) {
	return;
}

$sites_array = [];
foreach ( $sites as $k => $v ) {
	if ( is_array( $v ) && isset( $v['site'] ) ) {
		$sites_array[] = $v['site'];
	}
}

if ( in_array( 'twitter', $sites_array, true ) && in_array( 'x-twitter', $sites_array, true ) ) {
	$twitter_key = array_search( 'twitter', $sites_array );
	if ( false !== $twitter_key ) {
		unset( $sites_array[ $twitter_key ] );
	}
}

$is_custom = vcex_validate_att_boolean( 'is_custom', $atts );
$use_modal = vcex_validate_att_boolean( 'modal', $atts );

$wrap_class = [
	'vcex-social-share',
	'vcex-module',
];

if ( ! empty( $atts['bottom_margin'] ) ) {
	$wrap_class[] = vcex_parse_margin_class( $atts['bottom_margin'], 'bottom' );
}

if ( ! empty( $atts['css_animation'] ) ) {
	$wrap_class[] = vcex_get_css_animation( $atts['css_animation'] );
}

if ( ! empty( $atts['visibility'] ) ) {
	$wrap_class[] = vcex_parse_visibility_class( $atts['visibility'] );
}

if ( ! empty( $atts['el_class'] ) ) {
	$wrap_class[] = vcex_get_extra_class( $atts['el_class'] );
}

$wrap_class = vcex_parse_shortcode_classes( $wrap_class, 'vcex_social_share', $atts );

// Modal Popup.
if ( $use_modal ) {
	$modal_id = uniqid( 'wpex-modal-' );
	$modal_button_type = ! empty( $atts['modal_button_type'] ) ? esc_html( $atts['modal_button_type'] ) : 'icon_text';
	$modal_button_text = ! empty( $atts['modal_button_text'] ) ? esc_html( $atts['modal_button_text'] ) : esc_html__( 'Share', 'total-theme-core' );

	if ( 'icon' === $modal_button_type ) {
		$modal_button_html = '<span class="screen-reader-text">' . $modal_button_text . '<span>';
	} else {
		$modal_button_html = '<span>' . $modal_button_text . '<span>';
	}

	if ( 'icon' === $modal_button_type || 'icon_text' === $modal_button_type ) {
		if ( isset( $atts['modal_button_svg'] ) && 'arrow' === $atts['modal_button_svg'] ) {
			$modal_button_icon_svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="currentColor"><path d="M307 34.8c-11.5 5.1-19 16.6-19 29.2v64H176C78.8 128 0 206.8 0 304C0 417.3 81.5 467.9 100.2 478.1c2.5 1.4 5.3 1.9 8.1 1.9c10.9 0 19.7-8.9 19.7-19.7c0-7.5-4.3-14.4-9.8-19.5C108.8 431.9 96 414.4 96 384c0-53 43-96 96-96h96v64c0 12.6 7.4 24.1 19 29.2s25 3 34.4-5.4l160-144c6.7-6.1 10.6-14.7 10.6-23.8s-3.8-17.7-10.6-23.8l-160-144c-9.4-8.5-22.9-10.6-34.4-5.4z"/></svg>';
		} else {
			$modal_button_icon_svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M18 16.08c-.76 0-1.44.3-1.96.77L8.91 12.7c.05-.23.09-.46.09-.7s-.04-.47-.09-.7l7.05-4.11c.54.5 1.25.81 2.04.81 1.66 0 3-1.34 3-3s-1.34-3-3-3-3 1.34-3 3c0 .24.04.47.09.7L8.04 9.81C7.5 9.31 6.79 9 6 9c-1.66 0-3 1.34-3 3s1.34 3 3 3c.79 0 1.5-.31 2.04-.81l7.12 4.16c-.05.21-.08.43-.08.65 0 1.61 1.31 2.92 2.92 2.92s2.92-1.31 2.92-2.92c0-1.61-1.31-2.92-2.92-2.92zM18 4c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zM6 13c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm12 7.02c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1z"/></svg>';
		}
		// @todo update to use vcex_get_theme_icon_html()
		$modal_button_icon = '<span class="vcex-social-share-modal-trigger__icon wpex-icon">' . $modal_button_icon_svg . '</span>';
		$modal_button_html = $modal_button_icon . $modal_button_html;
	}

	// Modal Button.
	$modal_button_class = 'vcex-social-share-modal-trigger wpex-inline-flex wpex-items-center wpex-gap-5 wpex-open-modal';
	if ( vcex_has_classic_styles() ) {
		$modal_button_class .= ' wpex-text-md';
	}
	if ( isset( $atts['modal_button_style'] ) && 'theme' === $atts['modal_button_style'] ) {
		$modal_button_class .= ' theme-button';
	} else {
		$modal_button_class .= ' wpex-unstyled-button';
	}
	$modal_button_icon_placement = $atts['modal_button_icon_placement'] ?? 'right';
	if ( 'icon_text' === $modal_button_type && 'left' !== $modal_button_icon_placement ) {
		$modal_button_class .= ' wpex-flex-row-reverse';
	}
	$modal_open_html = '<button type="button" class="' . $modal_button_class . '" aria-controls="' . esc_attr( $modal_id ) . '" aria-expanded="false">' . $modal_button_html . '</button>';
	
	// Open dialog element.
	$modal_max_width = ! empty( $atts['modal_max_width'] ) ? $atts['modal_max_width'] : '500px';

	if ( is_numeric( $modal_max_width ) ) {
		$modal_max_width = "{$modal_max_width}px";
	}

	$modal_open_html .= '<dialog id="' . esc_attr( $modal_id ) . '" class="vcex-social-share-modal wpex-modal wpex-p-20 wpex-shadow-xl" style="max-width:' . esc_attr( $modal_max_width ) . '"><div class="vcex-social-share-modal__inner wpex-modal__inner" tabindex="0">';

	// Dialog heading and close icon.
	$modal_open_html .= '<div class="vcex-social-share-modal__header wpex-flex wpex-gap-20 wpex-items-start wpex-mb-25">';
		$modal_title =  ! empty( $atts['modal_title'] ) ? $atts['modal_title'] : esc_html__( 'Share', 'total-theme-core' );
		$modal_open_html .= '<div class="vcex-social-share-modal__title wpex-heading wpex-text-2xl">' . esc_html( $modal_title ) . '</div>';
		$modal_open_html .= '<button type="button" class="vcex-social-share-modal__close wpex-close-modal wpex-unstyled-button wpex-flex wpex-items-center wpex-text-2 wpex-hover-text-1 wpex-ml-auto"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" height="25" width="25"><path d="M0 0h24v24H0V0z" fill="none"></path><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12 19 6.41z"></path></svg></button>';
	$modal_open_html .= '</div>';

	// Close dialog element.
	$modal_close_html = '</div></dialog>';
}

// Custom Design.
if ( $is_custom ) {

	wp_enqueue_script( 'wpex-social-share' );

	$expand_buttons = vcex_validate_att_boolean( 'expand', $atts );
	$social_items = wpex_social_share_items();
	$has_labels = vcex_validate_att_boolean( 'has_labels', $atts );

	if ( ! empty( $atts['button_border_radius'] ) ) {
		$border_radius_class = vcex_parse_border_radius_class( $atts['button_border_radius'] );
	} else {
		$border_radius_class = $has_labels ? 'wpex-rounded-sm' : 'wpex-rounded-full';
	}

	$buttons_class = [
		'vcex-social-share__buttons',
	];

	$buttons_class[] = 'wpex-flex';
	if ( $expand_buttons ) {
		$buttons_class[] = 'wpex-overflow-x-auto';
		$buttons_class[] = 'wpex-hide-scrollbar';
	} else {
		$buttons_class[] = 'wpex-flex-wrap';
		if ( ! empty( $atts['align'] ) && 'none' !== $atts['align'] ) {
			$buttons_class[] = vcex_parse_justify_content_class( $atts['align'] );
		}
	}
	$buttons_class[] = 'wpex-gap-5';

	?>

	<div class="<?php echo esc_attr( $wrap_class ); ?>"<?php echo vcex_get_unique_id( $atts['unique_id'] ?? null ); ?> <?php wpex_social_share_data( vcex_get_the_ID(), $sites_array ); ?>><?php

		if ( $use_modal ) {
			// @codingStandardsIgnoreLine
			echo $modal_open_html;
		}

		echo '<div class="' . esc_attr( implode( ' ', $buttons_class ) ) . '">';

		// Loop through and display social links.
		foreach ( $sites_array as $social_site ) {
			if ( ! array_key_exists( $social_site, $social_items ) ) {
				continue;
			}
			if ( isset( $social_items[ $social_site ]['icon_class'] ) ) {
				$icon = vcex_get_theme_icon_html( $social_items[ $social_site ]['icon_class'], 'vcex-social-share__icon' );
			} else {
				$icon = vcex_get_theme_icon_html( $social_items[ $social_site ]['icon'] ?? $social_site, 'vcex-social-share__icon' );
			}
			if ( $icon ) {
				$button_class = [
					'vcex-social-share__button',
					"vcex-social-share__button--{$social_site}",
					$border_radius_class,
					'wpex-' . sanitize_html_class( $social_site ),
					'wpex-social-btn',
					'wpex-social-bg',
					'wpex-gap-10',
				];

				if ( $has_labels ) {
					$button_class[] = 'wpex-w-auto';
				//	$button_class[] = 'wpex-h-auto'; // deprecated when switching to flex styles - why?
					$button_class[] = 'wpex-py-5';
					$button_class[] = 'wpex-px-15';
				}

				if ( $expand_buttons ) {
					$button_class[] = 'wpex-flex-grow';
				}

				if ( ! empty( $atts['button_font_weight'] ) ) {
					$button_class[] = vcex_parse_font_weight_class( $atts['button_font_weight'] );
				}

				if ( ! empty( $atts['button_bg_hover'] ) || ! empty( $atts['button_color_hover'] ) ) {
					$button_class[] = 'wpex-hover-opacity-100';
				}

				$button_aria_label = ! empty( $social_items[ $social_site ]['reader_text'] ) ? $social_items[ $social_site ]['reader_text'] : $social_site;

				echo '<button class="' . esc_attr( implode( ' ', array_filter( $button_class ) ) ) . '" aria-label="' . esc_attr( $button_aria_label ) . '">' . $icon;
				if ( $has_labels ) {
					$label = wpex_get_translated_theme_mod( "social_share_{$social_site}_label" );
					if ( ! $label ) {
						$label = $social_items[ $social_site ]['label'] ?? $social_site;
					}
					if ( $label ) {
						$label_class = 'vcex-social-share__label';
						if ( ! empty( $atts['label_bk'] ) ) {
							$label_class .= '  wpex-hidden wpex-' . sanitize_html_class( $atts['label_bk'] ) .'-inline';
						}
						echo '<span class="' . esc_attr( $label_class ) . '">' . esc_html( $label ) . '</span>';
					}
				}
				echo '</button>';
			}
		}

		echo '</div>'; // close vcex-social-share__buttons

		if ( $use_modal ) {
			// @codingStandardsIgnoreLine
			echo $modal_close_html;
		}

	?></div>

<?php }

// Default social share design.
else {

	// Social share function arguments.
	$args = [
		'position' => 'horizontal',
		'instance' => 'vcex_social_share',
	];

	if ( ! empty( $atts['style'] ) ) {
		$args['style'] = $atts['style'];
	}

	if ( ! empty( $atts['align'] ) ) {
		$args['align'] = $atts['align'];
	}

	?>
	<div class="<?php echo esc_attr( $wrap_class ); ?>"<?php echo vcex_get_unique_id( $atts['unique_id'] ?? null ); ?>>
		<?php if ( $use_modal ) {
			// @codingStandardsIgnoreLine
			echo $modal_open_html;
		} ?>
		<div <?php wpex_social_share_class( $args ); ?> <?php wpex_social_share_data( vcex_get_the_ID(), $sites_array ); ?>>
			<?php wpex_social_share_list( $args, $sites_array ); ?>
		</div>
		<?php if ( $use_modal ) {
			// @codingStandardsIgnoreLine
			echo $modal_close_html;
		} ?>
	</div>
<?php }
