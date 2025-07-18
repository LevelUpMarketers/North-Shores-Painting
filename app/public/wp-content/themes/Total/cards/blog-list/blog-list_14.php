<?php
defined( 'ABSPATH' ) || exit;

$html = '';

// Get card breakpoint.
$bk = $this->get_breakpoint();

if ( $bk ) {
	$bk = "-{$bk}";
	$flex_class = 'wpex-flex wpex-flex-col wpex-flex-grow';
} else {
	$flex_class = 'wpex-flex';
}

// Set flex row class.
if ( $this->has_flex_direction_reverse() ) {
	$flex_row_class = "wpex{$bk}-flex-row-reverse";
} else {
	$flex_row_class = "wpex{$bk}-flex-row";
}

// Begin card output.
$html .= '<div class="wpex-card-inner ' . $flex_class . ' ' . $flex_row_class . ' wpex-gap-15 wpex' . $bk . '-gap-25">';

	// Media
	$html .= $this->get_media( array(
		'class' => 'wpex' . $bk . '-w-40 wpex-flex-shrink-0 wpex-self-stretch',
		'thumbnail_args' => array(
			'class' => 'wpex-w-100 wpex-h-100',
			'image_class' => 'wpex-w-100 wpex-h-100 wpex-object-cover',
		),
	) );

	// Details
	$html .= '<div class="wpex-card-details wpex-flex-grow wpex-last-mb-0">';

		// Date
		$html .= $this->get_date( array(
			'class' => 'wpex-mb-10',
			'type' => 'published',
			'format' => ( ! WPEX_WPML_ACTIVE && ! WPEX_POLYLANG_ACTIVE && 0 === strpos( get_locale(), 'en_' ) ) ? 'n/j/Y' : '',
		) );

		// Title
		$html .= $this->get_title( array(
			'class' => 'wpex-heading wpex-text-2xl',
		) );

		// Excerpt
		$html .= $this->get_excerpt( array(
			'class' => 'wpex-mt-15',
			'length' => 30,
		) );

		// More Button.
		$html .= $this->get_more_link( array(
			'class' => 'wpex-mt-15',
			'link_class' => 'wpex-border-0 wpex-border-b wpex-border-solid wpex-pb-5 wpex-no-underline',
		) );

	$html .= '</div>';

$html .= '</div>';

return $html;