<?php

namespace TotalTheme\Pagination;

\defined( 'ABSPATH' ) || exit;

/**
 * Next_Prev Pagination.
 */
class Next_Prev {

	/**
	 * The current query.
	 */
	protected $query;

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
	public function render()  {
		$max_pages = \absint( $this->query->max_num_pages ?? 1 );

		if ( ! $max_pages || 1 === $max_pages ) {
			return;
		}

		?>
			<div class="page-jump wpex-mt-30 wpex-flex wpex-flex-wrap wpex-gap-20 wpex-justify-between">
				<div class="newer-posts"><?php
					\previous_posts_link( '&larr; ' . \esc_html__( 'Newer Posts', 'total' ) );
				?></div>
				<div class="older-posts"><?php
					\next_posts_link( \esc_html__( 'Older Posts', 'total' ) . ' &rarr;' );
				?>
				</div>
			</div>
		<?php
	}

}
