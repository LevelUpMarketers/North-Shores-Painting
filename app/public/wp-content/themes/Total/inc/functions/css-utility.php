<?php

/**
 * Return utility font sizes.
 */
function wpex_utl_font_sizes(): array {
	$sizes = [
		''     => esc_html__( 'Default', 'total' ),
		'xs'   => esc_html__( 'x Small', 'total' ),
		'sm'   => esc_html__( 'Small', 'total' ),
		'base' => esc_html__( 'Base', 'total' ),
		'md'   => esc_html__( 'Medium', 'total' ),
		'lg'   => esc_html__( 'Large', 'total' ),
		'xl'   => esc_html__( 'x Large', 'total' ),
		'2xl'  => esc_html__( '2x Large', 'total' ),
		'3xl'  => esc_html__( '3x Large', 'total' ),
		'4xl'  => esc_html__( '4x Large', 'total' ),
		'5xl'  => esc_html__( '5x Large', 'total' ),
		'6xl'  => esc_html__( '6x Large', 'total' ),
		'7xl'  => esc_html__( '7x Large', 'total' ),
	];
	if ( ! totaltheme_has_classic_styles() ) {
		unset( $sizes['md'] );
	}
	return (array) apply_filters( 'wpex_utl_font_sizes', $sizes );
}

/**
 * Return utility percentage widths.
 */
function wpex_utl_percent_widths(): array {
	return (array) apply_filters( 'wpex_utl_precentage_widths', [
		''    => esc_html__( 'Default', 'total' ),
		'20'  => '20%',
		'25'  => '25%',
		'30'  => '30%',
		'33'  => '33%',
		'40'  => '40%',
		'50'  => '50%',
		'60'  => '60%',
		'67'  => '67%',
		'70'  => '70%',
		'75'  => '75%',
		'80'  => '80%',
		'100' => '100%',
	] );
}

/**
 * Return utility border radius.
 */
function wpex_utl_border_radius( $include_blobs = false ): array {
	$choices = [
		''             => esc_html__( 'Default', 'total' ),
		'rounded-xs'   => esc_html__( 'Extra Small', 'total' ),
		'rounded-sm'   => esc_html__( 'Small', 'total' ),
		'rounded'      => esc_html__( 'Average', 'total' ),
		'rounded-md'   => esc_html__( 'Medium', 'total' ),
		'rounded-lg'   => esc_html__( 'Large', 'total' ),
		'rounded-xl'   => esc_html__( 'x Large', 'total' ),
		'rounded-2xl'  => esc_html__( '2x Large', 'total' ),
		'rounded-3xl'  => esc_html__( '3x Large', 'total' ),
		'rounded-4xl'  => esc_html__( '4x Large', 'total' ),
		'rounded-full' => esc_html__( 'Full', 'total' ),
		'rounded-0'    => esc_html__( 'None', 'total' ),
	];
	if ( $include_blobs ) {
		$blobs = [
			'radius-blob-1' => esc_html__( 'Blob 1', 'total' ),
			'radius-blob-2' => esc_html__( 'Blob 2', 'total' ),
			'radius-blob-3' => esc_html__( 'Blob 3', 'total' ),
			'radius-blob-4' => esc_html__( 'Blob 4', 'total' ),
			'radius-blob-5' => esc_html__( 'Blob 5', 'total' ),
		];
		$choices = array_merge( $choices, $blobs );
	}
	return (array) apply_filters( 'wpex_utl_border_radius', $choices, $include_blobs );
}

/**
 * Return utility border width types.
 */
function wpex_utl_border_widths(): array {
	return (array) apply_filters( 'wpex_utl_border_widths', [
		''  => esc_html__( 'Default', 'total' ),
		'0px'  => '0px',
		'1'    => '1px',
		'2'    => '2px',
		'3'    => '3px',
		'4'    => '4px',
	] );
}

/**
 * Return utility paddings.
 */
function wpex_utl_paddings(): array {
	return (array) apply_filters( 'wpex_utl_paddings', [
		''     => esc_html__( 'Default', 'total' ),
		'0px'  => '0px',
		'5px'  => '5px',
		'10px' => '10px',
		'15px' => '15px',
		'20px' => '20px',
		'25px' => '25px',
		'30px' => '30px',
		'40px' => '40px',
		'50px' => '50px',
		'60px' => '60px',
	] );
}

/**
 * Return utility margins.
 */
function wpex_utl_margins(): array {
	return (array) apply_filters( 'wpex_utl_margins', [
		''     => esc_html__( 'Default', 'total' ),
		'0px'  => '0px',
		'5px'  => '5px',
		'10px' => '10px',
		'15px' => '15px',
		'20px' => '20px',
		'25px' => '25px',
		'30px' => '30px',
		'40px' => '40px',
		'50px' => '50px',
		'60px' => '60px',
	] );
}

/**
 * Return utility letter spacing options.
 */
function wpex_utl_letter_spacing(): array {
	return (array) apply_filters( 'wpex_utl_letter_spacing', [
		''         => esc_html__( 'Default', 'total' ),
		'tighter'  => esc_html__( 'Tighter', 'total' ),
		'tight'    => esc_html__( 'Tight', 'total' ),
		'normal'   => esc_html__( 'Normal', 'total' ),
		'wide'     => esc_html__( 'Wide', 'total' ),
		'wider'    => esc_html__( 'Wider', 'total' ),
		'widest'   => esc_html__( 'Widest', 'total' ),
	] );
}

/**
 * Return utility line height options.
 */
function wpex_utl_line_height(): array {
	return (array) apply_filters( 'wpex_utl_line_height', [
		''        => esc_html__( 'Default', 'total' ),
		'tight'   => esc_html__( 'Tight', 'total' ),
		'snug'    => esc_html__( 'Snug', 'total' ),
		'normal'  => esc_html__( 'Normal', 'total' ),
		'relaxed' => esc_html__( 'Relaxed', 'total' ),
		'loose'   => esc_html__( 'Loose', 'total' ),
	] );
}

/**
 * Get utility shadows.
 */
function wpex_utl_shadows(): array {
	return (array) apply_filters( 'wpex_utl_shadows', [
		''		      => esc_html__( 'Default', 'total' ),
		'shadow-none' => esc_html__( 'None', 'total' ),
		'shadow-xs'   => esc_html__( 'Extra Small', 'total' ),
		'shadow-sm'   => esc_html__( 'Small', 'total' ),
		'shadow'      => esc_html__( 'Average', 'total' ),
		'shadow-md'   => esc_html__( 'Medium', 'total' ),
		'shadow-lg'   => esc_html__( 'Large', 'total' ),
		'shadow-xl'   => esc_html__( 'Extra Large', 'total' ),
		'shadow-2xl'  => esc_html__( '2x Large', 'total' ),
	] );
}

/**
 * Get utility divider styles.
 */
function wpex_utl_divider_styles(): array {
	return (array) apply_filters( 'wpex_utl_divider_styles', [
		''       => esc_html__( 'Default', 'total' ),
		'solid'  => esc_html__( 'Solid', 'total' ),
		'dotted' => esc_html__( 'Dotted', 'total' ),
		'dashed' => esc_html__( 'Dashed', 'total' ),
	] );
}

/**
 * Get utility opacity.
 */
function wpex_utl_opacities(): array {
	return (array) apply_filters( 'wpex_utl_opacities', [
		''	  => esc_html__( 'Default', 'total' ),
		'10'  => '10%',
		'20'  => '20%',
		'30'  => '30%',
		'40'  => '40%',
		'50'  => '50%',
		'60'  => '60%',
		'70'  => '70%',
		'80'  => '80%',
		'90'  => '90%',
		'100' => '100%',
	] );
}

/**
 * Get utility breakpoints.
 */
function wpex_utl_breakpoints(): array {
	return (array) apply_filters( 'wpex_utl_breakpoints', [
		''   => esc_html__( 'Default', 'total' ),
		'sm' => esc_html__( 'sm - 640px', 'total' ),
		'md' => esc_html__( 'md - 768px', 'total' ),
		'lg' => esc_html__( 'lg - 1024px', 'total' ),
		'xl' => esc_html__( 'xl - 1280px', 'total' ),
	] );
}

/**
 * Return utility visibility class.
 */
function wpex_utl_visibility_class( $show_hide = 'hide', $screen = '' ) {
	if ( ! $screen || ! array_key_exists( $screen, wpex_utl_breakpoints() ) ) {
		return;
	}
	$class = '';
	switch ( $show_hide ) {
		case 'hide':
			$class = 'wpex-hidden wpex-' . sanitize_html_class( $screen ) . '-block';
			break;
		case 'show':
			$class = 'wpex-' . sanitize_html_class( $screen ) . '-hidden';
			break;
	}
	return $class;
}

/**
 * Return utility font size class.
 */
function wpex_sanitize_utl_font_size( $font_size = '' ) {
	if ( ! $font_size || ! array_key_exists( $font_size, wpex_utl_font_sizes() ) ) {
		return;
	}
	if ( in_array( $font_size, [ '5xl', '6xl', '7xl' ], true ) && totaltheme_has_classic_styles() ) {
		$class = "wpex-text-4xl wpex-md-text-{$font_size}";
	} else {
		$class = 'wpex-text-' . sanitize_html_class( $font_size );
	}
	return apply_filters( 'wpex_utl_font_size_class', $class, $font_size );
}
