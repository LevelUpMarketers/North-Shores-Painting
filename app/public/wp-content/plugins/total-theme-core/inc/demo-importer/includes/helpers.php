<?php

namespace TotalTheme\Demo_Importer;

\defined( 'ABSPATH' ) || exit;

class Helpers {

	/**
	 * New plugin installer.
	 */
	public static function new_plugin_installer() {
		if ( ! \class_exists( 'TotalTheme\Demo_Importer\Plugin_Installer' ) ) {
			require_once TOTALTHEME_DEMO_IMPORTER_DIR_PATH . 'includes/plugin-installer.php';
		}

		return new Plugin_Installer;
	}

	/**
	 * New content importer;
	 */
	public static function new_content_importer( $demo, $data ) {
		if ( ! \class_exists( 'TotalTheme\Demo_Importer\Content_Importer' ) ) {
			require_once TOTALTHEME_DEMO_IMPORTER_DIR_PATH . 'includes/content-importer.php';
		}

		return new Content_Importer( $demo, $data );
	}

	/**
	 * New widget importer;
	 */
	public static function new_widget_importer( $demo ) {
		if ( ! \class_exists( 'TotalTheme\Demo_Importer\Widget_Importer' ) ) {
			require_once \TOTALTHEME_DEMO_IMPORTER_DIR_PATH . 'includes/widget-importer.php';
		}

		return new Widget_Importer( $demo );
	}

	/**
	 * Update imported content option.
	 */
	public static function get_imported_data_list(): array {
		return (array) \get_option( 'totaltheme_demo_importer_imported_data', [] );
	}

	/**
	 * Delete imported data option.
	 */
	public static function delete_imported_data_list(): bool {
		return (bool) \delete_option( 'totaltheme_demo_importer_imported_data' );
	}

	/**
	 * Update imported content option.
	 */
	public static function update_imported_data_list( $part = 'all', $new_data = '' ): bool {
		if ( empty( $new_data ) ) {
			return true;
		}

		$option = self::get_imported_data_list();

		if ( 'all' === $part ) {
			$option = (array) $new_data;
		} else {
			if ( \in_array( $part, [ 'posts', 'categories', 'terms', 'attachments' ], true ) ) {
				if ( ! isset( $option[ $part ] ) || ! \is_array( $option[ $part ] ) ) {
					$option[ $part ] = [];
				}
				if ( \is_array( $new_data ) ) {
					$option[ $part ] = \array_unique( \array_merge( $option[ $part ], $new_data ) );
				} elseif ( ! in_array( $new_data, $option[ $part ], true ) ) {
					$option[ $part ][] = $new_data;
				}
			} else {
				$option[ $part ] = $new_data;
			}
		}

		return (bool) \update_option( 'totaltheme_demo_importer_imported_data', $option, false );
	}

	/**
	 * Returns true if on demo importer.
	 */
	public static function is_admin_page( $hook ): bool {
		return ( \defined( '\WPEX_ADMIN_PANEL_HOOK_PREFIX' ) && \WPEX_ADMIN_PANEL_HOOK_PREFIX . '-demo-importer' === $hook );
	}

	/**
	 * Fix broken WPBakery background images.
	 */
	public static function fix_wpb_broken_background_images( $context ) {
		if ( \is_string( $context ) && \str_contains( $context, 'url(' ) ) {
			\preg_match_all('~\bbackground(url-image)?\s*:(.*?)\(\s*(\'|")?(?<image>.*?)\3?\s*\)~i', $context, $matches );
			if ( ! empty( $matches['image'] ) ) {
				foreach ( $matches['image'] as $image_url ) {
					if ( \str_contains( $image_url, '?id=' ) ) {
						\preg_match( '/\?id=(\d+)/', $image_url, $image_id );
						if ( ! empty( $image_id[1] ) && $correct_url = \esc_url( \wp_get_attachment_url( $image_id[1] ) ) ) {
							$context = \str_replace( $image_url, "{$correct_url}?id={$image_id[1]}", $context );
						}
					}
				}
			}
		}
		return $context;
	}

	/**
	 * Replace demo urls.
	 */
	public static function replace_demo_urls( $demo, $text ) {
		if ( $text && is_string( $text ) ) {

			// Normal urls.
			if ( str_contains( $text, 'https://total' ) ) {
				$from_url = [
					"https://total.wpexplorer.com/{$demo}/",
					"https://totalwpthemedemo.com/{$demo}/",
				];
				$to_url = esc_url( home_url( '/' ) );
				$text = \str_replace( $from_url, $to_url, $text );
			}

			// Encoded urls.
			if ( str_contains( $text, 'https%3A%2F%2Ftotal' ) ) {
				$from_url_encoded = [
					"https%3A%2F%2Ftotal.wpexplorer.com%2F{$demo}%2F",
					"https%3A%2F%2Ftotalwpthemedemo.com%2F{$demo}%2F",
				];
				$to_url = esc_url( home_url( '/' ) );
				$text = \str_replace( $from_url_encoded, rawurlencode( $to_url ), $text );
			}

			// Double encoded urls.
			if ( str_contains( $text, 'https%253A%252F%252Ftotal' ) ) {
				$to_url_encoded = rawurlencode( rawurlencode( $to_url ) );
				$from_url_encoded = [
					"https%253A%252F%252Ftotal.wpexplorer.com%252F{$demo}%252F",
					"https%253A%252F%252Ftotalwpthemedemo.com%252F{$demo}%252F",
				];
				$to_url = esc_url( home_url( '/' ) );
				$text = \str_replace( $from_url_encoded, rawurlencode( rawurlencode( $to_url ) ), $text );
			}

		}
		return $text;
	}

	/**
	 * Replace demo urls in json.
	 */
	public static function replace_demo_urls_json( $demo, $json ) {
		if ( str_contains( $json, "https:\/\/totalwpthemedemo.com\/{$demo}\/" ) ) {
			$json = \str_replace( "https:\/\/totalwpthemedemo.com\/{$demo}\/", \str_replace( '/', '\/', esc_url( home_url( '/' ) ) ), $json );
		}
		return $json;
	}

	/**
	 * Get demo builder.
	 */
	public static function get_demo_builder( $demo_data = [] ) {
		if ( isset( $demo_data['builder'] ) ) {
			return sanitize_text_field( $demo_data['builder'] );
		}
		$plugins = $demo_data['plugins'] ?? [];
		if ( is_array( $plugins ) ) {
			if ( in_array( 'WPBakery Page Builder', $plugins, true ) ) {
				return 'WPBakery';
			} elseif ( in_array( 'Elementor', $plugins, true ) ) {
				return 'Elementor';
			} elseif ( in_array( 'Gutenberg', $plugins, true ) ) {
				return 'Gutenberg';
			}
		}
		return 'none';
	}

	/**
	 * Get remote data.
	 */
	public static function get_remote_data( $demo, $data, $args = [] ) {
		$args = \wp_parse_args( $args, [
			'redirection' => 0,
		] );
		$response = \wp_safe_remote_get( "https://totalwpthemedemo.com/{$demo}/wp-json/twtd/v1/export/{$data}", $args );
		if ( ! \is_wp_error( $response ) && 200 === \wp_remote_retrieve_response_code( $response ) ) {
			return \wp_remote_retrieve_body( $response ) ?: false;
		} else {
			return 'error';
		}
	}

	/**
	 * Render demo screenshot.
	 */
	public static function render_demo_screenshot( $demo, $alt = '' ): void {
		if ( str_ends_with( $demo, '-elementor' ) ) {
			$demo = str_replace( '-elementor', '', $demo );
		}
		$url = "https://totalwptheme.com/wp-content/uploads/demo-screenshots/jpgs/{$demo}.jpg";
		echo '<img src="' . esc_url( $url ) .'" loading="lazy" alt="' . esc_attr( $alt ) . '">';
	}

}
