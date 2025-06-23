<?php
/**
 * Returns categories block for use with meta element.
 *
 * @package TotalTheme
 * @subpackage Partials\Meta\Blocks
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;

$taxonomy = $args['taxonomy'] ?? null;

if ( ! $taxonomy ) {
    return;
}

$post_terms_args = [
    'taxonomy'  => $taxonomy,
    'before'    => '<li class="meta-category">' . totaltheme_get_icon( $args['icon'] ?? 'folder-o', 'meta-icon' ) . '<span>',
    'after'     => '</span></li>',
    'hook_name' => $args['hook_name'] ?? 'meta',
];

// This is a fallback filter that was added before v5.4.2.
if ( isset( $args['hook_name'] ) && 'staff_single_meta' === $args['hook_name'] ) {
    $post_terms_args = apply_filters( 'wpex_staff_single_meta_categories_args', $post_terms_args );
}

wpex_list_post_terms( $post_terms_args );