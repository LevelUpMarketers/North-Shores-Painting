<?php

defined( 'ABSPATH' ) || exit;

$html = '<div class="wpex-card-inner wpex-flex wpex-flex-col wpex-flex-grow wpex-bg-gray-100">';

	// Media
	$html .= $this->get_media();

	// Details
	$html .= '<div class="wpex-card-details wpex-flex wpex-flex-col wpex-flex-grow wpex-p-40 wpex-last-mb-0">';

		// Meta
		$html .= '<div class="wpex-card-meta wpex-text-3 wpex-flex wpex-flex-wrap wpex-gap-5 wpex-text-xs wpex-mb-15">';

			// Date
			$html .= $this->get_date( [
				'type' => 'published',
			] );

			// Read Time
			$html .= $this->get_estimated_read_time( [
				'before' => '<span>&bull;</span>',
				'minute_text' => esc_html__( '%s min read', 'total' ),
				'second_text' => esc_html__( '%s sec read', 'total' ),
			] );

		$html .= '</div>';

		// Title
		$html .= $this->get_title( [
			'class' => 'wpex-heading wpex-child-inherit-color wpex-text-2xl wpex-font-normal',
		] );

		// Excerpt
		$html .= $this->get_excerpt( [
			'class' => 'wpex-mt-20 wpex-mb-40',
		] );

		// Footer
		$html .= '<div class="wpex-card-footer wpex-text-3 wpex-mt-auto wpex-flex wpex-flex-wrap wpex-justify-between wpex-gap-10">';

			$html .= '<div class="wpex-card-footer__divider wpex-h-1px wpex-w-100 wpex-bg-current wpex-mb-10 wpex-opacity-40"></div>';

			// Comments
			$html .= $this->get_comment_count( [
				'class'      => 'wpex-child-inherit-color wpex-text-xs',
				'link_class' => 'wpex-no-underline',
				'icon'       => 'comment-o',
				'icon_class' => 'wpex-mr-10',
			] );

			if ( ! empty( $this->args['title'] ) ) {
				$escaped_post_title = esc_html( $this->args['title']);
			} else {
				$escaped_post_title = the_title_attribute( [
					'echo' => false,
					'post' => get_post( $this->post_id ),
				] );
			}

			// More Button.
			if ( $this->has_link() ) {
				$html .= $this->get_link_open( [
					'class' => 'wpex-card-more-link wpex-ml-auto wpex-flex wpex-items-center'
				] );
					$html .= '<svg aria-hidden="true xmlns="http://www.w3.org/2000/svg" enable-background="new 0 0 24 24" height="20px" viewBox="0 0 24 24" width="20px" fill="currentColor"><g><rect fill="none" height="24" width="24"/></g><g><polygon points="6,6 6,8 14.59,8 5,17.59 6.41,19 16,9.41 16,18 18,18 18,6"/></g></svg><span class="screen-reader-text">' . sprintf( esc_attr_x( '%s about %s', '*read more text* about *post name* aria label', 'Total' ), esc_html__( 'Read more', 'total' ), $escaped_post_title ) . '</span>';
				$html .= $this->get_link_close();
			}

		$html .= '</div>';

	$html .= '</div>';

$html .= '</div>';

return $html;
