<?php

namespace TotalTheme\Pagination;

use WP_Query;

\defined( 'ABSPATH' ) || exit;

/**
 * Load more pagination.
 */
class Load_More {

	/**
	 * Query var used to store loadmore POST data.
	 */
	public const QUERY_VAR = 'wpex_loadmore_data';

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Checks if we are currently fetching posts.
	 */
	public static function is_doing_ajax(): bool {
		return ! empty( $_REQUEST['action'] ) && 'wpex_ajax_load_more' === $_REQUEST['action'];
	}

	/**
	 * Returns load more data.
	 *
	 * @todo prefix loadmore.
	 */
	public static function get_data( $key = '' ) {
		if ( empty( $_POST['loadmore'] ) || ! self::is_doing_ajax() ) {
			return;
		}
		$data = \get_query_var( self::QUERY_VAR );
		if ( $key ) {
			return $data[ $key ] ?? '';
		}
		return $data;
	}

	/**
	 * Enqueues load more scripts.
	 */
	public static function enqueue_scripts(): void {
		// Make sure possibly needed scripts are loaded.
		// @todo can this be optimized somehow?
		\wpex_enqueue_slider_pro_scripts();
		\wpex_enqueue_lightbox_scripts();

		// WP Media.
		if ( \apply_filters( 'wpex_loadmore_enqueue_mediaelement', false ) ) {
			\wp_enqueue_style( 'wp-mediaelement' );
			\wp_enqueue_script( 'wp-mediaelement' );
		}

		\wp_enqueue_script(
			'wpex-loadmore',
			\totaltheme_get_js_file( 'frontend/loadmore' ),
			[ 'jquery', 'imagesloaded' ],
			\WPEX_THEME_VERSION,
			[
				'strategy' => 'defer',
			]
		);

		\wp_localize_script(
			'wpex-loadmore',
			'wpex_loadmore_params',
			[
				'ajax_url' => \set_url_scheme( \admin_url( 'admin-ajax.php' ) ),
				'i18n'     => [
					'text'        => esc_js( self::get_more_text() ),
					'loadingText' => esc_js( self::get_loading_text() ),
					'failedText'  => esc_js( self::get_failed_text() ),
				],
			]
		);
	}

	/**
	 * Returns an array of loader options.
	 */
	public static function get_loader_svg_options(): array {
		return [
			''                 => \esc_html__( 'Default', 'total' ),
			'ball-triangle'    => \esc_html__( 'Ball Triangle', 'total' ),
			'bars'             => \esc_html__( 'Bars', 'total' ),
			'circles'          => \esc_html__( 'Circles', 'total' ),
			'grid'             => \esc_html__( 'Grid', 'total' ),
			'oval'             => \esc_html__( 'Oval', 'total' ),
			'puff'             => \esc_html__( 'Puff', 'total' ),
			'rings'            => \esc_html__( 'Rings', 'total' ),
			'spinning-circles' => \esc_html__( 'Spinning Circles', 'total' ),
			'tail-spin'        => \esc_html__( 'Tail Spin', 'total' ),
			'three-dots'       => \esc_html__( 'Three Dots', 'total' ),
			'wordpress'        => 'WordPress',
		];
	}

	/**
	 * Returns loader gif.
	 */
	public static function get_loader_gif(): string {
		return (string) \apply_filters( 'wpex_loadmore_gif', null );
	}

	/**
	 * Returns loader svg.
	 */
	public static function get_loader_svg(): string {
		$svg = ( $svg = \get_theme_mod( 'loadmore_svg' ) ) ? \sanitize_text_field( $svg ) : '';
		if ( $svg && ! \array_key_exists( $svg, self::get_loader_svg_options() ) ) {
			$svg = 'default';
		}
		return $svg;
	}

	/**
	 * Returns loader svg html.
	 */
	public static function get_loader_svg_html( string $svg = '', int $size = 0 ): string {
		if ( ! $svg ) {
			$svg = self::get_loader_svg();
		}
		if ( ! $size ) {
			$size = ( $custom_size = \get_theme_mod( 'loadmore_svg_size' ) ) ? \absint( $custom_size ) : '';
			if ( ! $size ) {
				$size = totaltheme_has_classic_styles() ? 20 : 24;
			}
		}
		return (string) \totaltheme_get_loading_icon( $svg, $size );
	}

	/**
	 * Returns the more text.
	 */
	public static function get_more_text(): string {
		$text = ( $text = \wpex_get_translated_theme_mod( 'loadmore_text' ) ) ? \sanitize_text_field( $text ) : \esc_html__( 'Load More', 'total' );
		$text = \apply_filters( 'wpex_loadmore_text', $text ); // @deprecated
		return (string) \apply_filters( 'totaltheme/pagination/load_more/button_text', $text );
	}

	/**
	 * Returns the loading text.
	 */
	public static function get_loading_text(): string {
		$text = ( $text = \wpex_get_translated_theme_mod( 'loadmore_loading_text' ) ) ? \sanitize_text_field( $text ) : \esc_html__( 'Loading...', 'total' );
		$text = \apply_filters( 'wpex_loadmore_loading_text', $text ); // @deprecated
		return (string) \apply_filters( 'totaltheme/pagination/load_more/loading_text', $text );
	}

	/**
	 * Returns the failed text.
	 */
	public static function get_failed_text(): string {
		$text = ( $text = \wpex_get_translated_theme_mod( 'loadmore_failed_text' ) ) ? \sanitize_text_field( $text ) : \esc_html__( 'Failed to load posts.', 'total' );
		$text = \apply_filters( 'wpex_loadmore_failed_text', $text ); // @deprecated
		return (string) \apply_filters( 'totaltheme/pagination/load_more/failed_text', $text );
	}

	/**
	 * Render button.
	 */
	public static function render_button( $args = [] ): void {
		$page = \get_query_var( 'paged' ) ?: 1;

		global $wp_query;

		// define load more default args.
		$defaults = [
			'grid'            => '#blog-entries',
			'loop_type'       => 'blog',
			'nonce'           => \wp_create_nonce( 'wpex-load-more-nonce' ),
			'page'            => $page,
			'maxPages'        => $wp_query->max_num_pages,
			'infinite_scroll' => false,
			'count'           => \wpex_get_loop_counter(),
			'query_vars'      => \wp_json_encode( $wp_query->query_vars ),

			// @todo deprecate?
			'is_home'         => \is_home(),
			'category'        => \is_category() ? \get_query_var( 'cat' ) : false, // is this being used anywhere?
		];

		/**
		 * Add current term_id to args.
		 * Required for meta field checks (custom term settings).
		 */
		if ( \is_category() || \is_tag() || \is_tax() ) {
			$defaults['term_id'] = \get_queried_object_id();
		}

		$args                = \wp_parse_args( $args, $defaults );
		$max_pages           = \absint( $args['maxPages'] ?? 1 );
		$has_infinite_scroll = \wp_validate_boolean( $args['infinite_scroll'] ?? false );

		if ( ! \wp_doing_ajax() && ( ! $max_pages || 1 === $max_pages || ( $max_pages == $page ) ) ) {
			return; // there isn't any pagination.
		}

		self::enqueue_scripts();

		$output = '';

		$output .= '<div class="wpex-load-more-wrap wpex-mt-30 wpex-text-center">';

			$button_class = 'wpex-load-more';

			if ( $has_infinite_scroll ) {
				$button_class .= ' wpex-load-more--infinite-scroll wpex-h-1px wpex-invisible';
			}

			if ( \get_theme_mod( 'loadmore_btn_expanded', true ) ) {
				$button_class .= ' wpex-load-more--expanded wpex-w-100';
			}

			$button_class .= ' theme-button';

			if ( \totaltheme_has_classic_styles() ) {
				$button_class .= ' wpex-py-0';
			}

			$output .= '<a href="#" class="' . \esc_attr( $button_class ) . '" data-loadmore="' . \htmlentities( \wp_json_encode( $args ) ) . '">';

				$output .= '<span class="theme-button-inner">' . \esc_html( self::get_more_text() ) . '</span>';

			$output .= '</a>';

			$gif = self::get_loader_gif();
			$spinner_class = 'wpex-load-more-spinner wpex-spinner wpex-hidden';

			if ( $gif ) {
				$output .= '<img src="' . \esc_url( $gif ) . '" class="' . \esc_attr( $spinner_class ) . '" alt="' . \esc_attr( self::get_loading_text() ) . '" height="20" width="20">';
			} elseif ( $svg = self::get_loader_svg_html() ) {
				$output .= '<div class="' . \esc_attr( $spinner_class ) . '">' . $svg . '</div>';
			}

			$output .= '</div>';

		echo $output; // @codingStandardsIgnoreLine
	}

	/**
	 * Get posts via AJAX.
	 */
	public static function get_posts() {
		check_ajax_referer( 'wpex-load-more-nonce', 'nonce' );

		if ( empty( $_POST['loadmore'] ) || ! \is_array( $_POST['loadmore'] ) ) {
			\wp_die();
		}

		$loadmore = $_POST['loadmore'];
		//$loadmore = json_decode( html_entity_decode( stripslashes( $_POST['loadmore'] ) ), true );

		$query_args = $loadmore['query_vars'] ?? $loadmore['query'] ?? [];
		if ( \is_string( $query_args ) ) {
			$query_args = \json_decode( \stripslashes_deep( $query_args ), true );
			$loadmore['query_vars'] = $query_args; // update array.
		}

		if ( ! \is_array( $query_args ) ) {
			return;
		}

		// Make sure we are only querying published posts since WP doesn't know we are on the front-end.
		$query_args['post_status'] = 'publish';

		// Updated query paged argument.
		$query_args['paged'] = \absint( $_POST['page'] ?? 2 );

		\wpex_set_loop_counter( $loadmore['count'] ?? 0 );

		// Update load more data.
		\set_query_var( self::QUERY_VAR, $loadmore );

		$loop_type = $loadmore['loop_type'] ?? 'blog';

		$posts = new WP_Query( $query_args );

		\ob_start();

			if ( $posts->have_posts() ) :
				while ( $posts->have_posts() ): $posts->the_post();
					\get_template_part( 'partials/loop/loop', $loop_type );
				endwhile;
			endif;

			// used to update wpex_set_loop_counter()
			echo '<div class="wpex-hidden" data-count="' . \esc_attr( \wpex_get_loop_counter() ) . '"></div>';

			\wp_reset_postdata();

			\set_query_var( self::QUERY_VAR, null );

		$data = \ob_get_clean();

		\wp_send_json_success( $data );

		\wp_die();
	}

}
