<?php

/**
 * vcex_author_bio shortcode output.
 *
 * @package Total WordPress Theme
 * @subpackage Total Theme Core
 * @version 1.8
 */

defined( 'ABSPATH' ) || exit;

// Define main vars.
$style = ! empty( $atts['style'] ) ? trim( sanitize_text_field( $atts['style'] ) ) : 'default';

if ( 'default' !== $style ) {
	$breakpoint = 'md';
	$avatar_size = isset( $atts['avatar_size'] ) ? absint( $atts['avatar_size'] ) : null;
	$avatar_border_radius_class = 'wpex-rounded-full';

	if ( ! empty( $atts['avatar_spacing'] ) ) {
		$avatar_spacing = absint( $atts['avatar_spacing'] );
	}

	if ( in_array( $style, [ 'alt-3'] ) ) {
		$avatar_border_radius_class = '';
	}

	if ( ! empty( $atts['avatar_border_radius'] ) ) {
		$avatar_border_radius_class = vcex_parse_border_radius_class( $atts['avatar_border_radius'] );
	}

	$avatar_class = [
		'class' => trim( "wpex-align-middle {$avatar_border_radius_class}" ),
	];

}

// Shortcode classes.
$wrap_class = [
	'vcex-author-bio',
	'vcex-module',
];

if ( 'default' !== $style ) {
	$wrap_class[] = "vcex-author-bio--{$style}";
}

switch ( $style ) {
	case 'alt-1':
		$wrap_class[] = "wpex-flex wpex-flex-col wpex-{$breakpoint}-flex-row wpex-{$breakpoint}-items-center";
		$wrap_class[] = 'wpex-bordered';
		if ( empty( $atts['padding_all'] ) ) {
			$wrap_class[] = 'wpex-p-30';
		}
		break;
	case 'alt-2':
	case 'alt-3':
		$wrap_class[] = 'wpex-flex wpex-items-center';
		break;
	case 'alt-4':
		$wrap_class[] = "wpex-flex wpex-flex-col wpex-{$breakpoint}-flex-row";
		break;
	case 'alt-5':
		$wrap_class[] = 'wpex-flex wpex-items-center';
		break;
}

if ( ! empty( $atts['bottom_margin'] ) ) {
	$wrap_class[] = vcex_parse_margin_class( $atts['bottom_margin'], 'bottom' );
}

if ( ! empty( $atts['visibility'] ) ) {
	$wrap_class[] = vcex_parse_visibility_class( $atts['visibility'] );
}

if ( ! empty( $atts['css_animation'] ) ) {
	$wrap_class[] = vcex_get_css_animation( $atts['css_animation'] );
}

if ( 'default' !== $style ) {

	if ( ! empty( $atts['padding_all'] ) ) {
		$wrap_class[] = vcex_parse_padding_class( $atts['padding_all'] );
	}

	if ( ! empty( $atts['border_style'] ) ) {
		$wrap_class[] = vcex_parse_border_style_class( $atts['border_style'] );
	}

	if ( ! empty( $atts['border_width'] ) ) {
		$wrap_class[] = vcex_parse_border_width_class( $atts['border_width'] );
	}

	if ( ! empty( $atts['border_radius'] ) ) {
		$wrap_class[] = vcex_parse_border_radius_class( $atts['border_radius'] );
	}

	if ( ! empty( $atts['css'] ) ) {
		$wrap_class[] = vcex_vc_shortcode_custom_css_class( $atts['css'] );
	}

}

if ( ! empty( $atts['max_width'] ) ) {
	$wrap_class[] = 'wpex-max-w-100';
	$wrap_class[] = vcex_parse_align_class( ! empty( $atts['align'] ) ? $atts['align'] : 'center' );
}

if ( ! empty( $atts['el_class'] ) ) {
	$wrap_class[] = vcex_get_extra_class( $atts['el_class'] );
}

$wrap_class = vcex_parse_shortcode_classes( $wrap_class, 'vcex_author_bio', $atts );

// Start output here.
$output = '<div class="' . esc_attr( $wrap_class ) . '">';

	// Get user data for custom styles.
	if ( 'default' !== $style ) {

		$post = get_post( vcex_get_the_ID() );

		$date_format = ! empty( $atts['date_format'] ) ? $date_format : '';

		if ( function_exists( 'wpex_get_author_box_data' ) ) {

			$authordata = wpex_get_author_box_data( $post );

			$author_display = trim( esc_html( ucfirst( $authordata['author_name'] ) ) );

			$author_link = '';

			if ( isset( $atts['author_onclick'] ) ) {

				switch ( $atts['author_onclick'] ) {
					case 'author_website':
						$author_link = get_the_author_meta( 'user_url', $post->post_author );
						$author_link_title = esc_html( 'Go to Author Website', 'total-theme-core' );
						break;
					case 'author_archive':
						$author_link = get_author_posts_url( $post->post_author );
						$author_link_title = esc_html( 'Go to Author Page', 'total-theme-core' );
						break;
				}
			}

			if ( ! empty( $atts['author_onclick_title'] ) ) {
				$author_link_title = $atts['author_onclick_title'];
			}

		}

		if ( empty( $authordata ) ) {
			$style = ''; // prevent showing anything.
		}

	}

	switch ( $style ) {

		/*--------------------------------*/
		/* [ Style => Alt 1 ]
		/*--------------------------------*/
		case 'alt-1':

			if ( empty( $avatar_size ) ) {
				$avatar_size = 100;
			}

			$avatar = get_avatar( $authordata['post_author'], $avatar_size, '', '', $avatar_class );

			if ( ! isset( $avatar_spacing ) ) {
				$avatar_spacing = '30';
			}

			if ( ! empty( trim( $avatar ) ) ) {

				$output .= '<div class="vcex-author-bio__avatar wpex-mb-' . $avatar_spacing . ' wpex-' . $breakpoint .'-mb-0 wpex-' . $breakpoint .'-mr-' . $avatar_spacing . ' wpex-flex-shrink-0">';

					if ( $author_link ) {
						$output .= '<a href="' . esc_url( $author_link ) . '" title="' . $author_link_title . '">' . $avatar . '</a>';
					} else {
						$output .= $avatar;
					}

				$output .= '</div>';

			}

			$output .= '<div class="vcex-author-bio__details wpex-flex-grow">';

				if ( ! empty( $authordata['author_name'] ) ) {

					$output .= '<div class="vcex-author-bio__title wpex-font-heading wpex-text-lg">';

						if ( $author_link ) {
							$author_display = '<a href="' . esc_url( $author_link ) . '"  title="' . $author_link_title . '">' . $author_display . '</a>';
						} else {
							$author_display = '<span class="wpex-font-bold">' . $author_display . '</span>';
						}

						$output .= sprintf( esc_html__( 'By %s on %s', 'total-theme-core' ), $author_display, get_the_date( $date_format, $post->ID ) );

					$output .= '</div>';


				}

				$get_terms = vcex_get_list_post_terms();

				if ( ! empty( $get_terms ) ) {

					$output .= '<div class="vcex-author-bio__meta wpex-mt-10">' . sprintf( esc_html__( 'Posted in %s', 'total-theme-core' ), $get_terms ) . '</div>';

				}

			$output .= '</div>';

			break;

		/*--------------------------------*/
		/* [ Style => Alt 2 ]
		/*--------------------------------*/
		case 'alt-2':

			if ( empty( $avatar_size ) ) {
				$avatar_size = 50;
			}

			if ( ! isset( $avatar_spacing ) ) {
				$avatar_spacing = '20';
			}

			$avatar = get_avatar( $authordata['post_author'], $avatar_size, '', '', $avatar_class );

			if ( ! empty( trim( $avatar ) ) ) {

				$output .= '<div class="vcex-author-bio__avatar wpex-mr-' . $avatar_spacing . ' wpex-flex-shrink-0">';

					if ( $author_link ) {
						$output .= '<a href="' . esc_url( $author_link ) . '"  title="' . $author_link_title . '">' . $avatar . '</a>';
					} else {
						$output .= $avatar;
					}

				$output .= '</div>';

			}

			if ( ! empty( $authordata['author_name'] ) ) {

				$output .= '<div class="vcex-author-bio__name">';

					if ( $author_link ) {
						$output .= '<a href="' . esc_url( $author_link ) . '" class="wpex-inherit-color wpex-no-underline">' . $author_display . '</a>';
					} else {
						$output .= $author_display;
					}

				$output .= '</div>';


			}

			break;

		/*--------------------------------*/
		/* [ Style => Alt 3 ]
		/*--------------------------------*/
		case 'alt-3':

			if ( empty( $avatar_size ) ) {
				$avatar_size = 80;
			}

			if ( ! isset( $avatar_spacing ) ) {
				$avatar_spacing = '20';
			}

			$avatar = get_avatar( $authordata['post_author'], $avatar_size, '', '', $avatar_class );

			if ( ! empty( trim( $avatar ) ) ) {

				$output .= '<div class="vcex-author-bio__avatar wpex-mr-' . $avatar_spacing . ' wpex-flex-shrink-0">';

					if ( $author_link ) {
						$output .= '<a href="' . esc_url( $author_link ) . '"  title="' . $author_link_title . '">' . $avatar . '</a>';
					} else {
						$output .= $avatar;
					}

				$output .= '</div>';

			}

			if ( ! empty( $authordata['author_name'] ) ) {

				$output .= '<div class="vcex-author-bio__name wpex-heading wpex-text-lg">';

					if ( $author_link ) {
						$output .= '<a href="' . esc_url( $author_link ) . '"  title="' . $author_link_title . '" class="wpex-no-underline">' . $author_display . '</a>';
					} else {
						$output .= $author_display;
					}

				$output .= '</div>';


			}

			break;

		/*--------------------------------*/
		/* [ Style => Alt 4 ]
		/*--------------------------------*/
		case 'alt-4':

			if ( empty( $avatar_size ) ) {
				$avatar_size = 65;
			}

			if ( ! isset( $avatar_spacing ) ) {
				$avatar_spacing = '25';
			}

			$avatar = get_avatar( $authordata['post_author'], $avatar_size, '', '', $avatar_class );

			if ( ! empty( trim( $avatar ) ) ) {

				$output .= '<div class="vcex-author-bio__avatar wpex-mb-' . $avatar_spacing . ' wpex-' . $breakpoint .'-mb-0 wpex-' . $breakpoint .'-mr-' . $avatar_spacing . ' wpex-flex-shrink-0">';

					if ( $author_link ) {
						$output .= '<a href="' . esc_url( $author_link ) . '"  title="' . $author_link_title . '">' . $avatar . '</a>';
					} else {
						$output .= $avatar;
					}

				$output .= '</div>';

			}

			$output .= '<div class="vcex-author-bio__details wpex-flex-grow">';

				if ( ! empty( $authordata['author_name'] ) ) {

					$output .= '<div class="vcex-author-bio__title wpex-heading wpex-text-lg">';

						if ( $author_link ) {
							$output .= '<a href="' . esc_url( $author_link ) . '"  title="' . $author_link_title . '" class="wpex-no-underline">' . $author_display . '</a>';
						} else {
							$output .= $author_display;
						}

					$output .= '</div>';

					$description = get_the_author_meta( 'description', $post->post_author );

					if ( $description ) {

						$output .= '<div class="vcex-author-bio__description wpex-mt-10">' . do_shortcode( wp_kses_post( $description ) ) . '</div>';

					}

					if ( function_exists( 'wpex_get_user_social_links' ) ) {

						$output .= wpex_get_user_social_links( array(
							'user_id'         => $post->post_author,
							'display'         => 'icons',
							'before'          => '<div class="vcex-author-bio__social wpex-mt-10 wpex-leading-none wpex-last-mr-0">',
							'after'           => '</div>',
							'link_attributes' => array(
								'class' => 'wpex-inline-block wpex-m-5 wpex-inherit-color wpex-hover-text-accent wpex-mr-10'
							),
						) );

					}

				}

			$output .= '</div>';

			break;

		/*--------------------------------*/
		/* [ Style => Alt 5 ]
		/*--------------------------------*/
		case 'alt-5':

			if ( empty( $avatar_size ) ) {
				$avatar_size = 50;
			}

			if ( ! isset( $avatar_spacing ) ) {
				$avatar_spacing = '20';
			}

			$avatar = get_avatar( $authordata['post_author'], $avatar_size, '', '', $avatar_class );

			if ( ! empty( trim( $avatar ) ) ) {

				$output .= '<div class="vcex-author-bio__avatar wpex-mr-' . $avatar_spacing . ' wpex-flex-shrink-0">';

					if ( $author_link ) {
						$output .= '<a href="' . esc_url( $author_link ) . '"  title="' . $author_link_title . '">' . $avatar . '</a>';
					} else {
						$output .= $avatar;
					}

				$output .= '</div>';

			}

			$output .= '<div class="vcex-author-bio__details">';

				if ( ! empty( $authordata['author_name'] ) ) {

					$output .= '<div class="vcex-author-bio__name wpex-text-1 wpex-font-semibold">';

						if ( $author_link ) {
							$output .= '<a href="' . esc_url( $author_link ) . '" class="wpex-inherit-color wpex-no-underline">' . $author_display . '</a>';
						} else {
							$output .= $author_display;
						}

					$output .= '</div>';

				}

				$output .= '<div class="vcex-author-bio__meta wpex-flex wpex-flex-wrap wpex-gap-5 wpex-text-sm">';

					$output .= get_the_date( $date_format, $post->ID );

					if ( $post && $post->post_content ) {

						$output .= '<span>&bull;</span>';

						$words = str_word_count( strip_tags( $post->post_content ) );
						$wpm = 200; // estimated words per minute.

						$minutes = ceil( $words / $wpm );

						if ( $minutes > 1 ) {
							$output .= sprintf( esc_html__( '%s minute read', 'total-theme-core' ), $minutes );
						} else {
							$seconds = floor( $words % $wpm / ( $wpm / 60 ) );
							$output .= sprintf( esc_html__( '%s second read', 'total-theme-core' ), $seconds );
						}

					}

				$output .= '</div>';

			$output .= '</div>';

			break;

		/*--------------------------------*/
		/* [ Style => Default ]
		/*--------------------------------*/
		case 'default':

			if ( function_exists( 'wpex_get_template_part' ) ) {
				ob_start();
				wpex_get_template_part( 'author_bio' );
				$author_bio = ob_get_clean();
				if ( $author_bio && is_string( $author_bio ) ) {
					if ( ! empty( $atts['bottom_margin'] ) ) {
						$author_bio = str_replace( ' wpex-mb-40', '', $author_bio );
					}
					$output .= $author_bio;
				}
			}

			break;

	}

$output .= '</div>';

// @codingStandardsIgnoreLine
echo $output;
