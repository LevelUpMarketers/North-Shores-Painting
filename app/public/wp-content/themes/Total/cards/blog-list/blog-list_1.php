<?php

defined( 'ABSPATH' ) || exit;

$bk = $this->get_breakpoint();

if ( $bk ) {
	$bk = "-{$bk}";
	$flex_class = 'wpex-flex wpex-flex-col';
} else {
	$flex_class = 'wpex-flex';
}

if ( $this->has_flex_direction_reverse() ) {
	$flex_row_class = "wpex{$bk}-flex-row-reverse";
} else {
	$flex_row_class = "wpex{$bk}-flex-row";
}

$inner_class = "wpex-card-inner {$flex_class} {$flex_row_class } wpex-gap-20 wpex{$bk}-gap-25";

// Begin card output.
$html = '<div class="' . $inner_class . '">';

	// Media
	$html .= $this->get_media( [
		'class'          => "wpex{$bk}-w-40 wpex-flex-shrink-0 wpex-self-stretch",
		'thumbnail_args' => [
			'class'       => 'wpex-w-100 wpex-h-100',
			'image_class' => 'wpex-w-100 wpex-h-100 wpex-object-cover',
		],
	] );

	// Details.
	$html .= '<div class="wpex-card-details wpex-flex-grow wpex-last-mb-0">';

		$html .= $this->get_title( [
			'class' => 'wpex-heading wpex-text-lg wpex-mb-10',
		] );

		$html .= '<div class="wpex-card-meta wpex-flex wpex-flex-wrap wpex-mb-15 wpex-child-inherit-color">';

			$html .= $this->get_date( [
				'class'      => 'wpex-mr-20',
				'icon'       => 'calendar-o',
				'icon_class' => 'wpex-mr-10',
			] );

			$html .= $this->get_author( [
				'class'      => 'wpex-mr-20',
				'link_class' => 'wpex-hover-underline',
				'icon'       => 'user-o',
				'icon_class' => 'wpex-mr-10',
			] );

			$html .= $this->get_primary_term( [
				'class'      => 'wpex-mr-20',
				'term_class' => 'wpex-mr-10 wpex-hover-underline',
				'icon'       => 'folder-o',
				'icon_class' => 'wpex-mr-10',
			] );

			$html .= $this->get_comment_count( [
				'class'      => 'wpex-child-inherit-color',
				'link_class' => 'wpex-hover-underline',
				'icon'       => 'comment-o',
				'icon_class' => 'wpex-mr-10',
			] );

		$html .= '</div>';

		$html .= $this->get_excerpt( [
			'class' => 'wpex-mb-20',
		] );

		$html .= $this->get_more_link( [
			'link_class' => 'theme-button',
		] );

	$html .= '</div>';

$html .= '</div>';

return $html;
