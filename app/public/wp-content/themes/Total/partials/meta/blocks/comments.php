<?php
/**
 * Returns comments block for use with meta element.
 *
 * @package TotalTheme
 * @subpackage Partials\Meta\Blocks
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! comments_open() || post_password_required() ) {
    return;
}

?>

<li class="meta-comments comment-scroll"><?php echo totaltheme_get_icon( $args['icon'] ?? 'comment-o', 'meta-icon' ); ?><?php comments_popup_link( esc_html__( '0 Comments', 'total' ), esc_html__( '1 Comment',  'total' ), esc_html__( '% Comments', 'total' ), 'comments-link' ); ?></li>