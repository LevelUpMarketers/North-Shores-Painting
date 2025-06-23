<?php

/**
 * The template for displaying Comments.
 *
 * The area of the page that contains both current comments and the comment
 * form. The actual display of comments is handled by a callback to
 * wpex_comment() which is located at functions/comments-callback.php
 *
 * @package TotalTheme
 * @subpackage Templates
 * @version 6.1
 */

defined( 'ABSPATH' ) || exit;

if ( ! wpex_show_comments() ) {
	return;
}

wpex_hook_comments_before();

?>

<section id="comments" <?php wpex_comments_class(); ?>><?php

	$comments_number = number_format_i18n( get_comments_number() );
	$comments_title = sprintf( esc_html__( 'Comments (%s)', 'total' ), absint( $comments_number ) );
	$comments_title = apply_filters( 'wpex_comments_title', $comments_title );

	wpex_heading( [
		'tag'           => get_theme_mod( 'comments_heading_tag' ) ?: 'h3',
		'content'		=> $comments_title,
		'classes'		=> [ 'comments-title' ],
		'apply_filters'	=> 'comments',
	] );
	?>

	<?php wpex_hook_comments_top(); ?>

	<?php if ( have_comments() ) : ?>

		<ol class="comment-list"><?php
			$avatar_size = get_theme_mod( 'comment_avatar_size' );

			if ( $avatar_size ) {
				$avatar_size = (int) sanitize_text_field( $avatar_size );
			} else {
				$avatar_size = 50;
			}

			/**
			 * Displays the comments list.
			 */
			wp_list_comments( [
				'style'       => 'ol',
				'avatar_size' => $avatar_size,
				'format'      => 'html5',
			] );

		?></ol>

		<?php
		/**
		 * Displays the comment pagination.
		 */
		if ( 'desc' == get_option( 'comment_order' ) ) {
			$next_text = sprintf( esc_html__( 'Older comments %s', 'total' ), '&rarr;' );
			$prev_text = sprintf( esc_html__( '%s Newer comments', 'total' ), '&larr;' );
		} else {
			$next_text = sprintf( esc_html__( 'Newer comments %s', 'total' ), '&rarr;' );
			$prev_text = sprintf( esc_html__( '%s Older comments', 'total' ), '&larr;' );
		}

		the_comments_navigation( [
			'prev_text' => $prev_text,
			'next_text' => $next_text,
		] ); ?>

		<?php
		/**
		 * Display comments closed notice.
		 */
		if ( ! comments_open() && get_comments_number() ) : ?>
			<p class="no-comments wpex-text-md wpex-mt-30 wpex-text-center wpex-bold"><?php
				esc_html_e( 'Comments are closed.' , 'total' );
			?></p>
		<?php endif; ?>

	<?php endif; ?>

	<?php
	/**
	 * Displays the comment form.
	 */
	$form_args = [];
	if ( $title_reply = get_theme_mod( 'comment_form_title_reply' ) ) {
		$form_args['title_reply'] = sanitize_text_field( (string) $title_reply );
	}
	comment_form( $form_args ); ?>

	<?php wpex_hook_comments_bottom(); ?>

</section>

<?php

wpex_hook_comments_after();
