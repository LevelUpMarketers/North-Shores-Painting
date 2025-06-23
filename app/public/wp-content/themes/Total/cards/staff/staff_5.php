<?php

defined( 'ABSPATH' ) || exit;

$html = '';

$html .= '<div class="wpex-card-inner wpex-flex wpex-flex-col wpex-flex-grow wpex-surface-1 wpex-p-40 wpex-text-center wpex-rounded wpex-shadow">';

	$html .= $this->get_thumbnail( [
		'class' => 'wpex-mb-20 wpex-rounded-full wpex-mx-auto',
		'image_class' => 'wpex-rounded-full'
	] );

	$html .= $this->get_title( [
		'class' => 'wpex-heading wpex-text-lg',
	] );

	if ( ! empty( $this->post_id ) ) {
		$email = sanitize_email( get_post_meta( $this->post_id, 'wpex_staff_email', true ) );
		if ( $email ) {
			$html .= $this->get_element( [
				'content' => $email,
				'link' => 'mailto:' . $email,
				'class' => 'wpex-card-staff-member-email wpex-mt-5',
				'link_class' => 'wpex-no-underline',
			] );
		}
	}

$html .= '</div>';

return $html;
