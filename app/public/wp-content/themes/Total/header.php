<?php
/**
 * The header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package TotalTheme
 * @subpackage Templates
 * @version 5.10.1
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

	<?php wpex_hook_after_body_tag(); // Added before wp_body_open was introduced ?>

	<?php wpex_hook_outer_wrap_before(); ?>

	<div id="outer-wrap" class="wpex-overflow-clip">
		
		<?php wpex_hook_outer_wrap_top(); ?>

		<?php wpex_hook_wrap_before(); ?>

		<div id="wrap" class="wpex-clr">

			<?php wpex_hook_wrap_top(); ?>

			<?php wpex_hook_main_before(); ?>

			<main id="main" class="site-main wpex-clr">

				<?php wpex_hook_main_top(); ?>