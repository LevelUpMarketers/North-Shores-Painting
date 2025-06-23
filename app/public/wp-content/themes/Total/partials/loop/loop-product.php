<?php
/**
 * Main Loop > Product
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 5.4
 */

defined( 'ABSPATH' ) || exit;

if ( function_exists( 'woocommerce_get_template_part' ) ) {
	woocommerce_get_template_part( 'content', 'product' );
} else {
	get_template_part( 'partials/loop/loop' );
}