<?php
/**
 * Programmatically insert the Proof of Concept blog post.
 * This file can be loaded by the plugin to add the post if it doesn't exist.
 */

function db_dump_blog_proof_of_concept_post() {
    // Check if a post with the same slug already exists.
    if ( get_page_by_path( 'proof-of-concept-post', OBJECT, 'post' ) ) {
        return true; // Already exists, nothing to do.
    }

    $post_data = array(
        'post_author'    => 5,
        'post_date'      => '2025-06-06 12:00:00',
        'post_date_gmt'  => '2025-06-06 12:00:00',
        'post_content'   => 'Proof of concept blog post content.',
        'post_title'     => 'Proof of Concept Post',
        'post_excerpt'   => '',
        'post_status'    => 'publish',
        'comment_status' => 'open',
        'ping_status'    => 'closed',
        'post_name'      => 'proof-of-concept-post',
        'post_modified'  => '2025-06-06 12:00:00',
        'post_modified_gmt' => '2025-06-06 12:00:00',
        'post_parent'    => 0,
        'menu_order'     => 0,
        'post_type'      => 'post',
        'post_mime_type' => '',
    );

    $post_id = wp_insert_post( $post_data, true );
    if ( is_wp_error( $post_id ) ) {
        return false;
    }

    // Post metadata similar to WordPress defaults.
    add_post_meta( $post_id, '_edit_last', 5 );
    add_post_meta( $post_id, '_edit_lock', time() . ':5' );

    // Assign default category (ID = 1).
    wp_set_post_terms( $post_id, array( 1 ), 'category' );

    return true;
}
