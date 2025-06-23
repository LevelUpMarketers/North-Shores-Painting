<?php

defined( 'ABSPATH' ) || exit;

$has_link = $this->has_link_wrap();

$html = '';

if ( $has_link ) {
	$html .= $this->get_link_open( [
		'class' => 'wpex-card-inner wpex-flex wpex-flex-col wpex-flex-grow wpex-overflow-hidden wpex-rounded-md wpex-border wpex-border-solid wpex-border-main wpex-surface-1 wpex-no-underline wpex-inherit-color',
		'attributes' => array(
			'aria-label' => get_the_title( $this->post_id ),
		),
	] );
} else {
	$html .= '<div class="wpex-card-inner wpex-flex wpex-flex-col wpex-flex-grow wpex-overflow-hidden wpex-rounded-md wpex-border wpex-border-solid wpex-border-main wpex-surface-1">';
}

	$html .= $this->get_media( [
		'link' => ! $has_link,
	] );

	$html .= '<div class="wpex-card-details wpex-flex wpex-flex-col wpex-flex-grow wpex-p-30 wpex-last-mb-0">';

		$html .= $this->get_title( [
			'class' => 'wpex-heading wpex-child-inherit-color wpex-text-xl',
			'link' => ! $has_link,
		] );

		$html .= $this->get_excerpt( [
			'class' => 'wpex-mt-15',
		] );

	$html .= '</div>';

if ( $has_link ) {
	$html .= $this->get_link_close();
} else {
	$html .= '</div>';
}

return $html;
