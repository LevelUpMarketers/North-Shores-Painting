<?php declare(strict_types=1);

namespace TotalTheme;

\defined( 'ABSPATH' ) || exit;

/**
 * Replace_Vars Class.
 */
final class Replace_Vars {

	/**
	 * Check if functionality is enabled.
	 *
	 * @note we run this check for each instance so it could be disabled in certain situations of needed.
	 */
	protected function is_enabled(): bool {
		return (bool) \apply_filters( 'totaltheme/replace_vars/is_enabled', true );
	}

	/**
	 * Check if currently in template edit mode.
	 */
	protected function is_template_edit(): bool {
		return \function_exists( '\vcex_get_template_edit_mode' ) && \vcex_get_template_edit_mode();
	}

	/**
	 * Replace `{{variable_placeholders}}` with their correct value.
	 *
	 * @param string $text The string to replace the variables in.
	 * @param array $args Arguments to pass to the replacement.
	 *
	 * @return mixed $replaced_value
	 */
	public function replace( string $text, array $args = [] ): string {
		if ( ! \is_string( $text ) || ! \str_contains( $text, '{{' ) || ! \str_contains( $text, '}}' ) || ! $this->is_enabled() ) {
			return $text;
		}

		return \preg_replace_callback( "/\{\{([^}]*)}\}/", [ $this, 'handle_replacements' ], $text );
	}

	/**
	 * Returns the array of variables and their values.
	 */
	private function get_vars(): array {
		$vars = [
			'current_url' => '',
			'post_id' => '',
			'post_rating' => '',
			'post_author' => '',
			'post_date' => '',
			'post_modified' => '',
			'post_title' => '',
			'post_slug' => '',
			'post_subheading' => '',
			'post_excerpt' => '',
			'post_content' => '',
			'post_type_name' => '',
			'post_type_singular_name' => '',
			'title' => '',
			'taxonomy' => '',
			'term_id' => '',
			'term_name' => '',
			'term_description' => '',
			'permalink' => '',
			'category' => '',
			'primary_term_id' => '',
			'paged' => '',
			'post_count' => '',
			'card_icon' => '',
			'card_running_count' => '',

			// @todo ?
		//	'author_first_name' => '',
		//	'author_last_name' => '',
		//	'post_year' => '',
		//	'post_month' => '',
		//	'post_day' => '',
		//	'currentdate' => '',
		//	'currentyear' => '',
		//	'currentmonth' => '',
		//	'currentday' => '',
		//	'max_num_pages' => '',
		];

		return (array) \apply_filters( 'totaltheme/replace_vars/vars', $vars );
	}

	/**
	 * Handles the variable replacement.
	 *
	 * @param array $matches The matches returned by preg_replace_callback
	 * @return mixed
	 */
	private function handle_replacements( $matches ) {
		$vars        = $this->get_vars();
		$replacement = $matches[0];
		$var_name    = $matches[1] ?? '';
		$var_exists  = \array_key_exists( $var_name, $vars );
		$has_args    = ! $var_exists && $this->var_has_args( $var_name );

		if ( $var_exists || $has_args ) {
			if ( ! $has_args && ! empty( $vars[ $var_name ] ) ) {
				$replacement = \is_callable( $vars[ $var_name ] ) ? \call_user_func( $vars[ $var_name ] ) : $vars[ $var_name ];
			} else {
				$method_suffix = $has_args ? \strtok( $var_name, '_' ) : $var_name;
				$method = "get_{$method_suffix}";
				if ( \method_exists( $this, $method ) ) {
					if ( $has_args ) {
						$replacement = $this->$method( $var_name );
					} else {
						$replacement = $this->$method();
					}
				}
			}
		}

		if ( null === $replacement || false === $replacement ) {
			$replacement = '';
		} elseif ( ! \is_scalar( $replacement ) ) {
			$replacement = $matches[0];
		}

		return $replacement;
	}

	/**
	 * Checks if the current variable has args.
	 */
	private function var_has_args( string $var ): bool {
		return ( \str_starts_with( $var, 'acf_' ) || \str_starts_with( $var, 'cf_' ) || \str_starts_with( $var, 'icon_' ) );
	}

	/**
	 * Get the current URL.
	 */
	private function get_current_url(): string {
		return (string) \wpex_get_current_url();
	}

	/**
	 * Get the title var value.
	 */
	private function get_title(): string {
		if ( \in_the_loop() || \totaltheme_is_card() ) {
			return (string) $this->get_post_title();
		} elseif ( $title = \totaltheme_get_instance_of( 'Title' ) ) {
			return (string) $title->get();
		} else {
			return '';
		}
	}

	/**
	 * Get the post_title var value.
	 */
	private function get_post_title(): string {
		return (string) \get_the_title();
	}

	/**
	 * Get the post id.
	 */
	private function get_post_id(): int {
		return (int) \get_the_ID();
	}

	/**
	 * Get Post Rating.
	 */
	private function get_post_rating(): string {
		$rating = \get_post_meta( \get_the_ID(), 'wpex_post_rating', true );
		if ( ! $rating && \function_exists( 'wc_get_product' ) && 'product' === \get_post_type() ) {
			$product = \wc_get_product( get_the_ID() );
			if ( $product && \is_callable( [ $product, 'get_average_rating' ] ) ) {
				$rating = $product->get_average_rating();
			}
		}
		return (string) $rating;
	}

	/**
	 * Get the category name.
	 */
	private function get_category(): string {
		if ( $primary_term = totaltheme_get_post_primary_term() ) {
			return (string) $primary_term->name ?? '';
		}
		return '';
	}

	/**
	 * Get the primary term id.
	 */
	private function get_primary_term_id(): ?int {
		if ( $primary_term = totaltheme_get_post_primary_term() ) {
			return (int) $primary_term->term_id ?? '';
		} elseif ( $this->is_template_edit() ) {
			return 0;
		} else {
			return null;
		}
	}

	/**
	 * Get the post date.
	 */
	private function get_post_date(): string {
		return (string) \get_the_date();
	}

	/**
	 * Get the post modified date.
	 */
	private function get_post_modified(): string {
		return (string) \get_the_modified_date();
	}

	/**
	 * Get post slug.
	 */
	private function get_post_slug(): string {
		return (string) \get_post_field( 'post_name', \get_post() );
	}

	/**
	 * Get the post permalink.
	 */
	private function get_permalink(): string {
		return (string) \get_permalink();
	}

	/**
	 * Get the post author.
	 */
	private function get_post_author(): string {
		return (string) \get_the_author();
	}

	/**
	 * Get the post subheading.
	 */
	private function get_post_subheading(): string {
		return (string) totaltheme_call_static( 'Page\Header', 'get_subheading' );
	}

	/**
	 * Get the post content.
	 */
	private function get_post_content(): string {
		$content = \get_the_content();
		return ( $content && ! \str_contains( $content, '{{post_content}}' ) ) ? \totaltheme_render_content( $content ) : '';
	}

	/**
	 * Returns the post type name.
	 */
	private function get_post_type_name(): string {
		$name = get_post_type_object( get_post_type() )->labels->name ?? '';
		return $name ? esc_html( $name ) : '';
	}

	/**
	 * Returns the post type singular name.
	 */
	private function get_post_type_singular_name(): string {
		$name = get_post_type_object( get_post_type() )->labels->singular_name ?? '';
		return $name ? esc_html( $name ) : '';
	}

	/**
	 * Get the post excerpt.
	 */
	private function get_post_excerpt(): string {
		return (string) \get_the_excerpt() ?: '';
	}

	/**
	 * Get current taxonomy name.
	 */
	private function get_taxonomy() {
		$card = \totaltheme_card_instance();
		if ( isset( $card->term ) ) {
			return $card->term->taxonomy ?? '';
		}
		if ( $this->is_template_edit() ) {
			return "{{taxonomy}}";
		}
		$obj = \get_queried_object();
		if ( \is_a( $obj, 'WP_Term' ) ) {
			$tax = \get_taxonomy( $obj->taxonomy );
			if ( \is_a( $tax, 'WP_Taxonomy' ) ) {
				return (string) $tax->labels->singular_name;
			}
		}
	}

	/**
	 * Get current taxonomy term id.
	 */
	private function get_term_id() {
		$card = \totaltheme_card_instance();
		if ( isset( $card->term ) ) {
			return $card->term->term_id ?? '';
		} elseif ( $this->is_template_edit() ) {
			return 0;
		} else {
			return get_queried_object()->term_id ?? null;
		}
	}

	/**
	 * Get current taxonomy term name.
	 */
	private function get_term_name() {
		$card = \totaltheme_card_instance();
		if ( isset( $card->term ) ) {
			return $card->term->name ?? '';
		} elseif ( $this->is_template_edit() ) {
			return "{{term_name}}";
		} else {
			$obj = get_queried_object();
			return ( \is_a( $obj, 'WP_Term' ) && isset( $obj->name ) ) ? (string) $obj->name : '';
		}
	}

	/**
	 * Get current taxonomy term description.
	 */
	private function get_term_description() {
		$card = \totaltheme_card_instance();
		if ( isset( $card->term ) ) {
			return ! empty( $card->term->description ) ? \totaltheme_render_content( $card->term->description ) : '';
		} elseif ( $this->is_template_edit() ) {
			return "{{term_description}}";
		} else {
			$obj = \get_queried_object();
			return ( \is_a( $obj, 'WP_Term' ) && isset( $obj->description ) ) ? \totaltheme_render_content( $obj->description ) : '';
		}
	}

	/**
	 * Get post count.
	 */
	private function get_post_count(): ?int {
		global $wp_query;
		return ! empty( $wp_query->found_posts ) ? (int) $wp_query->found_posts : null;
	}

	/**
	 * Get paged text.
	 */
	private function get_paged(): string {
		if ( \is_paged() ) {
			$paged = \get_query_var( 'paged' ) ?: \get_query_var( 'page' ) ?: 1;
			if ( $paged > 1 ) {
				global $wp_query;
				$max_num_pages = $wp_query->max_num_pages ?? 0;
				if ( $max_num_pages ) {
					return \sprintf( \esc_html__( 'Page %s of %s' ), $paged, $max_num_pages );
				} else {
					return \sprintf( \esc_html__( 'Page %s' ), $paged );
				}
			}
		}
		return '';
	}

	/**
	 * Get card Icon.
	 */
	private function get_card_icon(): string {
		if ( $card = \totaltheme_card_instance() ) {
			return (string) $card->get_the_icon();
		} else if ( $this->is_template_edit() ) {
			return "{{card_icon}}";
		} else {
			return '';
		}
	}

	/**
	 * Get card running count.
	 */
	private function get_card_running_count(): int {
		return ( $card = \totaltheme_card_instance() ) ? (int) $card->get_var( 'running_count' ) : 1;
	}

	/**
	 * Get custom field value.
	 */
	private function get_cf( string $var ) {
		if ( $this->is_template_edit() ) {
			return "{{{$var}}}";
		}
		$key = \str_replace( 'cf_', '', $var );
		if ( $key ) {
			if ( \function_exists( 'get_field' ) ) {
				return \get_field( $key, $this->get_acf_field_post_id() );
			}
			$card = \totaltheme_card_instance();
			if ( isset( $card->term ) ) {
				return ! empty( $card->term->term_id ) ? \get_term_meta( $card->term->term_id, $key, true ) : '';
			}
			if ( \is_tax() && ! \in_the_loop() && ! $card ) {
				return \get_term_meta( \get_queried_object_id(), $key, true );
			} else {
				return \get_post_meta( \get_the_ID(), $key, true );
			}
		}
	}

	/**
	 * Get icon.
	 */
	private function get_icon( string $name ) {
		return \totaltheme_get_icon( \str_replace( 'icon_', '', $name ) );
	}

	/**
	 * Get acf field value.
	 */
	private function get_acf( string $var ) {
		if ( ! \function_exists( 'get_field' ) || $this->is_template_edit() ) {
			return "{{{$var}}}";
		}
		if ( $key = \str_replace( 'acf_', '', $var ) ) {
			return \get_field( $key, $this->get_acf_field_post_id() ) ?: '';
		}
	}

	/**
	 * Returns the ID to use for custom fields.
	 */
	private function get_acf_field_post_id() {
		$card = \totaltheme_card_instance();
		if ( isset( $card->term ) ) {
			return $card->term ?? 0; // ACF uses the term object not the ID
		}
		if ( ! $card && ! \in_the_loop() && ( \is_tax() || \is_category() || \is_tag() ) ) {
			return \get_queried_object();
		} else {
			return \get_the_ID();
		}
	}

}
