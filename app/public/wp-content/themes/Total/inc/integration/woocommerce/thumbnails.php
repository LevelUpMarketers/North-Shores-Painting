<?php

namespace TotalTheme\Integration\WooCommerce;

defined( 'ABSPATH' ) || exit;

/**
 * Theme tweaks for WooCommerce images.
 */
final class Thumbnails {

	/**
	 * Instance.
	 */
	private static $instance = null;

	/**
	 * Create or retrieve the instance of Thumbnails.
	 */
	public static function instance() {
		if ( null === static::$instance ) {
			static::$instance = new self();
		}
		return static::$instance;
	}

	/**
	 * Private constructor.
	 */
	private function __construct() {

		// Disable thumb regeneration as much as possible.
		\add_filter( 'woocommerce_resize_images', '__return_false' );
		\add_filter( 'woocommerce_image_sizes_to_resize', '__return_empty_array' );
		\add_filter( 'woocommerce_regenerate_images_intermediate_image_sizes', '__return_empty_array' );
		\add_action( 'customize_register', [ $this, 'remove_customizer_sections' ], 99 );

		// Admin only functions.
		if ( \wpex_is_request( 'admin' ) ) {
			\add_filter( 'admin_post_thumbnail_size', [ $this, 'admin_post_thumbnail_size' ], 10, 3 );
			\add_filter( 'wpex_image_sizes_tabs', [ $this, 'image_sizes_tabs' ], 40 );
		}

		// Add image sizes to Total panel.
		\add_filter( 'wpex_image_sizes', [ $this, 'add_image_sizes' ], 99 );

		// Define single shop thumbnail size.
		\add_filter( 'woocommerce_gallery_thumbnail_size', [ $this, 'gallery_thumbnail_size' ] );

		// Filter image sizes and return cropped versions.
		if ( \get_theme_mod( 'image_resizing', true ) ) {
			// @todo Could be optimized a bit to prevent duplicate checks.
			// @todo Figure out how to add retina support to the WooCommerce images since you can't easily add data attributes via Woo filters.
			\add_filter( 'wp_get_attachment_image_attributes', [ $this, 'filter_wp_get_attachment_image_attributes' ], 9999, 3 );
			\add_filter( 'wp_get_attachment_image_src', [ $this, 'filter_wp_get_attachment_image_src' ], 9999, 4 );
		}

	}

	/**
	 * Add WooCommerce tab to Total image sizes panel.
	 */
	public function image_sizes_tabs( $array ): array {
		$array['woocommerce'] = 'WooCommerce';
		return $array;
	}

	/**
	 * Add custom image sizes.
	 */
	public function add_image_sizes( $sizes ): array {
		return array_merge( $sizes, [
			'shop_catalog' => [
				'label'   => esc_html__( 'Product Entry', 'total' ),
				'section' => 'woocommerce',
			],
			'shop_single' => [
				'label'   => esc_html__( 'Product Post', 'total' ),
				'section' => 'woocommerce',
			],
			'shop_single_thumbnail' => [
				'label'    => esc_html__( 'Product Post Gallery Thumbnail', 'total' ),
				'section' => 'woocommerce',
			],
			'shop_category' => [
				'label'   => esc_html__( 'Category Thumbnail', 'total' ),
				'section' => 'woocommerce',
			],
			'shop_cart' => [
				'label'   => esc_html__( 'Widgets & Cart Thumbnail', 'total' ),
				'section' => 'woocommerce',
			],
		] );
	}

	/**
	 * Define single shop thumbnail size.
	 */
	public function gallery_thumbnail_size(): string {
		return 'shop_single_thumbnail';
	}

	/**
	 * Filter image attributes to add aspect ratio classes.
	 */
	public function filter_wp_get_attachment_image_attributes( $attr, $attachment, $size ) {
		switch ( $size ) {
			case 'single':
			case 'shop_single':
			case 'woocommerce_single':
				$custom_size = 'shop_single';
				break;
			case 'shop_single_thumbnail':
				$custom_size = 'shop_single_thumbnail';
				break;
			case 'woocommerce_thumbnail':
				$custom_size = 'shop_cart';
				break;
			case 'shop_catalog':
				$custom_size = 'shop_catalog';
				break;
		}
		if ( isset( $custom_size ) && $aspect_ratio = \get_theme_mod( "{$custom_size}_image_aspect_ratio" ) ) {
			if ( \array_key_exists( $aspect_ratio, \totaltheme_get_aspect_ratio_choices() ) ) {
				$object_class = 'wpex-aspect-' . \str_replace( '/', '-', $aspect_ratio );
				$object_fit = get_theme_mod( "{$custom_size}_image_fit" ) ?: 'cover';
				if ( $object_fit && in_array( $object_fit, [ 'cover', 'contain', 'fill', 'scale-down', 'none' ], true ) ) {
					$object_class .= " wpex-object-{$object_fit}";
				}
				$object_position = get_theme_mod( "{$custom_size}_image_position" );
				if ( $object_position && in_array( $object_position, [ 'top', 'center', 'bottom', 'left-top', 'left-center', 'left-bottom', 'right-top', 'right-center', 'right-bottom' ], true ) ) {
					$object_class .= " wpex-object-{$object_position}";
				}
				if ( isset( $attr['class'] ) ) {
					if ( \is_array( $attr['class'] ) ) {
						$attr['class'][] = $object_class;
					} elseif ( \is_string( $attr['class'] ) ) {
						$attr['class'] .= $attr['class'] ? " {$object_class}" : $object_class;
					}
				} else {
					$attr['class'] = $object_class;
				}
			}
		}
		return $attr;
	}

	/**
	 * Filter image sizes and return cropped versions where we aren't altering the HTML.
	 */
	public function filter_wp_get_attachment_image_src( $image, $attachment_id, $size, $icon ) {
		if ( $image ) {

			switch ( $size ) {
				case 'single':
				case 'shop_single':
				case 'woocommerce_single':
					$custom_dims = wpex_get_thumbnail_sizes( 'shop_single' );
					$custom_size = 'shop_single';
					break;
				case 'shop_single_thumbnail':
					$custom_dims = wpex_get_thumbnail_sizes( 'shop_single_thumbnail' );
					$custom_size = 'shop_single_thumbnail';
					break;
				case 'woocommerce_thumbnail':
					$custom_dims = wpex_get_thumbnail_sizes( 'shop_cart' );
					$custom_size = 'shop_cart';
					break;
				case 'shop_catalog':
					$custom_dims = wpex_get_thumbnail_sizes( 'shop_catalog' );
					$custom_size = 'shop_catalog';
					break;
			}

			// Generate custom image size via theme resizer.
			// @note We must always pass through the theme's function because otherwise it will return the defined Woo image sizes.
			if ( $attachment_id && ! empty( $custom_size ) ) {
				$generate_image = wpex_image_resize( [
					'attachment' => $attachment_id,
					'size'       => $custom_size,
					'height'     => $custom_dims['height'] ?? '',
					'width'      => $custom_dims['width'] ?? '',
					'crop'       => $custom_dims['crop'] ?? '',
					'image_src'  => $image, // IMPORTANT !!
				] );
				if ( ! empty( $generate_image ) ) {
					$image = $generate_image;
				}
			}
		}

		return $image;
	}

	/**
	 * Remove customizer sections.
	 */
	public function remove_customizer_sections( $wp_customize ): void {
		$wp_customize->remove_section( 'woocommerce_product_images' );
	}

	/**
	 * Set admin post thumbnail to correct size.
	 */
	public function admin_post_thumbnail_size( $size, $thumbnail_id, $post ) {
		if ( 'product' === get_post_type( $post ) ) {
			return 'shop_single';
		}
		return $size;
	}

	/**
	 * Modify the cart thumbnail html.
	 */
	public function cart_item_thumbnail() {
		_deprecated_function( __METHOD__, 'Total 6.0', '' );
	}

	/**
	 * Remove product settings from WooCommerce admin panel.
	 */
	public function remove_product_settings() {
		_deprecated_function( __METHOD__, 'Total 6.0', '' );
	}

	/**
	 * Prevent cloning.
	 */
	private function __clone() {}

	/**
	 * Prevent unserializing.
	 */
	public function __wakeup() {
		\trigger_error( 'Cannot unserialize a Singleton.', \E_USER_WARNING);
	}

}
