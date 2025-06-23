<?php

defined( 'ABSPATH' ) || exit;

$bk = $this->get_breakpoint();

if ( $bk ) {
	$bk = "-{$bk}";
	$flex_class = 'wpex-flex wpex-flex-col wpex-flex-grow';
} else {
	$flex_class = 'wpex-flex';
}

if ( $this->has_flex_direction_reverse() ) {
	$flex_row_class = "wpex{$bk}-flex-row-reverse";
} else {
	$flex_row_class = "wpex{$bk}-flex-row";
}

$inner_class = "wpex-card-inner {$flex_class} {$flex_row_class} wpex-surface-1 wpex-border wpex-border-solid wpex-border-main";

// Begin card output.
$html = '<div class="' . $inner_class . '">';

	// Media
	$html .= $this->get_media( [
		'class'          => "wpex{$bk}-w-50 wpex-flex-shrink-0 wpex-self-stretch",
		'thumbnail_args' => [
			'class'       => 'wpex-w-100 wpex-h-100',
			'image_class' => 'wpex-w-100 wpex-h-100 wpex-object-cover',
		],
	] );

	// Details
	$html .= '<div class="wpex-card-details wpex-flex-grow wpex-p-25 wpex-last-mb-0">';

		$html .= $this->get_terms_list( [
			'class'          => 'wpex-mb-15 wpex-font-bold wpex-text-xs wpex-uppercase wpex-tracking-wide',
			'term_class'     => 'wpex-inline-block wpex-hover-underline',
			'separator'      => '<span class="wpex-card-terms-list-sep wpex-inline-block wpex-mx-5">&#8725;</span>',
			'has_term_color' => true,
		] );

		$html .= $this->get_title( [
			'class'      => 'wpex-heading wpex-text-xl wpex-font-bold wpex-my-15',
			'link_class' => 'wpex-inherit-color-important',
		] );

		$html .= $this->get_excerpt( [
			'class' => 'wpex-text-2 wpex-my-15',
		] );

		$html .= $this->get_date( [
			'type'       => 'time_ago',
			'prefix'     =>  ! $this->is_event() ? esc_html__( 'Published', 'total' ) . ' ' : '',
			'class'      => 'wpex-mt-20',
			'icon'       => 'clock-o',
			'icon_class' => 'wpex-mr-10',
		] );

	$html .= '</div>';

$html .= '</div>';

return $html;
