<?php

/**
 * WPBakery Template for a blank page layout.
 *
 * @since 5.9.1
 */

defined( 'ABSPATH' ) || exit;

?><!DOCTYPE html>
<html <?php language_attributes(); ?> <?php wpex_html_class(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<link rel="profile" href="http://gmpg.org/xfn/11">
<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

<?php wp_body_open(); ?>

<?php while ( have_posts() ) : the_post(); ?>

	<div id="content-wrap" class="container">
		<div id="content" class="site-content">
			<article class="single-page-article wpex-clr">
				<?php the_content(); ?>
			</article>
		</div>
	</div>

<?php
endwhile;

?>

<?php
wp_footer();
?>

</body>
</html>
