<?php

defined( 'ABSPATH' ) || exit;

$html = '<div class="wpex-card-inner wpex-flex wpex-flex-col wpex-flex-grow">';

	$html .= $this->get_media( [
		'link' => true,
	] );

	$html .= '<div class="wpex-card-details wpex-flex wpex-flex-col wpex-flex-grow wpex-pt-15 wpex-last-mb-0">';

		$html .= $this->get_title( [
			'class' => 'wpex-heading wpex-child-inherit-color wpex-text-xl wpex-mb-5',
			'link'  => true,
		] );

		if ( str_starts_with( $this->get_var( 'thumbnail_overlay_style' ), 'just-events-date' ) ) {
			if ( function_exists( 'Just_Events\get_event_formatted_time' ) ) {
				$html .= $this->get_element( [
					'class'     => 'wpex-card-time wpex-mb-15',
					'content'   => Just_Events\get_event_formatted_time( $this->post_id ),
				] );
			}
		} else {
			$html .= $this->get_date( [
				'class'     => 'wpex-mb-15',
				'start_end' => 'both',
				'show_time' => false,
				'format'    => 'M j, Y',
			] );
		}

		$html .= $this->get_excerpt();

	$html .= '</div>';

$html .= '</div>';

return $html;
