<?php

namespace TotalTheme\Theme_Builder;

\defined( 'ABSPATH' ) || exit;

/**
 * Returns the template to use for the current post.
 */
class Post_Template {

	/**
	 * Stores the current post template.
	 */
	protected static $template_id;

	/**
	 * Store check if post has template.
	 */
	protected static $has_template;

	/**
	 * Checks if wp has loaded.
	 */
	protected static function wp_loaded(): bool {
		return (bool) \did_action( 'wp_loaded' );
	}

	/**
	 * Helper function checks if the current post has a template.
	 */
	public static function has_template(): bool {
		if ( ! \is_null( self::$has_template ) && self::wp_loaded() ) {
			return self::$has_template;
		}
		self::$has_template = (bool) self::get_template_content();
		return self::$has_template;
	}

	/**
	 * Get current post template id.
	 */
	public static function get_template_id( $post_type = '' ) {
		if ( ! \is_null( self::$template_id ) && self::wp_loaded() ) {
			return self::$template_id; // once we grab the template once we save it to prevent extra checks
		}

		$post_id = \is_admin() ? \get_the_ID() : \wpex_get_current_post_id(); // this is very important!!!!

		if ( ! $post_type ) {
			$post_type = \get_post_type( $post_id );
		}

		if ( $post_id && $meta = \get_post_meta( $post_id, 'wpex_singular_template', true ) ) {
			$template_id = $meta;
		} else {
			if ( \function_exists( 'is_product' ) && \is_product() ) {
				if ( ! \is_callable( '\Wt_Gc_Gift_Card_Common::is_gift_card_product' )
					|| ! \Wt_Gc_Gift_Card_Common::is_gift_card_product( get_the_ID() )
				) {
					$template_id = \get_theme_mod( 'woo_singular_template' );
				}
			} else {
				if ( $ptu_template_id = \wpex_get_ptu_type_mod( $post_type, 'singular_template_id' ) ) {
					$template_id = $ptu_template_id;
				} else {
					$template_id = \get_theme_mod( "{$post_type}_singular_template" );
					if ( taxonomy_exists( 'post_series' ) ) {
						$series_template_id = \get_theme_mod( 'post_series_singular_template' );
						if ( $series_template_id && get_the_terms( get_the_id(), 'post_series' ) ) {
							$template_id = $series_template_id;
						}
					}
				}
			}
		}

		$template_id = (int) \apply_filters( 'wpex_get_singular_template_id', $template_id, $post_type ); // @deprecated

		return (int) \apply_filters( 'wpex_singular_template_id', $template_id, $post_type );
	}

	/**
	 * Return current post template content.
	 */
	public static function get_template_content( $post_type = '' ) {
		$template_id = self::get_template_id( $post_type );

		$template_id = $template_id ? \wpex_parse_obj_id( $template_id, 'page' ) : false;

		if ( empty( $template_id ) ) {
			return;
		}

		$post = \get_post( $template_id );

		if ( $post && 'publish' === \get_post_status( $post ) ) {
			return $post->post_content;
		}
	}

	/**
	 * Render post template.
	 */
	public static function render_template( $template_content = '' ) {
		$parsed_content = \totaltheme_render_content( $template_content );
		$tag            = (string) \apply_filters( 'wpex_singular_template_html_tag', 'div' );
		$tag_escaped    = \tag_escape( $tag );

		$class = [
			'custom-singular-template',
			'entry',
			'wpex-clr',
		];

		if ( \function_exists( 'wc_get_product_class' ) && \is_singular( 'product' ) ) {
			$class = \array_unique( \array_merge( $class, (array) \wc_get_product_class() ) );
			if ( \function_exists( '\WC' ) && isset( \WC()->structured_data ) && \is_callable( [ \WC()->structured_data, 'generate_product_data' ] ) ) {
				WC()->structured_data->generate_product_data();
			}
		}

		echo '<' . $tag_escaped . ' class="' . \esc_attr( \implode( ' ', $class ) ) . '">' . $parsed_content . '</' . $tag_escaped . '>';

		return true;
	}

}
