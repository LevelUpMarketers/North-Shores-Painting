<?php

namespace TotalTheme\Helpers;

\defined( 'ABSPATH' ) || exit;

/**
 * Advanced post excerpt generator.
 */
class Post_Excerpt_Generator {

	/**
	 * Stores the excerpt_type.
	 */
	protected $excerpt_type = 'custom';

	/**
	 * Args.
	 */
	protected $args = [
		'post_id'              => '',
		'length'               => 30,
		'trim_by'              => 'words',
		'more'                 => '&hellip;',
		'before'               => '',
		'after'                => '',
		'search_query'         => '',
		'extract_excerpt'      => true,
		'extract_search_query' => false,
		'use_excerpt_field'    => true,
		'use_meta_description' => false,
		'trim_excerpt_field'   => false,
	];

	/**
	 * Constructor.
	 */
	public function __construct( array $args ) {
		$this->args = \wp_parse_args( $args, $this->args );

		if ( ! $this->args['post_id'] ) {
			$this->args['post_id'] = \get_the_id();
		}
	
		$this->args = \apply_filters( 'wpex_excerpt_args', $this->args, $this->args['context'] ?? '' );
		$this->args = \apply_filters( 'totaltheme/post/excerpt/args', $this->args );
		$this->parse_args();
	}

	/**
	 * Returns the excerpt.
	 */
	public function get_excerpt(): string {
		if ( isset( $this->args['custom_output'] ) ) {
			 // bail early (unsanitized) if we are passing a custom excerpt.
			return $this->args['before'] . $this->args['custom_output'] . $this->args['after'];
		} else {
			if ( 0 === $this->args['length'] ) {
				return '';
			}
			$excerpt = $this->extract_excerpt();
		}

		if ( $excerpt ) {
			$excerpt = \totaltheme_replace_vars( $excerpt );
		}

		if ( 'protected' === $this->excerpt_type || 'post_content' === $this->excerpt_type ) {
			return $excerpt; // bail early for these types
		}

		if ( $excerpt ) {
			if ( ! empty( $this->args['readmore'] ) ) {
				$excerpt = $excerpt . $this->add_readmore();
			}
			if ( \str_contains( $excerpt, '.&hellip;' ) ) {
				$excerpt = \str_replace( '.&hellip;', '&hellip;', $excerpt ); // prevents period followed by ...
			}
			if ( ! \str_contains( $excerpt, '<p>' ) ) {
				$excerpt = '<p>' . $excerpt . '</p>'; // wrap all excerpts inside <p> tags.
			}
		}

		$excerpt = \apply_filters( 'wpex_excerpt_output', $excerpt, $this->args );
		$excerpt = (string) \apply_filters( 'totaltheme/post/excerpt', $excerpt, $this->args['post_id'], $this->args );
		return $this->args['before'] . $excerpt . $this->args['after'];
	}

	/**
	 * Extract post excerpt.
	 */
	protected function extract_excerpt(): ?string {
		$post = \get_post( $this->args['post_id'] );

		if ( ! $this->args['search_query'] ) {
			// Get post content up to the more tag.
			// Note: We can use get_the_content() if we are also viewing the same post or we will get a memory error.
			if ( 9999 === $this->args['length'] && ! $this->is_current_page() ) {
				// @note if the first param is null it will insert the default WP (more) link.
				$post_content = \get_the_content( '', $this->get_more(), $post );
			}
			// Get the full post content.
			if ( -1 === $this->args['length'] ) {
				$post_content = $post->post_content ?? '';
			}
			if ( isset( $post_content ) ) {
				$this->excerpt_type = 'post_content';
				return (string) \apply_filters( 'the_content', $post_content );
			}
		}

		if ( $this->args['use_excerpt_field'] && $custom_excerpt = $this->get_post_excerpt( $post ) ) {
			if ( ! $this->args['search_query'] || $this->string_has_search_query( $custom_excerpt ) ) {
				$this->excerpt_type = 'post_excerpt';
				if ( ! empty( $this->args['custom_excerpts_more'] ) ) { 
					$custom_excerpt = $custom_excerpt . $this->get_more();
				}
				return $custom_excerpt;
			}
		}

		if ( $this->args['use_meta_description'] && $meta_desc = $this->get_meta_description() ) {
			if ( ! $this->args['search_query'] || $this->string_has_search_query( $meta_desc ) ) {
				$this->excerpt_type = 'meta_description';
				return $meta_desc;
			}
		}

		if ( 'private' === \get_post_status( $this->args['post_id'] ) || \post_password_required( $post ) ) {
			$this->excerpt_type = 'protected';
			return '<p>' . \esc_html__( 'There is no excerpt because this is a protected post.' ) . '</p>';
		}

		if ( 9999 === $this->args['length'] || -1 === $this->args['length'] ) {
			$this->args['length'] = 30;
		}

		if ( ! $this->args['extract_excerpt'] || empty( $post->post_content ) ) {
			return '';
		}

		$post_content = (string) $post->post_content;

		if ( $this->args['search_query']
			&& $this->string_has_search_query( $post_content )
			&& $search_query_text = $this->get_text_with_search_query( $post_content )
		) {
			$post_content = $search_query_text;
		}

		if ( \str_contains( $post_content, '[vc_column_text' ) ) {
			$this->excerpt_type = 'vc_column_text';
			\preg_match( '{\[vc_column_text.*?\](.*?)\[/vc_column_text\]}is', $post_content, $matches );
			if ( ! empty( $matches[1] ) ) {
				$post_content = $matches[1];
			}
		}

		// Sanitize and return trimmed excerpt.
		$post_content = \strip_shortcodes( $post_content ); // don't allow any shortcodes from generated content

		if ( ! $post_content ) {
			return '';
		}

		$excerpt = $this->trim_excerpt( $post_content ); // this function will strip out all tags.

		return $excerpt;
	}

	/**
	 * Get meta description.
	 */
	protected function get_meta_description(): string {
		if ( \defined( 'WPSEO_VERSION' ) && $meta_desc = \get_post_meta( $this->args['post_id'], '_yoast_wpseo_metadesc', true ) ) {
			if ( \function_exists( 'wpseo_replace_vars' ) ) {
				$meta_desc = \wpseo_replace_vars( $meta_desc, [] );
			}
			$meta_desc = $this->sanitize_excerpt( $this->do_shortcode( $meta_desc ) );
		}
		return (string) ($meta_desc ?? '');
	}

	/**
	 * Returns the post excerpt.
	 *
	 * @note We don't use get_the_excerpt beause it won't return excerpts for private posts and it should for
	 * SEO and usability. Also in new versions of WP the get_the_excerpt() filter will always return an excerpt
	 * even if a custom one isn't set!
	 */
	protected function get_post_excerpt( $post ): string {
		if ( ! empty( $post->post_excerpt ) ) {
			$post_excerpt = $post->post_excerpt;
			if ( $this->args['trim_excerpt_field'] && -1 !== $this->args['length'] ) {
				$excerpt_length = \str_word_count( \wp_strip_all_tags( $post_excerpt ) );
				if ( $excerpt_length > $this->args['length'] ) {
					$post_excerpt = $this->trim_excerpt( $post_excerpt );
				}
			}
			return (string) \apply_filters( 'get_the_excerpt', $this->sanitize_excerpt( $this->do_shortcode( $post_excerpt ) ), $post );
		}
		return '';
	}

	/**
	 * Adds p tags.
	 */
	protected function wpautop( string $string ): string {
		if ( ! \str_contains( '<p', $string ) ) {
			$string = (string) \wpautop( $string );
		}
		return $string;
	}

	/**
	 * Do shortcode wrapper.
	 */
	protected function do_shortcode( string $string ): string {
		return (string) \do_shortcode( \totaltheme_shortcode_unautop( $string ) );
	}

	/**
	 * Trims the excerpt.
	 */
	protected function trim_excerpt( $excerpt ): string {
		if ( 'words' === $this->args['trim_by'] ) {
			return \wp_trim_words( $excerpt, $this->args['length'], $this->get_more() );
		} else {
			$excerpt = \wp_strip_all_tags( $excerpt );
			$excerpt = \preg_replace( "/[\n\r\t ]+/", ' ', $excerpt );
			return \mb_strimwidth( $excerpt, 0, $this->args['length'], $this->get_more() );
		}
	}

	/**
	 * Parses args to deal with renamed args.
	 */
	protected function parse_args(): void {
		if ( isset( $this->args['trim_type'] ) ) {
			$this->args['trim_by'] = $this->args['trim_type'];
		}
		if ( isset( $this->args['generate_excerpts'] ) ) {
			$args['extract_excerpt'] = $this->args['generate_excerpts'];
		}
		if ( isset( $this->args['custom_excerpts'] ) ) {
			$this->args['use_excerpt_field'] = $this->args['custom_excerpts'];
		}
		if ( isset( $this->args['trim_custom_excerpts'] ) ) {
			$this->args['trim_excerpt_field'] = $this->args['trim_custom_excerpts'];
		}
		$this->args['length'] = (int) $this->args['length'];
	}

	/**
	 * Helper check if the excerpt we are getting excerpts for is the current page.
	 */
	protected function is_current_page(): bool {
		return (int) $this->args['post_id'] === (int) wpex_get_current_post_id();
	}

	/**
	 * String has search query.
	 */
	protected function string_has_search_query( string $string ): bool {
		return \str_contains( \strtolower( $string ), \strtolower( $this->args['search_query'] ) );
	}

	/**
	 * Returns the string that contains the search query.
	 */
	protected function get_text_with_search_query( $post_content ) {
		$search_query = \preg_quote( $this->args['search_query'], '/' );
		// This is an expensive process.
		if ( true === $this->args['extract_search_query'] ) {
			if ( \str_contains( $post_content, '[' ) ) {
				$post_content = $this->do_shortcode( $post_content );
			}
			if ( \str_contains( $post_content, '<!-- wp:' ) ) {
				$post_content = \do_blocks( $post_content );
			}
		}
		\preg_match( "/([^\n]*{$search_query}[^\n]*)/i", $this->wpautop( \wp_strip_all_tags( \strip_shortcodes( $post_content ) ) ), $matches );
		if ( ! empty( $matches[0] ) ) {
			return $matches[0];
		}
	}

	/**
	 * Appends a read more link to the excerpt.
	 *
	 * This is a soft deprecated function.
	 */
	protected function add_readmore(): string {
		$readmore = '';
		if ( $permalink = \get_permalink( $this->args['post_id'] ) ) {
			$read_more_text = $this->args['read_more_text'] ?? \esc_html__( 'Read more', 'total' );
			$readmore = '<a href="' . \esc_url( $permalink ) . '" class="wpex-readmore theme-button">' . \esc_html( $read_more_text ) . '</a>';
			$readmore = (string) apply_filters( 'wpex_excerpt_more_link', $readmore ); // @deprecated
		}
		return $readmore;
	}

	/**
	 * Sanitize excerpt.
	 */
	protected function sanitize_excerpt( $excerpt ): string {
		return \totaltheme_is_live_search() ? \wp_strip_all_tags( $excerpt ) : \wp_kses_post( $excerpt );
	}

	/**
	 * Returns the excerpt "more".
	 */
	protected function get_more(): string {
		return (string) $this->args['more'];
	}

}
