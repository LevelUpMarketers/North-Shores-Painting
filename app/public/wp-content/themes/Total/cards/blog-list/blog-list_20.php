<?php

defined( 'ABSPATH' ) || exit;

$bk = $this->get_breakpoint();

if ( $bk ) {
	$bk = "-{$bk}";
	$flex_class = "wpex-flex wpex-flex-col wpex-flex-grow wpex{$bk}-items-center";
} else {
	$flex_class = 'wpex-flex wpex-items-center';
}

if ( $this->has_flex_direction_reverse() ) {
	$flex_row_class = "wpex{$bk}-flex-row-reverse";
} else {
	$flex_row_class = "wpex{$bk}-flex-row";
}

$html = '<div class="wpex-card-inner ' . $flex_class . ' ' . $flex_row_class . ' wpex-gap-15">';

	$html .= $this->get_media( [
		'class' => "wpex{$bk}-w-20 wpex-flex-shrink-0 wpex-self-stretch",
		'thumbnail_args' => [
			'class' => 'wpex-w-100 wpex-h-100',
			'image_class' => 'wpex-w-100 wpex-h-100 wpex-object-cover',
		],
	] );

	$html .= '<div class="wpex-card-details wpex-flex wpex-flex-col wpex-flex-grow';
		if ( $this->has_flex_direction_reverse() ) {
			$html .= ' wpex-items-end';
		}
	$html .= '">';

		if ( totaltheme_has_classic_styles() ) {
			$title_class = 'wpex-heading wpex-text-md wpex-child-inherit-color';
		} else {
			$title_class = 'wpex-heading wpex-text-lg wpex-child-inherit-color';
		}

		$html .= $this->get_title( [
			'class' => $title_class,
		] );

		$html .= $this->get_excerpt( [
			'class' => 'wpex-my-10',
			'length' => 0
		] );

		$html .= '<div class="wpex-card-meta wpex-flex wpex-flex-wrap wpex-items-center wpex-gap-5 wpex-mt-5">';

			$html .= $this->get_date( [
				'type' => 'published',
			] );

			$primary_term = $this->get_primary_term( [
				'term_class' => 'wpex-no-underline wpex-hover-underline wpex-text-current wpex-hover-text-1',
				'has_term_background_color' => false,
			] );

			if ( $primary_term  ) {
				$html .= '<span>&ndash;</span>';
				$html .= $primary_term;
			}

		$html .= '</div>';

	$html .= '</div>';

$html .= '</div>';

return $html;
