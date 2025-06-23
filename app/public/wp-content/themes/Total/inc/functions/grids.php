<?php

defined( 'ABSPATH' ) || exit;

/**
 * Returns grid class based on settings.
 */
 function wpex_grid_columns_class( $columns = '' ) {
   if ( ! $columns ) {
      return;
   }

   $class = '';

   // Responsive columns.
   if ( is_array( $columns ) ) {
      $responsive_columns = $columns;
      $class .= ' wpex-grid-cols-' . absint( $responsive_columns[ 'd'] );
      unset( $responsive_columns[ 'd'] );
      foreach ( $responsive_columns as $key => $val ) {
         if ( $val ) {
            $class .= ' wpex-' . sanitize_html_class( $key ) . '-grid-cols-' . sanitize_html_class( $val );
         }
      }
   }

   // Standard columns.
   else {

      // Sanitize columns
      $columns = absint( $columns );

      // Default colums.
      $class .= " wpex-grid-cols-{$columns}";

      $auto_responsive = ( 1 === $columns ) ? false : true;
      $auto_responsive = apply_filters( 'wpex_grid_columns_class_auto_responsive', $auto_responsive, $columns );

      if ( $auto_responsive ) {

         // Convert 4 columns to 2 columns for "auto" responsive and stick to old standards.
         if ( 4 === $columns ) {
            $class .= ' wpex-tp-grid-cols-2';
         }

         // Convert columns to 1 column for small devices.
         // @todo add filter to modify the default breakpoint prefix.
         $class .= ' wpex-pp-grid-cols-1';

      }

   }

   $class = apply_filters( 'wpex_grid_columns_class', $class, $columns );

   return trim( $class );
}

/**
 * Return
 */
function wpex_row_column_width_class( $columns = '4' ) {
    $class = '';

    // Responsive columns.
    if ( is_array( $columns ) && count( $columns ) > 1 ) {
		$class = 'span_1_of_' . sanitize_html_class( $columns[ 'd' ] );
		$responsive_columns = $columns;
		unset( $responsive_columns[ 'd'] );
		foreach ( $responsive_columns as $key => $val ) {
			if ( $val ) {
				$class .= ' span_1_of_' . sanitize_html_class( $val ) . '_' . sanitize_html_class( $key );
			}
		}
	}

    // Non responsive columns.
    else {
      $cols_class = sanitize_html_class( $columns );
		$class = "span_1_of_{$cols_class}";
	}

   /*** deprecated ***/
   $class = apply_filters( 'wpex_grid_class', $class );

	return (string) apply_filters( 'wpex_row_column_width_class', $class, $columns );
}

/**
 * Returns the gap class.
 */
function wpex_gap_class( $gap = '' ): string {
	if ( '0px' === $gap || '0' === $gap ) {
		$gap = 'none';
	}
   $gap_class = sanitize_html_class( $gap );
   $gap_class = "gap-{$gap_class}";
	return (string) apply_filters( 'wpex_gap_class', $gap_class );
}
