<?php

namespace TotalThemeCore\Vcex;

\defined( 'ABSPATH' ) || exit;

/**
 * Render shortcodes with AJAX.
 */
final class Ajax {

	/**
	 * AJAX Action.
	 */
	const ACTION = 'vcex_ajax_action';

	/**
	 * Security Nonce Action.
	 */
	const NONCE = 'vcex-ajax-functions';

	/**
	 * Instance.
	 */
	private static $instance = null;

	/**
	 * Create or retrieve the instance of Scripts.
	 */
	public static function instance() {
		if ( null === static::$instance ) {
			static::$instance = new self();
		}
		return static::$instance;
	}

	/**
	 * Class Constructor.
	 */
	private function __construct() {
		\add_action( 'wp_ajax_' . self::ACTION, [ $this, 'action' ] );
		\add_action( 'wp_ajax_nopriv_' . self::ACTION, [ $this, 'action' ] );
	}

	/**
	 * Checks if we are currently making an ajax request.
	 */
	public function is_doing_ajax(): bool {
		return ! empty( $_REQUEST['action'] ) && self::ACTION === $_REQUEST['action'];
	}

	/**
	 * Register scripts.
	 */
	public function register_scripts(): void {
		$dependencies = [
		//	'imagesloaded' // @deprecated in 5.10 - this makes the load more seem slower and it's only needed for isotope.
		];

		/**
		 * Filters the ajax script dependencies.
		 */
		$dependencies = (array) \apply_filters( 'vcex_ajax_script_dependencies', $dependencies );

		\wp_register_script(
			'vcex-ajax',
			\vcex_get_js_file( 'frontend/ajax' ),
			$dependencies,
			TTC_VERSION,
			true
		);

		$ajaxl10n = [
			'ajax_url'        => \esc_url( \set_url_scheme( \admin_url( 'admin-ajax.php' ) ) ),
			'nonce'           => \wp_create_nonce( self::NONCE ),
			// @note in 2.0 we switched to using JS memory instead to prevent cross page issues and dynamic queries.
			'session_storage' => (int) \apply_filters( 'vcex_ajax_use_session_storage', ! vcex_vc_is_inline() ),
			'url_sort_prefix' => Url_Sort_Query::get_prefix(),
		];

		if ( \in_array( 'imagesloaded', $dependencies ) ) {
			$ajaxl10n['wait_for_images'] = '1';
		}

		\wp_localize_script(
			'vcex-ajax',
			'vcex_ajax_params',
			$ajaxl10n
		);
	}

	/**
	 * Enqueue scripts.
	 */
	public function enqueue_scripts( $shortcode_class = '', $shortcode_atts = [] ): void {
		\do_action( 'vcex_ajax_enqueue_scripts', [
			'shortcode_class' => $shortcode_class,
			'shortcode_atts'  => $shortcode_atts,
		] );

		/**
		 * Filters whether the wp media scripts should load when using ajax.
		 *
		 * @param bool $maybe_enqueue_media_elements
		 */
		$maybe_enqueue_media_elements = (bool) \apply_filters( 'vcex_loadmore_enqueue_mediaelement', false );

		if ( $maybe_enqueue_media_elements ) {
			\wp_enqueue_style( 'wp-mediaelement' );
			\wp_enqueue_script( 'wp-mediaelement' );
		}

		\wp_enqueue_script( 'vcex-ajax' );
	}

	/**
	 * The ajax action.
	 */
	public function action(): void {
		\check_ajax_referer( self::NONCE, 'nonce' );

		if ( empty( $_POST['shortcodeAtts'] ) || empty( $_POST['shortcodeClass'] ) ) {
			\wp_die();
		}

		$data            = [];
		$shortcode_class = $_POST['shortcodeClass'];
		$shortcode_atts  = $_POST['shortcodeAtts'];
		$extra_data      = $_POST['data'] ?? null;

		if ( $extra_data ) {
			$extra_data = \json_decode( \stripslashes( $extra_data ), true );
			$return = $extra_data['return'] ?? '';
		}

		$allowed_classes = [
			'Wpex_Post_Cards_Shortcode',
		];

		if ( ! \in_array( $shortcode_class, $allowed_classes ) ) {
			\wp_die();
		}

		if ( \is_callable( [ 'WPBMap', 'addAllMappedShortcodes' ] ) ) {
			\WPBMap::addAllMappedShortcodes(); // Fix for WPBakery not working in ajax.
		}

		$shortcode_atts = \json_decode( \stripslashes( $shortcode_atts ), true );

		if ( 'Wpex_Post_Cards_Shortcode' === $shortcode_class ) {
			$data = [];

			if ( isset( $extra_data['ignore_tax_query'] ) ) {
				$shortcode_atts['ignore_tax_query'] = $extra_data['ignore_tax_query'];
			}

			if ( empty( $return ) || $return !== 'filter_counts' ) {
				$post_cards = new Post_Cards( $shortcode_atts );
				$data['html'] = $post_cards->get_output();
			}

			// Get counters.
			if ( ! empty( $extra_data['filter'] )
				&& isset( $extra_data['update_counts'] )
				&& '1' === $extra_data['update_counts']
			) {
				if ( isset( $post_cards ) ) {
					$shortcode_atts = $post_cards->get_atts();
				} else {
					// This is VERY important.
					$shortcode_atts = \vcex_shortcode_atts( 'wpex_post_cards', $shortcode_atts, 'Wpex_Post_Cards_Shortcode' );
				}
				$term_counts = $this->get_term_counts( $extra_data['filter'], $shortcode_atts );
				$data['counts'] = \wp_json_encode( $term_counts );
			}

		} else {
			$data = [
				'html' => $_POST['shortcodeClass']::output( $shortcode_atts ),
			];
		}

		\wp_send_json_success( \wp_send_json_success( $data ) );

		\wp_die();
	}

	/**
	 * Returns the ajax loader element.
	 *
	 */
	public function get_ajax_loader(): string {
		$html = '<div class="vcex-ajax-loader wpex-hidden wpex-absolute wpex-inset-0 wpex-items-center wpex-justify-center">';
			$html .= '<span class="vcex-ajax-loader__overlay wpex-absolute wpex-inset-0 wpex-surface-1 wpex-opacity-60 wpex-z-10"></span>';
			if ( \is_callable( '\TotalTheme\Pagination\Load_More::get_loader_svg_html' ) ) {
				$icon_size  = \get_theme_mod( 'ajax_loader_svg_size' ) ?: 30;
				$svg_icon = \TotalTheme\Pagination\Load_More::get_loader_svg_html( '', (int) $icon_size );
				$svg = \apply_filters( 'vcex_ajax_loader_svg_icon', $svg_icon );
				$html .= '<div class="vcex-ajax-loader__icon wpex-relative wpex-z-20">' . $svg_icon . '</div>';
			}
		$html .= '</div>';
		return $html;
	}

	/**
	 * Returns the load more button.
	 */
	public function get_loadmore_button( $shortcode_tag, $shortcode_atts = [], $query = '', $infinite_scroll = false ) {
		if ( \is_array( $shortcode_tag ) ) {
			extract( $shortcode_tag );
		}

		$page         = \get_query_var( 'paged' ) ?: 1;
		$max_pages    = $query->max_num_pages;
		$vc_is_inline = \vcex_vc_is_inline();

		// No need for load more if we already reached the last page.
		if ( $page >= $max_pages || ( $infinite_scroll && $vc_is_inline ) ) {
			return;
		}

		// Remove useless attributes.
		unset( $shortcode_atts['wrap_css'] );
		unset( $shortcode_atts['show_categories_tax'] );

		// @todo is this needed?
		if ( ! \in_array( $shortcode_tag, [ 'vcex_post_type_archive', 'vcex_post_type_grid', 'vcex_recent_news' ] ) ) {
			unset( $shortcode_atts['post_type'] );
			unset( $shortcode_atts['taxonomy'] );
		}

		// Define load more text.
		$loading_text = \esc_html__( 'Loading&hellip;', 'total-theme-core' );
		$failed_text  = \esc_html__( 'Failed to load posts.', 'total-theme-core' );

		if ( empty( $loadmore_text ) ) {
			if ( \is_callable( [ 'TotalTheme\Pagination\Load_More', 'get_more_text' ] ) ) {
				$loadmore_text = \TotalTheme\Pagination\Load_More::get_more_text();
			} else {
				$loadmore_text = esc_html__( 'Load More', 'total-theme-core' );
			}
		}

		if ( \is_callable( [ 'TotalTheme\Pagination\Load_More', 'get_loading_text' ] ) ) {
			$loading_text = \TotalTheme\Pagination\Load_More::get_loading_text();
		}

		if ( \is_callable( [ 'TotalTheme\Pagination\Load_More', 'get_failed_text' ] ) ) {
			$failed_text = \TotalTheme\Pagination\Load_More::get_failed_text();
		}

		// Create array of load more settings to be added to the button data.
		$settings = [
			'class'           => 'vcex-loadmore-button theme-button',
			'text'            => $loadmore_text,
			'loading_text'    => $loading_text,
			'failed_text'     => $failed_text,
			'infinite_scroll' => $infinite_scroll,
		];

		if ( \is_callable( '\TotalTheme\Pagination\Load_More::get_loader_svg_html' ) ) {
			$settings['svg'] = \TotalTheme\Pagination\Load_More::get_loader_svg_html();
		} else {
			$settings['gif'] = includes_url( 'images/spinner-2x.gif' );
		}

		$settings = \apply_filters( 'vcex_get_loadmore_button_settings', $settings, $shortcode_tag, $shortcode_atts ); // @deprecated
		$settings = (array) \apply_filters( 'vcex_loadmore_button_settings', $settings, $shortcode_tag, $shortcode_atts );

		// Check if infinite scroll is enabled.
		$has_infinite_scroll = \wp_validate_boolean( $settings['infinite_scroll'] ?? false );

		if ( $vc_is_inline && $has_infinite_scroll  ) {
			return;
		}

		// Load more classes.
		$loadmore_classes = [
			'vcex-loadmore',
			'wpex-clear',
			'wpex-text-center',
		];

		if ( 'wpex_post_cards' === $shortcode_tag || ! vcex_has_classic_styles() ) {
			$loadmore_classes[] = 'wpex-mt-30';
		} else {
			$loadmore_classes[] = 'wpex-mt-10';
		}

		if ( $has_infinite_scroll ) {
			$loadmore_classes[] = 'vcex-loadmore--infinite-scroll wpex-invisible';
		}

		$loadmore_classes = (array) \apply_filters( 'vcex_loadmore_class', $loadmore_classes, $shortcode_tag, $shortcode_atts );

		// Return load more button.
		$button = '<div class="' . \esc_attr( \implode( ' ', $loadmore_classes ) ) . '">';

			$button_class = $settings['class'] ?? '';

			if ( $has_infinite_scroll ) {
				if ( is_string( $button_class ) ) {
					$button_class .= ' wpex-h-1px';
				} else {
					$button_class[] = 'wpex-h-1px';
				}
			}

			$btn_attr = [
				'href'                  => '#',
				'class'                 => \esc_attr( $button_class ),
				'data-infinite-scroll'  => (int) $has_infinite_scroll,
				'data-text'             => \esc_attr( $settings['text'] ),
				'data-loading-text'     => \esc_attr( $settings['loading_text'] ),
				'data-failed-text'      => \esc_attr( $settings['failed_text'] ),
			];

			// Add extra date for elements (not needed for Post Cards).
			if ( 'wpex_post_cards' !== $shortcode_tag ) {
				$btn_attr['data-page']             = \esc_attr( $page );
				$btn_attr['data-max-pages']        = \esc_attr( $max_pages );
				$btn_attr['data-nonce']            = \esc_attr( \wp_create_nonce( 'vcex-ajax-pagination-nonce' ) );
				$btn_attr['data-shortcode-tag']    = \esc_attr( $shortcode_tag );
				if ( $this->is_auto_query( $shortcode_atts ) ) {
					$shortcode_atts['query_vars'] = \wp_json_encode( $query->query_vars );
				}
				$btn_attr['data-shortcode-params'] = \esc_attr( \wp_json_encode( $shortcode_atts, false ) );
			}

			$button .= '<a';
				foreach ( $btn_attr as $name => $value_escaped ) {
		            $button .= ' ' . $name . '="' .  $value_escaped . '"';
		        }
			$button .= '>';

				$button_text_allowed_tags = [
					'img' => [
						'src' => [],
						'alt' => [],
					],
					'span' => [
						'class' => [],
					],
				];

				$button_text_allowed_tags = \apply_filters( 'vcex_loadmore_button_text_allowed_tags', $button_text_allowed_tags );

				$button .= '<span class="vcex-txt">' . \wp_kses( $settings['text'], $button_text_allowed_tags ) . '</span>';

			$button .= '</a>';

			// Spinner.
			if ( $has_infinite_scroll ) {
				$spinner_class = 'vcex-loadmore-spinner vcex-spinner wpex-invisible wpex-opacity-0';
			} else {
				$spinner_class = 'vcex-loadmore-spinner vcex-spinner wpex-hidden';
			}
			if ( ! empty( $settings['gif'] ) ) {
				$button .= '<img src="' . \esc_url( $settings['gif'] ) . '" class="' . $spinner_class . ' wpex-opacity-40" alt="' . \esc_attr( $settings['loading_text'] ) . '" height="20" width="20">';
			} elseif ( ! empty( $settings['svg'] ) ) {
				$button .= '<div class="' . $spinner_class . '">' . $settings['svg'] . '</div>';
			}

		$button .= '</div>';

		return $button;
	}

	/**
	 * Check if we are showing an auto query.
	 */
	protected function is_auto_query( $atts = [] ): bool {
		if ( isset( $atts['query_type'] ) ) {
			return 'auto' === $atts['query_type'];
		} else {
			return \vcex_validate_att_boolean( 'auto_query', $atts );
		}
	}

	/**
	 * Returns updated taxonomy counters based on ajax request.
	 */
	protected function get_term_counts( $filter_items = '', $shortcode_atts = '' ) {
		if ( ! \is_array( $filter_items ) || empty( $filter_items ) ) {
			return;
		}

		$counts = [];

		foreach ( $filter_items as $taxonomy => $terms ) {
			if ( \taxonomy_exists( $taxonomy ) ) {
				foreach ( $terms as $term_id ) {
					$counts_key = $taxonomy .'|' . $term_id;
					$counts[ $counts_key ] = 0;
				}
			} else {
				unset( $filter_items );
			}
		}

		if ( empty( $counts ) ) {
			return;
		}

		$posts_per_page = ( isset( $shortcode_atts['query_type'] ) && 'auto' === $shortcode_atts['query_type'] ) ? 9999 : -1;

		$shortcode_atts['unfiltered_query_args'] = [
			'posts_per_page' => $posts_per_page, // @note using -1 in auto queries causes an error in the WP query.
		];

		$get_posts = \vcex_build_wp_query( $shortcode_atts, 'wpex_post_cards', 'ids' );
		if ( $get_posts->posts ) {
			if ( empty( $shortcode_atts['ajax_filter'] ) ) {
				$counts['all'] = \count( $get_posts->posts );
			}
			$include_children = $shortcode_atts['ajax_filter']['include_children'] ?? true;
			foreach ( $get_posts->posts as $post_id ) {
				foreach ( $filter_items as $taxonomy => $terms ) {
					foreach ( $terms as $term_id ) {
						$term_id_array = [ $term_id ];
						$counts_key = $taxonomy .'|' . $term_id;
						if ( $include_children ) {
							$children = \get_term_children( $term_id, $taxonomy );
							if ( $children ) {
								$term_id_array = \array_merge( $term_id_array, $children );
							}
						}
						if ( \has_term( $term_id_array, $taxonomy, $post_id ) ) {
							$counts[ $counts_key ]++;
						}
					}
				}
			}
		} else {
			$counts = 'empty';
		}

		return $counts;
	}

	/**
	 * Parses a multi attribute returned by the AJAX filter.
	 *
	 * @deprecated 1.6.1
	 */
	public function parse_ajax_filter_multi_attribute_value( $value ): array {
		$result = [];
		$params_pairs = \explode( '|', $value );
		if ( ! empty( $params_pairs ) ) {
			foreach ( $params_pairs as $pair ) {
				$param = \preg_split( '/\:/', $pair );
				if ( ! empty( $param[0] ) && isset( $param[1] ) ) {
					$result[ $param[0] ] = $param[1];
				}
			}
		}
		return $result;
	}

}
