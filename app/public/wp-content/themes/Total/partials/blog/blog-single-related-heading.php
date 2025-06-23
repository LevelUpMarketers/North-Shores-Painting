<?php
/**
 * Blog single related heading.
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 5.8.1
 */

defined( 'ABSPATH' ) || exit;

wpex_heading( [
	'tag'           => get_theme_mod( 'related_heading_tag' ) ?: 'h3',
	'content'		=> wpex_blog_related_heading(),
	'classes'		=> [
        'related-posts-title',
    ],
	'apply_filters'	=> 'blog_related',
] );
