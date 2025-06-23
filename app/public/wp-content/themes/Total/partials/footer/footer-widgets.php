<?php

use TotalTheme\Footer\Widgets as Footer_Widgets;

/**
 * Footer Widgets.
 *
 * @package TotalTheme
 * @subpackage Partials
 * @version 5.6.1
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'TotalTheme\Footer\Widgets' ) ) {
	return;
}

$columns    = (int) get_theme_mod( 'footer_widgets_columns', 4 );
$grid_class = wpex_row_column_width_class( $columns );
$grid_class = (array) apply_filters( 'wpex_footer_widget_col_classes', array( $grid_class ) );

if ( ! empty( $grid_class ) && is_array( $grid_class ) ) {
	$grid_class = array_map( 'esc_attr', $grid_class );
	$grid_class = implode( ' ', $grid_class );
}

?>

<div id="footer-widgets" <?php Footer_Widgets::wrapper_class(); ?>>

	<?php do_action( 'wpex_hook_footer_widgets_top' ); ?>

	<?php if ( is_active_sidebar( 'footer_one' ) ) { ?>

		<div class="footer-box <?php echo esc_attr( $grid_class ); ?> col col-1"><?php dynamic_sidebar( 'footer_one' ); ?></div>

	<?php } ?>

	<?php if ( $columns > 1 && is_active_sidebar( 'footer_two' ) ) : ?>

		<div class="footer-box <?php echo esc_attr( $grid_class ); ?> col col-2"><?php dynamic_sidebar( 'footer_two' ); ?></div>

	<?php endif; ?>

	<?php if ( $columns > 2 && is_active_sidebar( 'footer_three' ) ) : ?>

		<div class="footer-box <?php echo esc_attr( $grid_class ); ?> col col-3"><?php dynamic_sidebar( 'footer_three' ); ?></div>

	<?php endif; ?>

	<?php if ( $columns > 3 && is_active_sidebar( 'footer_four' ) ) : ?>

		<div class="footer-box <?php echo esc_attr( $grid_class ); ?> col col-4"><?php dynamic_sidebar( 'footer_four' ); ?></div>

	<?php endif; ?>

	<?php if ( $columns > 4 && is_active_sidebar( 'footer_five' ) ) : ?>

		<div class="footer-box <?php echo esc_attr( $grid_class ); ?> col col-5"><?php dynamic_sidebar( 'footer_five' ); ?></div>

	<?php endif; ?>

	<?php if ( $columns > 5 && is_active_sidebar( 'footer_six' ) ) : ?>

		<div class="footer-box <?php echo esc_attr( $grid_class ); ?> col col-6"><?php dynamic_sidebar( 'footer_six' ); ?></div>

	<?php endif; ?>

	<?php do_action( 'wpex_hook_footer_widgets_bottom' ); ?>

</div>
