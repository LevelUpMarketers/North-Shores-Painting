<?php

defined( 'ABSPATH' ) || exit;

/**
 * Returns post author data for use with author box.
 */
function wpex_get_author_box_data( $post = null, $args = [] ) {
	if ( ! $post ) {
		global $post;
	}

	// Need to always return data to prevent warnings.
	$data = [
		'post_author' => '',
		'avatar_size' => '',
		'author_name' => '',
		'posts_url'   => '',
		'description' => '',
	];

	if ( ! empty( $post ) ) {
		$authordata  = get_userdata( $post->post_author );
		$author_name = apply_filters( 'the_author', is_object( $authordata ) ? $authordata->display_name : null );
		$avatar_size = $args['avatar_size'] ?? get_theme_mod( 'author_box_avatar_size' );
		if ( empty( $avatar_size ) && '0' !== strval( $avatar_size ) ) {
			$avatar_size = 70;
		}
		$avatar_size = apply_filters( 'wpex_author_bio_avatar_size', absint( $avatar_size ), $post, $args );
		$data = [
			'post_author' => $post->post_author,
			'avatar_size' => absint( $avatar_size ),
			'author_name' => $author_name,
			'posts_url'   => get_author_posts_url( $post->post_author ),
			'description' => get_the_author_meta( 'description', $post->post_author ),
		];
		if ( ( isset( $data['avatar_size'] ) && 0 !== $data['avatar_size'] ) ) {
			if ( array_key_exists( 'avatar_args', $args ) ) {
				$avatar_args = $args['avatar_args'];
			} else {
				$avatar_class = 'wpex-align-middle';
				$avatar_border_radius = get_theme_mod( 'author_box_avatar_border_radius' ) ?: 'round';
				$avatar_class .= ' wpex-' . sanitize_html_class( $avatar_border_radius );
				$avatar_args = [
					'class' => $avatar_class
				];
				$args['avatar_args'] = $avatar_args;
			}
			$data['avatar'] = get_avatar( $post->post_author, $data['avatar_size'], '', '', $avatar_args );
		} else {
			$data['avatar'] = ''; // important!
		}
	}
	$data = wp_parse_args( $args, $data );
	$data = apply_filters( 'wpex_post_author_bio_data', $data, $post );
	return (array) apply_filters( 'wpex_author_box_data', $data, $post );
}

/**
 * Display author box social links.
 */
function wpex_author_box_social_links( $post_author = '' ) {
	wpex_user_social_links( [
		'user_id'         => $post_author,
		'display'         => 'icons',
		'before'          => '<div class="author-bio-social wpex-mb-15"><div class="author-bio-social__items wpex-inline-flex wpex-flex-wrap wpex-gap-5">',
		'after'           => '</div></div>',
		'link_attributes' => [
			'class' => 'author-bio-social__item ' . wpex_get_social_button_class( get_theme_mod( 'author_box_social_style', 'flat-color-round' ) ),
		],
	] );
}
