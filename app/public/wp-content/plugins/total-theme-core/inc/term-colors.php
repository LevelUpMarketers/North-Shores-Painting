<?php

namespace TotalThemeCore;

\defined( 'ABSPATH' ) || exit;

/**
 * Adds support for Term colors.
 */
class Term_Colors {

	/**
	 * The meta_id used to store the term color.
	 */
	protected const META_KEY = 'wpex_color';

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Init.
	 */
	public static function init() {
		$is_admin = \is_admin();

		if ( $is_admin ) {
			\add_filter( 'wpex_term_meta_options', [ self::class, 'filter_wpex_term_meta_options'] );
		}

		if ( ! $is_admin || \wp_doing_ajax() ) {
			\add_action( 'totaltheme/inline_css', [ self::class, 'on_totaltheme_inline_css' ], 40 ); // set high priority.
		}
	}

	/**
	 * Returns an array of supported taxonomies for the color option.
	 */
	public static function supported_taxonomies() {
		$taxonomies = [ 'category' ];

		if ( \is_callable( '\PTU\Taxonomies::get_registered_items' ) ) {
			$ptu_taxonomies = \PTU\Taxonomies::get_registered_items();
			if ( \is_array( $ptu_taxonomies ) && \function_exists( '\wpex_get_ptu_tax_mod' ) ) {
				foreach ( $ptu_taxonomies as $ptu_taxonomy_post ) {
					$taxonomy = \get_post_meta( $ptu_taxonomy_post, '_ptu_name', true );
					if ( \taxonomy_exists( $taxonomy ) && \wp_validate_boolean( \wpex_get_ptu_tax_mod( $taxonomy, 'term_colors' ) ) ) {
						$taxonomies[] = $taxonomy;
					}
				}
			}
		}

		return (array) \apply_filters( 'wpex_term_colors_supported_taxonomies', $taxonomies );
	}

	/**
	 * Adds a new term option for defining the term color.
	 */
	public static function filter_wpex_term_meta_options( $options ) {
		$supported_taxonomies = (array) self::supported_taxonomies();
		if ( $supported_taxonomies ) {
			$new_options = [
				self::META_KEY => [
					'label'          => \esc_html__( 'Color', 'total-theme-core' ),
					'type'           => 'color',
					'has_admin_col'  => true,
					'show_on_create' => true,
					'taxonomies'     => $supported_taxonomies,
					'exclude'        => 'extra,theme',
					'args'           => [
						'type'              => 'color',
						'single'            => true,
						'sanitize_callback' => 'sanitize_text_field',
					],
				],
			];
			$options = \array_merge( $new_options, $options );
		}
		return $options;
	}

	/**
	 * Returns the color for a given term.
	 */
	public static function get_term_color( $term ) {
		$term = \get_term( $term );
		if ( $term && ! \is_wp_error( $term ) ) {
			return \get_term_meta( $term->term_id, self::META_KEY, true );
		}
	}

	/**
	 * Generates CSS for term colors: "has-term-{term_id}-color" and "has-term-{term_id}-background-color".
	 */
	public static function get_terms_colors_css() {
		$taxonomies = self::supported_taxonomies();

		if ( ! \is_array( $taxonomies ) || 0 === \count( $taxonomies ) ) {
			return;
		}

		$terms_colors = [];

		$terms = \get_terms( [
			'taxonomy'   => $taxonomies,
			'hide_empty' => false,
			'meta_key'   => self::META_KEY,
		] );

		if ( $terms && ! \is_wp_error( $terms ) ) {
			foreach ( $terms as $term ) {
				$term_color = self::get_term_color( $term );
				if ( $term_color ) {
					$terms_colors[ $term->term_id ] = $term_color;
				}
			}
		}

		if ( ! $terms_colors ) {
			return;
		}

		$terms_css = '';
		$root_css = '';

		// Loop through colors to generate the term colors CSS.
		foreach ( $terms_colors as $term_id => $term_color ) {
			$term_color_safe = self::sanitize_color( $term_color );
			$term_id_safe    = \absint( $term_id );

			$root_css  .= "--wpex-term-{$term_id_safe}-color:{$term_color_safe};";
			$terms_css .= ".has-term-{$term_id_safe}-color{color:var(--wpex-term-{$term_id_safe}-color)!important;}";
			$terms_css .= ".has-term-{$term_id_safe}-background-color{background-color:var(--wpex-term-{$term_id_safe}-color)!important;}";
		}

		$root_css = ":root{{$root_css}}";

		return "{$root_css}{$terms_css}";
	}

	/**
	 * Hooks into "totaltheme/inline_css".
	 */
	public static function on_totaltheme_inline_css( $css ) {
		echo self::get_terms_colors_css();
	}

	/**
	 * Sanitizes the color.
	 */
	private static function sanitize_color( $color ) {
		$color = sanitize_text_field( $color );
		if ( \str_starts_with( $color, 'palette-' ) && \class_exists( 'WPEX_Color_Palette' ) ) {
			$color = "var(--wpex-{$color}-color)";
		}
		return $color;
	}

}
