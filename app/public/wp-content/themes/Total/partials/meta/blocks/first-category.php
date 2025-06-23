<?php
/**
 * Returns first category block for use with meta element.
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

wpex_first_term_link( [
    'taxonomy' => $taxonomy,
    'before'   => '<li class="meta-category">' . totaltheme_get_icon( $args['icon'] ?? 'folder-o', 'meta-icon' ),
    'after'    => '</li>',
    'instance' => $args['hook_name'] ?? 'meta',
] );