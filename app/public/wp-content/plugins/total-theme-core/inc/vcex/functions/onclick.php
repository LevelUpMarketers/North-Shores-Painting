<?php

defined( 'ABSPATH' ) || exit;

/**
 * Return shortcode onclick attributes.
 *
 * @todo move to Helpers\Onclick_Attributes
 */
function vcex_get_shortcode_onclick_attributes( array $atts = [], string $shortcode_tag = '' ): array {
	$attrs = [
		'href'  => '',
		'class' => [], // always return empty array for adding classes!!
	];

	$has_lightbox = false;
	$onclick      = ! empty( $atts['onclick'] ) ? $atts['onclick'] : 'custom_link';

	switch ( $onclick ) {
		case 'home':
		case 'homepage':
			$attrs['href'] = get_home_url();
			break;
		case 'internal_link':
			if ( ! empty( $atts['onclick_internal_link'] ) ) {
				$internal_link = vcex_build_link( $atts['onclick_internal_link'] );
				if ( ! empty( $internal_link['url'] ) ) {
					$internal_link_url = vcex_parse_text( $internal_link['url'] );
					// Provide auto translations for internal links.
					if ( class_exists( 'Polylang', false ) || class_exists( 'SitePress', false ) ) {
						$post_id = url_to_postid( $internal_link_url );
						if ( $post_id ) {
							$post_id = wpex_parse_obj_id( $post_id, get_post_type( $post_id ) );
							if ( $post_id && $post_url = get_permalink( $post_id ) ) {
								if ( str_contains( $internal_link_url, '?' ) ) {
									$link_query = parse_url( $internal_link_url, PHP_URL_QUERY );
									if ( $link_query ) {
										$post_url = "{$post_url}?{$link_query}";
									}
								}
								$internal_link_url = $post_url;
							}
						}
					}
					$attrs['href'] = $internal_link_url;
				} else {
					$attrs['href'] = '#'; // @maybe this isn't a good idea?
				}
			}
			break;
		case 'post_permalink':
			$attrs['href'] = get_permalink( vcex_get_the_ID() );
			break;
		case 'current_url':
			global $wp;
			$attrs['href'] = $wp ? home_url( add_query_arg( [], $wp->request ) ) : '';
			break;
		case 'post_author':
			$attrs['href'] = get_author_posts_url( get_post_field( 'post_author', vcex_get_the_ID() ) );
			break;
		case 'go_back':
			$attrs['href'] = '#';
			$attrs['class'][] = 'wpex-go-back';
			break;
		case 'custom_field':
			if ( ! empty( $atts['onclick_custom_field'] ) ) {
				$meta_href = vcex_get_meta_value( $atts['onclick_custom_field'] );
				if ( is_array( $meta_href ) ) {
					$meta_href = $meta_href['url'] ?? '';
				}
				if ( ! $meta_href && vcex_is_template_edit_mode() ) {
					$attrs['href'] = '#';
				} elseif ( is_string( $meta_href ) ) {
					$attrs['href'] = vcex_parse_text( $meta_href );
				}
			}
			break;
		case 'callback_function':
			if ( ! empty( $atts['onclick_callback_function'] )
				&& function_exists( $atts['onclick_callback_function'] )
				&& vcex_validate_user_func( $atts['onclick_callback_function'] )
			) {
				$attrs['href'] = call_user_func( $atts['onclick_callback_function'] );
				if ( $attrs['href'] ) {
					$attrs['href'] = vcex_parse_text( $attrs['href'] );
				} elseif ( vcex_is_template_edit_mode() ) {
					$attrs['href'] = '#';
				}
			}
			break;
		case 'image':
			$image_id = $atts['onclick_image'] ?? '';
			if ( $image_id ) {
				$attrs['href'] = wp_get_attachment_image_url( $image_id, 'full', false );
			}
			break;
		case 'search_toggle':
			if ( function_exists( 'totaltheme_get_instance_of' ) ) {
				totaltheme_get_instance_of( 'Search\Modal' );
			}
			$attrs['class'][] = 'wpex-open-modal';
			$attrs['href'] = '#';
			$attrs['role'] = 'button';
			$attrs['aria-controls'] = 'wpex-search-modal';
			$attrs['aria-label'] = vcex_get_aria_label( 'search' );
			$attrs['aria-expanded'] = 'false';
			break;
		case 'dark_mode_toggle':
			if ( function_exists( 'totaltheme_call_static' ) && totaltheme_call_static( 'Dark_Mode', 'is_enabled' ) ) {
				$attrs['href'] = '#';
				$attrs['role'] = 'button';
				$attrs['aria-label'] = vcex_get_aria_label( 'dark_mode_toggle' );
				$attrs['aria-pressed'] = 'false';
				$attrs['data-wpex-toggle'] = 'theme';
			}
			break;
		case 'cart_toggle';
			if ( class_exists( 'WooCommerce', false ) ) {
				if ( class_exists( 'TotalTheme\Integration\WooCommerce\Cart\Off_Canvas', false ) ) {
					$attrs['href'] = '#';
					$attrs['role'] = 'button';
					$attrs['aria-controls'] = 'wpex-off-canvas-cart';
					$attrs['aria-label'] = vcex_get_aria_label( 'cart_open' );
					$attrs['aria-expanded'] = 'false';
					$attrs['data-wpex-toggle'] = 'off-canvas';
				} else {
					$cart_link = function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : '#';
					$attrs['href'] = $cart_link;
				}
			}
			break;
		case 'lightbox_image':
			$has_lightbox = true;
			$attrs['class'][] = 'wpex-lightbox';
			$lightbox_image = '';
			if ( ! empty( $atts['onclick_lightbox_image'] ) ) {
				$image_id = '';
				if ( is_numeric( $atts['onclick_lightbox_image'] ) ) {
					$image_id = $atts['onclick_lightbox_image'];
				} elseif ( is_array( $atts['onclick_lightbox_image'] ) ) {
					$image_id = $atts['onclick_lightbox_image']['id'] ?? null;
				}
				if ( $image_id ) {
					$lightbox_image = vcex_get_lightbox_image( $image_id );
				}
			}
			if ( $lightbox_image && is_string( $lightbox_image ) ) {
				$attrs['href'] = $lightbox_image;
			} elseif ( ! empty( $atts['onclick_url'] ) ) {
				$attrs['href'] = vcex_parse_text( $atts['onclick_url'] );
			}
			break;
		case 'lightbox_video':
			$has_lightbox = true;
			$attrs['class'][] = 'wpex-lightbox';
			if ( ! empty( $atts['onclick_url'] ) ) {
				$attrs['href'] = vcex_get_video_embed_url( vcex_parse_text( $atts['onclick_url'] ) );
			}
			break;
		case 'lightbox_post_video':
			$has_lightbox = true;
			$attrs['class'][] = 'wpex-lightbox';
			$attrs['href'] = vcex_get_post_video_oembed_url( vcex_get_the_ID() );
			break;
		case 'lightbox_gallery':
		case 'lightbox_post_gallery':
			$has_lightbox = true;
			$attrs['href'] = '#';
			$attrs['class'][] = 'wpex-lightbox-gallery';
			break;
		case 'popup':
			$has_lightbox = true;
			$attrs['class'][] = 'wpex-lightbox';
			if ( isset( $atts['onclick_url'] ) ) {
				$attrs['href'] = vcex_parse_text( $atts['onclick_url'] );
			}
			break;
		case 'local_scroll':
			if ( ! empty( $atts['onclick_url'] ) ) {
				$url = $atts['onclick_url'];
				if ( ! str_starts_with( $url, 'http' ) && ! str_starts_with( $url, 'www' ) ) {
					$url = ltrim( (string) $atts['onclick_url'], '#' );
					$url = "#{$url}";
				}
				$attrs['href'] = $url;
				$attrs['class'][] = 'local-scroll-link';
				unset( $atts['target'] );
				unset( $atts['rel'] );
			}
			break;
		// @todo - add support for modal/dialogs.
		/*case 'modal':
			if ( ! empty( $atts['onclick_url'] ) ) {
				$target_el = ltrim( (string) $atts['onclick_url'], '#' );
				$attrs['href'] = "#{$target_el}";
				$attrs['class'][] = 'wpex-open-modal';
				$attrs['aria-controls'] = $target_el;
				$attrs['aria-expanded'] = 'false';
			}
			break;
		*/
		case 'toggle_element':
			if ( ! empty( $atts['onclick_url'] ) && $target = vcex_parse_text( $atts['onclick_url'] ) ) {
				$target_el = ltrim( $target, '#' );
				$attrs['href'] = "#{$target_el}";
				$attrs['class'][] = 'wpex-toggle-element-trigger';
				$attrs['aria-controls'] = $target_el;
				if ( empty( $atts['onclick_data_attributes'] )
					|| ! str_contains( $atts['onclick_data_attributes'], 'aria-expanded' )
				) {
					$attrs['aria-expanded'] = 'false';
				}
			}
			break;
		case 'just_event_link':
			if ( $link = get_post_meta( get_the_ID(), '_just_events_link', true ) ) {
				$attrs['href'] = vcex_parse_text( $link );
			}
			break;
		case 'custom_link':
		default:
			if ( 'vcex_button' === $shortcode_tag ) {
				$attrs['href'] = ! empty( $atts['onclick_url'] ) ? vcex_parse_text( $atts['onclick_url'] ) : '#';
			} elseif ( isset( $atts['onclick_url'] ) ) {
				$attrs['href'] = vcex_parse_text( $atts['onclick_url'] );
			}
			break;
	}

	// Custom title attribute.
	if ( ! empty( $atts['onclick_title'] ) ) {
		$attrs['title'] = esc_attr( vcex_parse_text( $atts['onclick_title'] ) );
	}

	// Custom target.
	if ( ! empty( $atts['onclick_target'] ) ) {
		$attrs['target'] = esc_attr( $atts['onclick_target'] );
	}

	// Custom rel attribute.
	if ( ! empty( $atts['onclick_rel'] ) ) {
		$attrs['rel'] = esc_attr( $atts['onclick_rel'] );
	}

	// Lightbox additions.
	if ( $has_lightbox ) {

		// No target or rel needed for lightbox links.
		unset( $atts['target'] );
		unset( $atts['rel'] );

		// Enqueue lightbox scripts.
		vcex_enqueue_lightbox_scripts();

		// Get lightbox settings
		$lightbox_settings = vcex_get_shortcode_onclick_lightbox_settings( $atts, $shortcode_tag );
		if ( $lightbox_settings ) {
			foreach ( $lightbox_settings as $key => $value ) {
				$attrs["data-{$key}"] = $value;
			}
		}

	}

	// Check for custom data attributes.
	if ( ! empty( $atts['onclick_data_attributes'] ) ) {
		$custom_data = $atts['onclick_data_attributes'];
		if ( is_string( $custom_data ) ) {
			$custom_data = explode( ',', $custom_data );
		}
		if ( $custom_data && is_array( $custom_data ) ) {
			foreach ( $custom_data as $data ) {
				if ( is_string( $data ) && str_contains( $data, '|' ) ) {
					$data = explode( '|', $data );
					$data_name_safe = esc_attr( str_replace( 'data-', '', $data[0] ) );
					$attrs["data-{$data_name_safe}"] = esc_attr( do_shortcode( $data[1] ) );
				} else {
					$data_name_safe = esc_attr( str_replace( 'data-', '', $data ) );
					$attrs["data-{$data_name_safe}"] = ''; // empty data attribute
				}
			}
		}
	}

	// Download attribute.
	if ( vcex_validate_att_boolean( 'onclick_download', $atts )
		|| vcex_validate_att_boolean( 'download_attribute', $atts )
	) {
		$attrs['download'] = 'download';
	}

	// Parse href result.
	if ( ! empty( $attrs['href'] ) && is_string( $attrs['href'] ) ) {
		if ( '{{post}}' === $attrs['href'] ) {
			$attrs['href'] = esc_url( get_permalink( vcex_get_the_ID() ) );
		} else {
			$attrs['href'] = trim( $attrs['href'] );
			$sanitize = 'url';
			if ( in_array( $onclick, [ 'custom_link', 'custom_field', 'callback_function', 'internal_link' ], true )
				&& ! str_starts_with( $attrs['href'], 'http' )
				&& ! str_starts_with( $attrs['href'], '#' )
				&& ! str_starts_with( $attrs['href'], '/' )
			) {
				if ( str_starts_with( $attrs['href'], 'mailto:' ) || ( str_contains( $attrs['href'], '@' ) && is_email( $attrs['href'] ) ) ) {
					$sanitize = 'email';
				} elseif ( 0 === strlen( trim( preg_replace( '/[\s\#0-9_\-\+\/\(\)\.]/', '', $attrs['href'] ) ) ) && ! ip2long( $attrs['href'] ) ) {
					$sanitize = 'phone_number';
				}
			}
			switch ( $sanitize ) {
				case 'email':
					$attrs['href'] = esc_url( 'mailto:' . antispambot( str_replace( 'mailto:', '', $attrs['href'] ) ) );
					break;
				case 'phone_number':
					$attrs['href'] = esc_url( 'tel:' . trim( preg_replace( '/[^\d|\+]/', '', $attrs['href'] ) ) );
					break;
				case 'url':
				default:
					$attrs['href'] = esc_url( $attrs['href'] );
					break;
			}
		}

		// Set correct URL scheme for lightbox to prevent errors.
		if ( in_array( $onclick, [ 'lightbox_image', 'lightbox_video', 'popup' ], true ) ) {
			$attrs['href'] = set_url_scheme( $attrs['href'] );
		}
	}

	return (array) apply_filters( 'vcex_shortcode_onclick_attributes', $attrs, $atts, $shortcode_tag );
}

/**
 * Return shortcode lightbox settings.
 */
function vcex_get_shortcode_onclick_lightbox_settings( array $atts = [], string $shortcode_tag = '' ): array {
	$settings = [];
	$onclick  = '';

	if ( isset( $atts['onclick'] ) ) {
		$onclick = $atts['onclick'];
	}

	switch ( $onclick ) {
		case 'popup':
			$type = 'iframe';
			if ( ! empty( $atts['onclick_url'] ) ) {
				$url = vcex_parse_text( $atts['onclick_url'] );
				if ( $url && str_starts_with( $url, '#' ) ) {
					$type = 'inline';
				}
			}
			$settings['type'] = $type;
			break;
		case 'lightbox_gallery':
		case 'lightbox_post_gallery':
			if ( 'lightbox_post_gallery' === $onclick ) {
				$post_gallery_attachments = vcex_get_post_gallery_ids( vcex_get_the_ID() );
				if ( ! empty( $post_gallery_attachments ) && is_array( $post_gallery_attachments ) ) {
					$lightbox_gallery_attachments = $post_gallery_attachments;
				}
			}
			// Custom gallery should show as a backup if the post gallery is enabled but there aren't any pictures.
			// This is because of how the image element used to work pre Total 5.1.
			if ( 'lightbox_gallery' === $onclick || empty( $lightbox_gallery_attachments ) ) {
				if ( ! empty( $atts['onclick_lightbox_gallery'] ) ) {
					if ( is_string( $atts['onclick_lightbox_gallery'] ) ) {
						$lightbox_gallery_attachments = explode( ',', $atts['onclick_lightbox_gallery'] );
					} elseif ( is_array( $atts['onclick_lightbox_gallery'] ) ) {
						$lightbox_gallery_attachments = [];
						foreach ( $atts['onclick_lightbox_gallery'] as $k => $v ) {
							$image_id = $v['id'] ?? null;
							if ( $image_id && is_string( $v ) ) {
								$image_id = $v;
							}
							if ( $image_id ) {
								$lightbox_gallery_attachments[] = $image_id;
							}
						}
					}
				}
			}
			if ( ! empty( $lightbox_gallery_attachments ) && is_array( $lightbox_gallery_attachments ) ) {
				$settings['gallery'] = vcex_parse_inline_lightbox_gallery( $lightbox_gallery_attachments );
			}
			break;
	}

	// Check for custom lightbox dimensions.
	if ( ! empty( $atts['onclick_lightbox_dims'] ) && in_array( $onclick, [ 'lightbox_video', 'popup' ], true ) ) {
		$lightbox_dims = vcex_parse_lightbox_dims( $atts['onclick_lightbox_dims'], 'array' );
		if ( ! empty( $lightbox_dims['width'] ) ) {
			$settings['width'] = $lightbox_dims['width'];
		}
		if ( ! empty( $lightbox_dims['height'] ) ) {
			$settings['height'] = $lightbox_dims['height'];
		}
	}

	// Lightbox title.
	if ( ! empty( $atts['onclick_lightbox_title'] ) ) {
		$settings['title'] = esc_attr( $atts['onclick_lightbox_title'] );
	}

	// Lightbox caption.
	if ( ! empty( $atts['onclick_lightbox_caption'] ) ) {
		$settings['caption'] = str_replace( '"',"'", wp_kses_post( $atts['onclick_lightbox_caption'] ) );
	}

	return (array) apply_filters( 'vcex_shortcode_onclick_lightbox_settings', $settings, $atts, $shortcode_tag );
}
