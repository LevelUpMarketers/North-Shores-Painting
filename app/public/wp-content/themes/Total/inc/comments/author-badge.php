<?php

namespace TotalTheme\Comments;

\defined( 'ABSPATH' ) || exit;

/**
 * Adds comment author badges.
 */
class Author_Badge {

	/**
	 * The post types to insert the comment author badge to.
	 */
	protected $post_types = [ 'post' ];

	/**
	 * Constructor.
	 */
	public function __construct() {
		\add_filter( 'get_comment_author', [ $this, 'modify_comment_author' ], 10, 2 );
	}

	/**
	 * Hooks into the get_comment_author hook.
	 */
	public function modify_comment_author( $author, $comment_id ) {
		$post = \get_post();
		$comment = \get_comment( $comment_id );
		if ( ! $comment || ! $post ) {
			return $author;
		}
		$supported_types = \apply_filters( 'wpex_comment_author_badge_supported_post_types', $this->post_types );
		if ( $comment->user_id === $post->post_author && \in_array( $post->post_type, $supported_types ) ) {
			$author .= '<span class="wpex-badge wpex-ml-5">' . \esc_html__( 'Author', 'total' ) . '</span>';
		}
		return $author;
	}

}
