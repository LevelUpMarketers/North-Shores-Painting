<?php

namespace TotalTheme;

\defined( 'ABSPATH' ) || exit;

/**
 * Removes slugs from core custom post types.
 */
class Remove_Cpt_Slugs {

	/**
	 * Post types to remove the slugs from.
	 *
	 * @var array $types Array of post types to remove slugs from.
	 */
	protected $types = [];

	/**
	 * Constructor.
	 */
	public function __construct() {
		\add_filter( 'init', [ $this, '_on_init' ], 100 );
	}

	/**
	 * Hooks into the init hook.
	 */
	public function _on_init(): void {
		$this->types = $this->get_types();
		if ( $this->types ) {
			\add_filter( 'post_type_link', [ $this, 'post_type_link' ], 10, 3 );
			\add_action( 'pre_get_posts', [ $this, 'on_pre_get_posts' ] );
			\add_action( 'template_redirect', [ $this, '_on_template_redirect' ] );
		}
	}

	/**
	 * Return array of post types to remove the slugs from.
	 */
	private function get_types(): array {
		$types = [];
		$built_in_types = [
			'portfolio',
			'staff',
			'testimonials',
		];
		foreach ( $built_in_types as $built_in_type ) {
			if ( 0 === \get_theme_mod( "{$built_in_type}_slug" )
				&& \totaltheme_call_static( ucfirst( $built_in_type ) . '\Post_Type', 'is_enabled' )
			) {
				$types[] = $built_in_type;
			}
		}
		$types = \apply_filters_deprecated(
			'wpex_remove_post_type_slugs_types',
			[ $types ],
			'Total 6.1',
			'totaltheme/remove_cpt_slugs/post_types'
		);
		return (array) \apply_filters( 'totaltheme/remove_cpt_slugs/post_types', $types );
	}

	/**
	 * Remove slugs from the post types.
	 */
	public function post_type_link( $post_link, $post, $leavename ) {
		if ( ! \in_array( $post->post_type, $this->types, true ) || 'publish' !== $post->post_status ) {
			return $post_link;
		}
		if ( $slug = $this->get_post_type_slug( $post->post_type ) ) {
			$post_link = \str_replace( "/{$slug}/", '/', $post_link );
		}
		return $post_link;
	}

	/**
	 * WordPress will no longer recognize the custom post type as a custom post type.
	 * this function tricks WordPress into thinking it actually is still a custom post type.
	 */
	public function on_pre_get_posts( $query ) {
		if ( $query->is_main_query()
			&& 2 === count( $query->query )
			&& isset( $query->query['page'] )
			&& ! empty( $query->query['name'] )
		) {
			$query->set( 'post_type', array_merge( [ 'post', 'page' ], $this->types ) );
		}
	}

	/**
     * Redirect the old URL's with the slugs.
     */
    public function _on_template_redirect() {
		if ( ! is_singular( $this->types ) || is_admin() || is_preview() ) {
			return;
		}
		$slug = $this->get_post_type_slug( get_post_type() );
		$current_url = trailingslashit( $this->get_current_url() );
		if ( $slug && str_contains( $current_url,  "/{$slug}" ) ) {
			wp_safe_redirect( esc_url( str_replace( "/{$slug}", '', $current_url ) ), 301 );
			exit;
		}
    }

	/**
	 * Get Post type slug.
	 */
	private function get_post_type_slug( $type ): string {
		$obj = get_post_type_object( $type );
		return $obj->rewrite['slug'] ?? $obj->name ?? $type;
	}

	 /**
     * Get the current URL.
     */
    private function get_current_url() {
        global $wp;
        if ( $wp ) {
            return home_url( add_query_arg( [], $wp->request ) );
        }
    }

}
