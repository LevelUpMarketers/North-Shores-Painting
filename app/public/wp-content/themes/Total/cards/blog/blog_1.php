<?php

defined( 'ABSPATH' ) || exit;

$html = '';

$html .= $this->get_media( [
	'class' => 'wpex-mb-20',
] );

$html .= $this->get_title( [
	'class' => 'wpex-heading wpex-text-lg wpex-mb-10',
] );

$html .= '<div class="wpex-card-meta wpex-flex wpex-flex-wrap wpex-mb-15 wpex-child-inherit-color">';

	$html .= $this->get_date( [
		'class' => 'wpex-mr-20',
		'icon' => 'calendar-o',
		'icon_class' => 'wpex-mr-10',
	] );

	$html .= $this->get_author( [
		'class' => 'wpex-mr-20',
		'link_class' => 'wpex-hover-underline',
		'icon' => 'user-o',
		'icon_class' => 'wpex-mr-10',
	] );

	$html .= $this->get_primary_term( [
		'class' => 'wpex-mr-20',
		'term_class' => 'wpex-mr-5 wpex-hover-underline',
		'icon' => 'folder-o',
		'icon_class' => 'wpex-mr-10',
	] );

	$html .= $this->get_comment_count( [
		'class' => 'wpex-child-inherit-color',
		'link_class' => 'wpex-hover-underline',
		'icon' => 'comment-o',
		'icon_class' => 'wpex-mr-10',
	] );

$html .= '</div>';

$html .= $this->get_excerpt( [
	'class' => 'wpex-mb-20',
] );

$html .= $this->get_more_link( [
	'link_class' => 'theme-button',
] );

return $html;
