<?php

namespace TotalThemeCore\Vcex\Helpers;

\defined( 'ABSPATH' ) || exit;

/**
 * Returns image from source.
 */
class Get_Image_From_Source {

	/**
	 * Image.
	 */
	protected $image = '';

	/**
	 * Source.
	 */
	protected $source = '';

	/**
	 * Shortcode attributes.
	 */
	protected $atts = [];

	/**
	 * Constructor.
	 */
	public function __construct( string $source = '', array $shortcode_atts = [], bool $fallback = false ) {
		$this->source = $source ?: 'media_library';
		$this->atts   = $shortcode_atts;

		$method_name = "get_{$source}_image";
		if ( \method_exists( $this, $method_name ) ) {
			$this->$method_name();
		}

		if ( $fallback && ! $this->image ) {
			$this->get_media_library_image( is_string( $fallback ) ? $fallback : '' );
		}
	}

	/**
	 * Returns the image.
	 */
	public function get() {
		if ( is_numeric( $this->image ) && 'attachment' !== \get_post_type( $this->image ) ) {
			return; // verify attachments exist.
		}
		return $this->image;
	}

	/**
	 * Get image from media library.
	 */
	protected function get_media_library_image( string $fallback = '' ) {
		$image = $this->atts['image_id'] ?? $this->atts['image'] ?? '';
		if ( ! empty( $image ) ) {
			if ( \is_array( $image ) ) {
				$this->image = $image['id'] ?? $image['attachment'] ?? null; // Elementor.
			} elseif ( is_numeric( $image ) ) {
				$this->image = $image;
			}
		}
	}

	/**
	 * Get external image.
	 */
	protected function get_external_image() {
		$image = $this->atts['external_image'] ?? '';
		if ( ! empty( $image ) && is_string( $image ) ) {
			// Value is a shortcode.
			if ( \str_contains( $image, '`{`' ) ) {
				$image = \str_replace( [
					'`{`',
					'`}`',
					'``',
				], [
					'[',
					']',
					'"',
				], $image );
			}
			// Replace vars.
			if ( \function_exists( '\totaltheme_replace_vars' ) ) {
				$image = \totaltheme_replace_vars( $image );
			}
			// Set image.
			$this->image = \sanitize_text_field( $image );
		}
	}

	/**
	 * Get featured image.
	 */
	protected function get_featured_image() {
		if ( 'attachment' === \get_post_type() ) {
			$this->image = \get_the_ID();
		} else {
			if ( ! vcex_is_card() && ! \in_the_loop() && ( \is_tax() || \is_tag() || \is_category() ) ) {
				$this->get_term_thumbnail_image();
			} else {
				$this->image = \get_post_thumbnail_id( \get_the_ID() );
			}
		}
	}

	/**
	 * Get term thumbnail image.
	 */
	protected function get_term_thumbnail_image() {
		if ( \function_exists( '\wpex_get_term_thumbnail_id' ) ) {
			$card = \vcex_card_instance();
			if ( ! empty( $card->term ) ) {
				$term_id = $card->term->term_id ?? 0;
			}
			$this->image = \wpex_get_term_thumbnail_id( $term_id ?? \get_queried_object_id() );
		}
	}

	/**
	 * Get primary term thumbnail image.
	 */
	protected function get_primary_term_thumbnail_image() {
		if ( \function_exists( '\wpex_get_term_thumbnail_id' )
			&& \function_exists( '\totaltheme_get_post_primary_term' )
			&& $primary_term = \totaltheme_get_post_primary_term()
		) {
			$this->image = \wpex_get_term_thumbnail_id( $primary_term );
		}
	}

	/**
	 * Get secondary thumbnail image.
	 */
	protected function get_secondary_thumbnail_image() {
		$secondary_thumbnail = \get_post_meta( \get_the_ID(), 'wpex_secondary_thumbnail', true );
		if ( ! empty( $secondary_thumbnail ) ) {
			$this->image = $secondary_thumbnail;
		}
	}

	/**
	 * Get card thumbnail image.
	 */
	protected function get_card_thumbnail_image() {
		$card_thumbnail = \get_post_meta( \get_the_ID(), 'wpex_card_thumbnail', true );
		if ( ! empty( $card_thumbnail ) ) {
			$this->image = $card_thumbnail;
		}
	}

	/**
	 * Get image from callback function.
	 */
	protected function get_callback_image() {
		$this->get_callback_function_image();
	}

	/**
	 * Get image from callback function.
	 */
	protected function get_callback_function_image() {
		if ( \function_exists( '\vcex_validate_user_func' ) ) {
			$callback = $this->atts['image_callback_function'] ?? $this->atts['callback_function'] ?? '';
			if ( $callback && \function_exists( $callback ) && \vcex_validate_user_func( $callback ) ) {
				$callback_val = \call_user_func( $callback );
				if ( ! empty( $callback_val ) ) {
					$this->image = $callback_val;
				}
			}
		}
	}

	/**
	 * Get custom field image.
	 */
	protected function get_custom_field_image() {
		$image_cf = $this->atts['image_custom_field'] ?? $this->atts['img_custom_field'] ?? $this->atts['custom_field_name'] ?? $this->atts['wpex_bg_image_custom_field'] ?? '';
		if ( $image_cf ) {
			$image_cf_safe = \sanitize_text_field( $image_cf );
			if ( \function_exists( '\vcex_get_meta_value' ) ) {
				$cf_val = \vcex_get_meta_value( $image_cf_safe );
			} else {
				if ( \function_exists( '\get_field' ) && \function_exists( '\acf_is_field_key' ) && \acf_is_field_key( $image_cf_safe ) ) {
					$cf_val = get_field( $image_cf_safe );
				} else {
					$cf_val = get_post_meta( get_the_ID(), $image_cf_safe, true );
				}
			}
			if ( ! empty( $cf_val ) ) {
				if ( \is_array( $cf_val ) ) {
					$cf_val = $cf_val['ID'] ?? $cf_val['id'] ?? null;
				}
				if ( \is_numeric( $cf_val ) ) {
					if ( $cf_val && 'attachment' === \get_post_type( $cf_val ) ) {
						$this->image = $cf_val;
					}
				} elseif( \is_string( $cf_val ) ) {
					$this->image = $cf_val;
				}
			}
		}
	}

	/**
	 * Get author avatar image.
	 */
	protected function get_author_avatar_image() {
		$this->image = $this->get_avatar_url( \get_post() );
	}

	/**
	 * Get current user avatar image.
	 */
	protected function get_user_avatar_image() {
		$this->image = $this->get_avatar_url( \wp_get_current_user() );
	}

	/**
	 * Get avatar URL.
	 */
	protected function get_avatar_url( $source ) {
		return \get_avatar_url( $source, [
			'size' => $this->atts['img_width'] ?? $this->atts['image_width'] ?? '',
		] );
	}

	/**
	 * Get post series thumbnail image.
	 */
	protected function get_post_series_thumbnail_image() {
		if ( \taxonomy_exists( 'post_series' ) && \function_exists( '\wpex_get_term_thumbnail_id' ) ) {
			if ( \is_tax( 'post_series' ) && ! \vcex_is_card() && ! \in_the_loop() ) {
				$this->image = \wpex_get_term_thumbnail_id( get_queried_object_id() );
			} else {
				$post_series = get_the_terms( get_post(), 'post_series' );
				if ( ! is_wp_error( $post_series ) && ! empty( $post_series[0]->term_id ) ) {
					$this->image = \wpex_get_term_thumbnail_id( $post_series[0]->term_id );
				}
			}
		}
	}

}
