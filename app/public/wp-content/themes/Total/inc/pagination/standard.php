<?php

namespace TotalTheme\Pagination;

\defined( 'ABSPATH' ) || exit;

/**
 * Standard Pagination.
 */
class Standard {

	/**
	 * The current query.
	 */
	protected $query = null;

	/**
	 * The pagination args.
	 */
	protected $args = null;

	/**
	 * Constructor.
	 */
	public function __construct( $query = null )  {
		if ( $query ) {
			$this->query = $query;
		} else {
			global $wp_query;
			$this->query = $wp_query;
		}
	}

	/**
	 * Renders the pagination.
	 */
	public function render() {
		$args = $this->get_args();

		if ( ! $args ) {
			return;
		}

		$class = 'wpex-pagination';

		if ( $align = $this->get_align_class() ) {
			$class .= " {$align}";
		}

		$class .= ' wpex-clear wpex-mt-30';

		echo '<div class="' . \esc_attr( $class ) . '">' . \paginate_links( $args ) . '</div>';
	}

	/**
	 * Returns pagination args.
	 */
	protected function get_args(): array  {
		if ( \is_array( $this->args ) ) {
			return $this->args;
		}

		$arrow_icon  = ( $arrow_icon = \get_theme_mod( 'pagination_arrow' ) ) ? \sanitize_html_class( $arrow_icon ) : 'angle';
		$arrow_class = 'page-numbers-icon';
		$arrow_size  = 'angle' === $arrow_icon ? 'xs' : ''; // @todo add option for this.
		$arrow_bidi  = true;
		$prev_arrow  = "{$arrow_icon}-left";
		$next_arrow  = "{$arrow_icon}-right";

		// Set vars.
		$total = (int) $this->query->max_num_pages;
		$big   = 999999999;

		// Pagination disabled.
		if ( empty( $total ) || 1 === $total ) {
			return [];
		}

		// Get current page.
		$current_page = 1;
		if ( ! empty( $this->query->query['paged'] ) ) {
			$current_page = $this->query->query['paged'];
		} elseif ( \get_query_var( 'paged' ) ) {
			$current_page = get_query_var( 'paged' );
		} elseif ( \get_query_var( 'page' ) ) {
			$current_page = \get_query_var( 'page' );
		}

		// Get permalink structure.
		if ( \get_option( 'permalink_structure' ) ) {
			if ( \is_page() ) {
				$format = 'page/%#%/';
			} else {
				$format = '/%#%/';
			}
		} else {
			$format = '&paged=%#%';
		}

		$args = [
			'base'               => \str_replace( $big, '%#%', \html_entity_decode( \get_pagenum_link( $big ) ) ),
			'format'             => $format,
			'current'            => \max( 1, $current_page ),
			'total'              => $total,
			'mid_size'           => 3,
			'type'               => 'list',
			'prev_text'          => \totaltheme_get_icon( $prev_arrow, $arrow_class, $arrow_size, $arrow_bidi ) . '<span class="screen-reader-text">' . \esc_html__( 'Previous', 'total' ) . '</span>',
			'next_text'          => \totaltheme_get_icon( $next_arrow, $arrow_class, $arrow_size, $arrow_bidi ) . '<span class="screen-reader-text">' . \esc_html__( 'Next', 'total' ) . '</span>',
			'before_page_number' => '<span class="screen-reader-text">' . \esc_html__( 'Page', 'total' ) . ' </span>',
		];

		$args = \apply_filters( 'wpex_pagination_args', $args ); // @deprecated
		$this->args = (array) \apply_filters( 'totaltheme/pagination/standard/args', $args );

		return $this->args;
	}

	/**
	 * Returns pagination alignment.
	 */
	protected function get_align_class() {
		return ( $align = \get_theme_mod( 'pagination_align' ) ) ? 'wpex-text-' . \sanitize_html_class( $align ) : '';
	}

}
