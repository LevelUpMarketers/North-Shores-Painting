<?php

namespace TotalThemeCore\Vcex;

\defined( 'ABSPATH' ) || exit;

class Term_Query {

	/**
	 * Term_Query Args.
	 */
	protected $args = [];

	/**
	 * Constructor.
	 */
	public function __construct( array $shortcode_atts = [], string $shortcode_tag = '' ) {
		$this->args = $this->get_args( $shortcode_atts, $shortcode_tag );
		$this->args = (array) \apply_filters( 'totalthemecore/vcex/term_query/args', $this->args, $shortcode_atts, $shortcode_tag );
	}

	/**
	 * Return Terms.
	 */
	private function get_args( array $atts, string $shortcode_tag ): array {
		$query_type = $atts['query_type' ] ?? '';

		// Get current Taxonomy
		if ( ( 'tax_children' === $query_type || 'tax_parent' === $query_type ) && \is_tax() ) {
			$taxonomy = \get_query_var( 'taxonomy' );
		} else {
			$taxonomy = $atts['taxonomy'] ?? [];
		}

		// Convert taxonomy to array if there is more than 1 selected
		if ( \is_string( $taxonomy ) && \str_contains( $taxonomy, ',' ) ) {
			$taxonomy = \explode( ',', $taxonomy );
		}

		// Get primary term
		if ( 'primary_term' === $query_type ) {
			$primary_term = null;
			if ( \function_exists( 'totaltheme_get_post_primary_term' ) ) {
				$primary_term = totaltheme_get_post_primary_term( '', $taxonomy );
				if ( ! $taxonomy && isset( $primary_term->taxonomy ) ) {
					$taxonomy = $primary_term->taxonomy;
				}
			}
			if ( ! $primary_term ) {
				return []; // there is no primary term
			}
		}

		// Taxonomy is required
		if ( ! $taxonomy ) {
			return [];
		}
		
		// Add args
		$args = [
			'taxonomy'   => $taxonomy,
			'hide_empty' => \vcex_validate_att_boolean( 'hide_empty', $atts, true ),
		];

		if ( ! empty( $atts['order'] ) ) {
			$args['order'] = $atts['order'];
		}

		if ( ! empty( $atts['orderby'] ) ) {
			$args['orderby'] = \sanitize_sql_orderby( $atts['orderby'] );
		}

		/**
		 * @note can't use vcex_validate_att_boolean because we need to fix
		 * inconsistency with this setting being abled on one element but not the other
		 */
		if ( isset( $atts['parent_terms'] ) && \vcex_validate_boolean( $atts['parent_terms'] ) ) {
			$args['parent'] = 0;
		}

		if ( ! empty( $atts['child_of'] ) && \is_string( $taxonomy ) ) {
			if ( \is_numeric( $atts['child_of'] ) ) {
				$args['child_of'] = $atts['child_of'];
				unset( $args['parent'] );
			} else {
				$child_of = get_term_by( 'slug', $atts['child_of'], $taxonomy );
				if ( $child_of && ! is_wp_error( $child_of ) && isset( $child_of->term_id ) ) {
					$args['child_of'] = $child_of->term_id;
					unset( $args['parent'] );
				}
			}
		}

		// Add arguments based on query_type
		switch ( $query_type ) {
			case 'post_terms':
				if ( ! vcex_get_template_edit_mode() ) {
					$args['object_ids'] = \vcex_get_the_ID();
				}
				break;
			case 'primary_term':
				if ( ! empty( $primary_term ) ) {
					$args['include'] = [ $primary_term->term_id ];
				} 
				break;
			case 'tax_children':
				if ( \is_tax() ) {
					$args['child_of'] = \get_queried_object_id();
					unset( $args['parent'] );
				}
				break;
			case 'tax_parent':
				if ( \is_tax() ) {
					$args['parent'] = \get_queried_object_id();
				}
				break;
		}

		// Include exclude terms
		if ( 'wpex_term_cards' === $shortcode_tag && 'primary_term' !== $query_type ) {
			if ( ! empty( $atts['include_terms'] ) ) {
				$include_terms = preg_split( '/\,[\s]*/', $atts['include_terms'] );
				if ( is_array( $include_terms ) ) {
					$args['include'] = $include_terms;
				}
			}
			if ( ! empty( $atts['exclude_terms'] ) ) {
				$exclude_terms = preg_split( '/\,[\s]*/', $atts['exclude_terms'] );
				if ( is_array( $exclude_terms ) ) {
					$args['exclude'] = $exclude_terms;
				}
			}
		}
		
		return (array) \apply_filters( 'vcex_terms_grid_query_args', $args, $atts ); // @deprecated
	}

	/**
	 * Return Terms.
	 */
	public function get_terms() {
		return $this->args ? \get_terms( $this->args ) : false;
	}

}
