<?php
defined( 'ABSPATH' ) || exit;

$html = '';

// Get card breakpoint.
$bk = $this->get_breakpoint();

// Set flex row class.
if ( $this->has_flex_direction_reverse() ) {
	$flex_row_class = 'wpex-flex-row-reverse';
} else {
	$flex_row_class = '';
}

// Begin card output.
$html .= '<div class="wpex-card-inner wpex-flex ' . $flex_row_class . ' wpex-flex-grow wpex-gap-20 wpex-border wpex-border-solid wpex-border-gray-300 wpex-rounded-sm wpex-p-20">';

	// Details
	$html .= '<div class="wpex-card-details wpex-flex wpex-flex-col wpex-flex-grow">';

		// Title
		$html .= $this->get_title( array(
			'link' => true,
			'class' => 'wpex-heading wpex-text-xl wpex-font-bold',
		) );

		// Meta
		$html .= '<div class="wpex-card-meta wpex-text-sm wpex-flex wpex-flex-wrap wpex-gap-5 wpex-mt-5 wpex-opacity-60">';

			// Category
			$html .= $this->get_primary_term( array(
				'term_class' => 'wpex-inherit-color wpex-hover-underline',
			) );

			// Date
			$html .= $this->get_date( array(
				'type' => 'time_ago',
				'before' => '<span>&bull;</span>',
			) );

			// Read Time
			$html .= $this->get_estimated_read_time( array(
				'before' => '<span>&bull;</span>',
			) );

		$html .= '</div>';

		// Read more
		$html .= $this->get_more_link( array(
			'class' => 'wpex-mt-auto wpex-pt-15',
			'link_class' => 'wpex-hover-underline',
			'text' => esc_html__( 'Read Article', 'total' ) . ' &rarr;',
		) );

	$html .= '</div>';

	// Thumbnail
	$media_args = array(
		'class' => 'wpex-w-20 wpex-flex-shrink-0', // default width needed to work with custom width Post Cards element setting.
		'image_class' => 'wpex-rounded-sm',
	);
	if ( empty( $this->args['media_width'] ) ) {
		$media_args['css'] = 'max-width:100px;';
	}
	$html .= $this->get_thumbnail( $media_args );

$html .= '</div>';

return $html;