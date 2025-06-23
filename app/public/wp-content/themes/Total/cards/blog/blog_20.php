<?php

defined( 'ABSPATH' ) || exit;

$html = '<div class="wpex-card-inner wpex-flex wpex-flex-col wpex-flex-grow">';

	$html .= $this->get_media( [
		'class' => 'wpex-mb-15',
	] );

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

return $html;
