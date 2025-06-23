<?php
/**
 * Page Single Thumbnail
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 5.10.1
 */

defined( 'ABSPATH' ) || exit;

wpex_post_thumbnail( apply_filters( 'wpex_page_single_thumbnail_args', [
	'size'  => 'full',
	'class' => 'wpex-align-middle',
] ) );