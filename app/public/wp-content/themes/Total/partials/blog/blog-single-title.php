<?php
/**
 * Single blog post title
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 5.10.1
 */

defined( 'ABSPATH' ) || exit;

?>

<header <?php wpex_blog_single_header_class(); ?>>
	<h1 <?php wpex_blog_single_title_class(); ?>><?php the_title(); ?></h1>
</header>