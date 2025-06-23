<?php

defined( 'ABSPATH' ) || exit;

/**
 * Check if a given post has a video.
 */
function wpex_has_post_video( $post_id = null ) {
	return (bool) wpex_get_post_video( $post_id );
}

/**
 * Return video embed url.
 */
function wpex_get_video_embed_url( string $url = '' ): string {
	if ( ! $url || ! is_string( $url ) ) {
		return '';
	}

	// YouTube links
	if ( str_contains( $url, 'youtu' ) ) {
		if ( ! str_contains( $url, '/embed' ) ) {
			if ( str_contains(  $url, 'shorts' ) ) {
				$url = strtok( $url, '?' );
				$youtube_id = basename( parse_url( $url, PHP_URL_PATH ) );
			} elseif ( str_contains(  $url, 'playlist?list=' ) ) {
				$url = str_replace( 'playlist?list=', 'embed/videoseries?list=', $url );
			} else {
				$url = str_replace( 'youtu.be/', 'youtube.com/watch?v=', $url );
				$url_string = parse_url( $url, PHP_URL_QUERY );
				parse_str( $url_string, $args );
				$youtube_id = $args['v'] ?? '';
			}
			if ( ! empty( $youtube_id ) ) {
				$url = "youtube.com/embed/{$youtube_id}";
			}
		}
	}

	// Vimeo links.
	elseif ( str_contains( $url, 'vimeo' ) ) {
		if ( ! str_contains( $url, 'player.vimeo' ) ) {
			$video_id = (int) substr( parse_url( $url, PHP_URL_PATH ), 1 );
			if ( $video_id ) {
				$url = "player.vimeo.com/video/{$video_id}";
			}
		}
	}

	// Escape URL and set to correct URL scheme.
	if ( $url ) {
		$url = set_url_scheme( esc_url( $url ) );
	}

	// Add parameters.
	$params = (array) apply_filters( 'wpex_get_video_embed_url_params', [], $url );

	// Add params.
	if ( $params ) {
		$params_list = [];

		// Loop through and check vendors.
		foreach ( $params as $vendor => $params ) {

			// YouTube fixes.
			$vendor = ( 'youtube' === $vendor ) ? 'yout' : $vendor;

			// Check video url for vendor (youtube/vimeo/etc).
			if ( str_contains( $url, $vendor ) ) {
				foreach ( $params as $key => $val ) {
					$params_list[ sanitize_text_field( $key ) ] = esc_attr( $val );
				}
			}

		}

		if ( $params_list ) {
			$url = add_query_arg( $params_list, $url );
		}

	}

	return $url;
}

/**
 * Returns post video oEmbed url.
 */
function wpex_get_post_video_oembed_url( $post_id = '' ) {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}
	if ( $meta = get_post_meta( $post_id, 'wpex_post_video', true ) ) {
		$video = $meta;
	} elseif ( $meta = get_post_meta( $post_id, 'wpex_post_oembed', true ) ) {
		$video = $meta;
	} else {
		$video = '';
	}
	return (string) apply_filters( 'wpex_get_post_video_oembed_url', $video );
}

/**
 * Echo post video.
 */
function wpex_post_video( $post_id = '' ) {
	echo wpex_get_post_video( $post_id );
}

/**
 * Returns post video.
 *
 * @todo update to return an array with the video and type return array( $video, 'embed' )
 */
function wpex_get_post_video( $post_id = '' ) {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	$video = '';

	// Embed.
	if ( $embed = get_post_meta( $post_id, 'wpex_post_video_embed', true ) ) {
		$video = $embed;
	}

	// Check for self-hosted first.
	if ( ! $video ) {
		$self_hosted = get_post_meta( $post_id, 'wpex_post_self_hosted_media', true );
		if ( is_numeric( $self_hosted ) ) {
			if ( wp_attachment_is( 'video', $self_hosted ) ) {
				$video = $self_hosted;
			}
		} else {
			$video = $self_hosted;
		}
	}

	// Check for wpex_post_video custom field.
	if ( ! $video ) {
		$video = get_post_meta( $post_id, 'wpex_post_video', true );
	}

	// Check for post oembed.
	if ( ! $video ) {
		$video = get_post_meta( $post_id, 'wpex_post_oembed', true );
	}

	// Check old redux custom field last.
	if ( ! $video ) {
		$self_hosted = get_post_meta( $post_id, 'wpex_post_self_hosted_shortcode_redux', true );
		if ( is_numeric( $self_hosted ) ) {
			if ( wp_attachment_is( 'video', $self_hosted ) ) {
				$video = $self_hosted;
			}
		} else {
			$video = $self_hosted;
		}
	}

	return (string) apply_filters( 'wpex_get_post_video', $video );
}

/**
 * Returns post video type.
 */
function wpex_get_post_video_type( $video = '' ) {
	if ( is_string( $video ) && false !== strpos( $video, '<iframe' ) ) {
		return 'iframe';
	} elseif ( $video === get_post_meta( get_the_ID(), 'wpex_post_self_hosted_media', true ) ) {
		return 'self_hosted';
	} elseif ( $video === get_post_meta( get_the_ID(), 'wpex_post_self_hosted_shortcode_redux', true ) ) {
		return 'self_hosted';
	} else {
		return 'oembed'; // hopefully, in reality it could be anything...
	}
}

/**
 * Echo post video HTML.
 */
function wpex_post_video_html( $video = '' ) {
	echo wpex_get_post_video_html( $video );
}

/**
 * Returns post video HTML.
 */
function wpex_get_post_video_html( $video = '' ) {
	if ( ! $video ) {
		$video = wpex_get_post_video();
	}

	if ( empty( $video ) ) {
		return false;
	}

	$html = '';

	$video_type = wpex_get_post_video_type( $video );

	switch ( $video_type ) {
		case 'iframe':
			$iframe_video = wpex_sanitize_data( $video, 'iframe' );
			if ( $iframe_video ) {
				$add_responsive_wrap = ( str_contains( $video, 'youtu' ) || str_contains( $video, 'vimeo' ) );
				$add_responsive_wrap = (bool) apply_filters( 'wpex_responsive_video_wrap', $add_responsive_wrap, $video ); // @todo rename this filter.
				if ( $add_responsive_wrap ) {
					$html = '<div class="wpex-responsive-media">' . $iframe_video . '</div>';
				} else {
					$html = $iframe_video;
				}
			}
			break;
		case 'self_hosted':
			$video = is_numeric( $video ) ? wp_get_attachment_url( $video ) : $video;
			if ( filter_var( esc_url( $video ), FILTER_VALIDATE_URL ) ) {
				/*if ( function_exists( 'wp_video_shortcode' ) ) {
					$html = wp_video_shortcode( array( 'src' => $video, 'width' => '9999' ) );
				}*/
				// Switched to basic HTML in 5.8.1
				// @todo add ability to set the featured image as the video poster.
				$html = '<video class="wpex-w-100 wpex-align-middle" controls src="' . esc_url( $video ) . '"></video>';
			} else {
				$html = do_shortcode( sanitize_text_field( $video ) );
			}
			break;
		default:
			$html = wpex_video_oembed( $video );
			break;
	}

	return (string) apply_filters( 'wpex_post_video_html', $html, $video );

}

/**
 * Generate custom oEmbed output.
 *
 * @note: Used by Total Theme Core.
 */
function wpex_video_oembed( $video = '', $classes = '', $params = array() ) {
	if ( ! $video ) {
		return;
	}

	// Define output.
	$output = '';

	// Sanitize URL.
	$video_escaped = esc_url( $video );

	// If escaped video is empty then perhaps the $video is not an oembed URL, maybe
	// it's a shortcode, lets try and parse it.
	if ( empty( $video_escaped ) && ! empty( $video ) && is_string( $video ) ) {
		return do_shortcode( sanitize_text_field( $video ) );
	}

	// Fetch oEmbed output.
	if ( apply_filters( 'wpex_has_oembed_cache', true ) ) {
		global $wp_embed;
		if ( is_object( $wp_embed ) ) {
			$html = $wp_embed->shortcode( [], $video_escaped );
		}
	} else {
		$html = wp_oembed_get( $video_escaped );
	}

	// Return if there is an error fetching the oembed code.
	if ( empty( $html ) || is_wp_error( $html ) ) {
		return;
	}

	// Add classes.
	if ( $classes ) {

		// Class attribute already added already via filter.
		if ( strpos( 'class="', $html ) ) {
			$html = str_replace( 'class="', 'class="' . esc_attr( $classes ) . ' ', $html );
		}

		// No class attribute found so lets add new one with our custom classes.
		else {
			$html = str_replace( '<iframe', '<iframe class="' . esc_attr( $classes ) . '"', $html );
		}

	}

	// Apply filters for params.
	$params = (array) apply_filters( 'wpex_video_oembed_params', $params );

	// Add params.
	if ( $params && is_array( $params ) ) {

		// Define empty params string.
		$params_string = '';

		// Loop through and check vendors.
		foreach ( $params as $vendor => $params ) {

			// YouTube fixes.
			$vendor = ( 'youtube' === $vendor ) ? 'yout' : $vendor;

			// Check initial video url for vendor (youtube/vimeo/etc).
			if ( str_contains( $video_escaped, $vendor ) ) {

				// Loop through and add params to variable.
				foreach ( $params as $key => $val ) {
					$params_string .= '&' . esc_attr( $key ) . '=' . esc_attr( $val );
				}

			}

		}

		// Add params.
		if ( $params_string ) {
			$html = str_replace( '?feature=oembed', '?feature=oembed' . $params_string, $html );
		}

	}

	return $html;
}
