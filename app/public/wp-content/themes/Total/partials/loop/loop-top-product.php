<?php
/**
 * Loop Top > Product
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 5.4
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'woocommerce_get_template_part' ) ) {
	get_template_part( 'partials/loop/loop-top' );
	return;
}

?>

<div class="woocommerce wpex-clr">
	<ul class="products wpex-row wpex-clr">