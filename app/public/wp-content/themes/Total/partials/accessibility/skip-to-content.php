<?php

/**
 * Skip To Content.
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;

// Get default content ID
$id = get_theme_mod( 'skip_to_content_id' ) ?: 'content';

// Check for meta set value
$meta = get_post_meta( wpex_get_current_post_id(), 'skip_to_content_id', true );
if ( $meta && is_string( $meta ) ) {
    $id = $meta;
}

// Filter the content id
$id = apply_filters( 'wpex_skip_to_content_id', $id );

?>

<a href="<?php echo esc_url( '#' . str_replace( '#', '', $id ) ); ?>" class="skip-to-content"><?php esc_html_e( 'Skip to content', 'total' ); ?></a>
