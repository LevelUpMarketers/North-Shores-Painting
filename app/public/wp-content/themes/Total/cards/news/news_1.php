<?php

defined( 'ABSPATH' ) || exit;

if ( empty( $this->args['breakpoint'] ) ) {
	$this->args['breakpoint'] = 'sm';
}

// Get card breakpoint.
$bk = $this->get_breakpoint();

if ( $bk ) {
	$bk = "-{$bk}";
	$flex_class = 'wpex-flex wpex-flex-col';
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
$html = '<div class="wpex-card-inner ' . $flex_class . ' ' . $flex_row_class . ' wpex-gap-20">';

	$html .= $this->get_thumbnail( [
		'class'       => "wpex{$bk}-w-30 wpex-flex-shrink-0",
		'image_class' => 'wpex-w-100',
	] );

	$html .= '<div class="wpex-card-details wpex-flex-grow">';

		$html .= $this->get_title( [
			'link'  => true,
			'class' => 'wpex-heading wpex-text-lg',
		] );

		$html .= '<div class="wpex-card-meta wpex-flex wpex-flex-wrap wpex-gap-5 wpex-mb-15 wpex-child-inherit-color wpex-opacity-60 wpex-text-sm">';

			$html .= $this->get_date( [
				'format' => 'F j, Y g:ia',
			] );

			$html .= $this->get_author( [
				'prefix'     => esc_html__( 'by', 'total' ) . ' ',
				'link_class' => 'wpex-hover-underline',
			] );

		$html .= '</div>';

		$html .= $this->get_excerpt( [
			'class'  => 'wpex-my-15',
			'length' => 40,
		] );

		$html .= '<div class="wpex-card-footer wpex-text-sm">';

			$html .= $this->get_more_link( [
				'html_tag'   => 'span',
				'link_class' => 'wpex-hover-underline',
				'text'       => esc_html__( 'Read Full Article', 'total' ),
			] );

			$html .= $this->get_comment_count( [
				'html_tag'   => 'span',
				'link_class' => 'wpex-hover-underline',
				'before'     => ' &bull; ',
			] );

		$html .= '</div>';

	$html .= '</div>';

$html .= '</div>';

return $html;
