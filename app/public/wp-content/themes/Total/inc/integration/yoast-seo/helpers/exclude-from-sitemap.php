<?php

namespace TotalTheme\Integration\Yoast_SEO\Helpers;

\defined( 'ABSPATH' ) || exit;

/**
 * Excludes an post by ID from the Yoast sitemap.
 */
class Exclude_From_Sitemap {

	/**
	 * The id to exclude.
	 */
	protected $id;

	/**
	 * Constructor.
	 */
	public function __construct( $id = '' ) {
		if ( ! $id ) {
			return;
		}
		$this->id = $id;
		\add_action( 'wpseo_exclude_from_sitemap_by_post_ids', [ $this, 'filter' ] );
	}

	/**
	 * Hooks into wpseo_exclude_from_sitemap_by_post_ids to exclude post ids from Yoast SEO sitemap.
	 */
	public function filter( $excluded_posts_ids ) {
		if ( \is_array( $excluded_posts_ids ) ) {
			$excluded_posts_ids[] = $this->id;
		}
		return $excluded_posts_ids;
	}

}
