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

	$html .= $this->get_media( [
		'class'          => "wpex{$bk}-w-50 wpex-flex-shrink-0 wpex-self-stretch",
		'thumbnail_args' => [
			'class'       => 'wpex-w-100 wpex-h-100',
			'image_class' => 'wpex-w-100 wpex-h-100 wpex-object-cover',
		],
	] );

	$html .= '<div class="wpex-card-details wpex-flex wpex-flex-col wpex-flex-grow wpex-p-25 wpex-last-mb-0">';

		$html .= $this->get_primary_term( [
			'class'                     => 'wpex-font-semibold wpex-leading-normal wpex-mb-15',
			'term_class'                => 'wpex-inline-block wpex-bg-accent wpex-hover-bg-accent_alt wpex-no-underline wpex-px-10 wpex-py-5 wpex-text-xs',
			'has_term_background_color' => true,
		] );

		$html .= $this->get_title( [
			'class'      => 'wpex-heading wpex-text-xl wpex-font-bold wpex-mb-15',
			'link_class' => 'wpex-inherit-color-important',
		] );

		$html .= $this->get_excerpt( [
			'class' => 'wpex-text-2 wpex-mb-15',
		] );

		$html .= $this->get_date( [
			'type'       => 'modified',
			'class'      => 'wpex-mt-auto',
			'icon'       => 'clock-o',
			'icon_class' => 'wpex-mr-10',
		] );

	$html .= '</div>';

$html .= '</div>';

return $html;
