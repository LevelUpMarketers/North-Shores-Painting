<?php
/**
 * vcex_tribe_events_data shortcode output.
 *
 * @package Total WordPress Theme
 * @subpackage Total Theme Core
 * @version 1.7.0
 */

defined( 'ABSPATH' ) || exit;

$return = $atts['return'] ?? '';

if ( ! $return || ! 'tribe_event' === get_post_type() ) {
	return;
}

$data          = '';
$sanitize_data = true;
$el_display    = 'inline-block';

switch ( $return ) {
	case 'website':
		if ( function_exists( 'tribe_get_event_website_url' ) ) {
			$data = esc_url( tribe_get_event_website_url() );
		}
		break;
	case 'website_link':
		if ( function_exists( 'tribe_get_event_website_link' ) ) {
			$data = tribe_get_event_website_link();
		}
		break;
	case 'schedule_details':
		if ( function_exists( 'tribe_events_event_schedule_details' ) ) {
			$data = tribe_events_event_schedule_details();
		}
		break;
	case 'start_date':
		if ( function_exists( 'tribe_get_start_date' ) ) {
			$data = esc_html( tribe_get_start_date() );
		}
		break;
	case 'end_date':
		if ( function_exists( 'tribe_get_end_date' ) ) {
			$data = esc_html( tribe_get_end_date() );
		}
		break;
	case 'address':
		$el_display = 'block';
		if ( function_exists( 'tribe_get_address' ) && $address = esc_html( tribe_get_address() ) ) {
			$data .= "<div>{$address}</div>";
		}
		$extra_data = '';
		if ( function_exists( 'tribe_get_city' ) ) {
			$city = tribe_get_city();
			if ( $city ) {
				$extra_data .= "{$city},";
			}
		}
		if ( function_exists( 'tribe_get_region' ) && $region = esc_html( tribe_get_region() ) ) {
			$extra_data .= " {$region}";
		}
		if ( function_exists( 'tribe_get_zip' ) && $zip = esc_html( tribe_get_zip() ) ) {
			$extra_data .= " {$zip}";
		}
		if ( function_exists( 'tribe_get_country' ) && $country = esc_html( tribe_get_country() ) ) {
			$extra_data .= " {$country}";
		}
		if ( $extra_data ) {
			$data .= "<div>{$extra_data}</div>";
		}
		break;
	case 'cost':
		$data = function_exists( 'tribe_get_formatted_cost' ) ? esc_html( tribe_get_formatted_cost() ) : '';
		if ( ! $data ) {
			$data = esc_html_x( 'Free', 'Used when an event doesn\'t have a cost when using the Tribe Events Data element set to display the event cost.', 'total-theme-core' );
		}
		break;
	case 'venue':
		if ( function_exists( 'tribe_get_venue' ) ) {
			$data = esc_html( tribe_get_venue() );
		}
		break;
	case 'venue_region':
		if ( function_exists( 'tribe_get_region' ) ) {
			$data = esc_html( tribe_get_region() );
		}
		break;
	case 'venue_city':
		if ( function_exists( 'tribe_get_city' ) ) {
			$data = esc_html( tribe_get_city() );
		}
		break;
	case 'venu_website':
		if ( function_exists( 'tribe_get_venue_website_url' ) ) {
			$data = esc_url( tribe_get_venue_website_url() );
		}
		break;
	case 'venu_website_link':
		if ( function_exists( 'tribe_get_venue_website_link' ) ) {
			$data = tribe_get_venue_website_link();
		}
		break;
	case 'category':
		$terms = get_the_term_list( get_the_ID(), 'tribe_events_cat', '', ', ', '' );
		if ( $terms ) {
			$data = strip_tags( $terms );
		}
		break;
	case 'category_link':
		$data = get_the_term_list( get_the_ID(), 'tribe_events_cat', '', ', ', '' );
		break;
	case 'phone':
		if ( function_exists( 'tribe_get_phone' ) ) {
			$data = esc_html( tribe_get_phone() );
		}
		break;
	case 'phone_link':
		$phone = function_exists( 'tribe_get_phone' ) ? tribe_get_phone() : '';
		if ( $phone ) {
			$aria_label = sprintf(
				esc_attr_x( 'call event venue at %s', 'arial label text for the Tribe Event Data element Venue Phone Number with Link option.', 'total-theme-core' ),
				$phone
			);
			$data = '<a href="tel:' . esc_attr( $phone ) . '" aria-label="' . $aria_label . '">' . esc_html( $phone ) . '</a>';
		}
		break;
	case 'map':
		$sanitize_data = false;
		$map = tribe_get_embedded_map();
		$el_display = 'block';
		if ( function_exists( 'tribe_get_embedded_map' ) && $map == tribe_get_embedded_map() ) {
			ob_start();
				do_action( 'tribe_events_single_meta_map_section_start' );
					echo $map;
				do_action( 'tribe_events_single_meta_map_section_end' );
			$data = ob_get_clean();
		}
		break;
	case 'organizer':
		if ( function_exists( 'tribe_get_organizer' )
			&& function_exists( 'tribe_has_organizer' )
			&& tribe_has_organizer()
		) {
			$data = esc_html( tribe_get_organizer() );
		}
		break;
	case 'organizer_link':
		if ( function_exists( 'tribe_get_organizer_website_link' )
			&& function_exists( 'tribe_has_organizer' )
			&& tribe_has_organizer()
		) {
			$data = tribe_get_organizer_website_link();
		}
		break;
	case 'organizer_phone':
		if ( function_exists( 'tribe_get_organizer_phone' )
			&& function_exists( 'tribe_has_organizer' )
			&& tribe_get_organizer_phone()
		) {
			$data = esc_html( tribe_get_organizer_phone() );
		}
		break;
	case 'organizer_phone_link':
		if ( function_exists( 'tribe_get_organizer_phone' )
			&& function_exists( 'tribe_has_organizer' )
			&& tribe_get_organizer_phone()
		) {
			$phone = tribe_get_organizer_phone();
			if ( $phone ) {
				$phone_esc_html = esc_html( $phone );
				$phone_esc_attr = esc_attr( $phone );
				$aria_label_safe = sprintf(
					esc_attr_x( 'call event organizer at %s', 'arial label text for the Tribe Event Data element Organizer Phone Number with Link option.', 'total-theme-core' ),
					$phone_esc_attr
				);
				$data = "<a href='tel:{$phone_esc_attr}'' aria-label='{$aria_label_safe}'>{$phone}</a>";
			}
		}
		break;
	case 'organizer_email':
		if ( function_exists( 'tribe_get_organizer_email' )
			&& function_exists( 'tribe_has_organizer' )
			&& tribe_get_organizer_email()
		) {
			$data = esc_html( tribe_get_organizer_email() );
		}
		break;
}

if ( ! $data ) {
	return;
}

if ( $sanitize_data ) {
	$data = wp_kses_post( $data );
}

if ( vcex_validate_att_boolean( 'data_only', $atts ) ) {
	echo wp_strip_all_tags( $data );
	return;
}

$shortcode_class = [
	'vcex-tribe-event-data',
	'vcex-tribe-event-data--' . sanitize_html_class( $return ),
	'vcex-module',
];

if ( ! empty( $atts['text_align'] ) && 'left' !== $atts['text_align'] ) {
	$shortcode_class[] = 'wpex-text-' . sanitize_html_class( $atts['text_align'] );
}

$extra_classes = vcex_get_shortcode_extra_classes( $atts, 'vcex_tribe_events_data' );

if ( $extra_classes ) {
	$shortcode_class = array_merge( $shortcode_class, $extra_classes );
}

$shortcode_class = vcex_parse_shortcode_classes( $shortcode_class, 'vcex_tribe_events_data', $atts );

$output = '<div class="' . esc_attr( trim( $shortcode_class ) ) . '">';

	if ( ! empty( $atts['label'] ) ) {
		$label_safe = vcex_parse_text_safe( $atts['label'] );
		if ( $label_safe ) {
			$label_class = 'vcex-tribe-event-data__label wpex-bold';
			$label_margin = ! empty( $atts['label_margin'] ) ? absint( $atts['label_margin'] ) : 5;
			$label_margin = ( in_array( $el_display, [ 'flex', 'block' ] ) ) ? "wpex-mb-{$label_margin}" : "wpex-mr-{$label_margin}";
			$label_class .= " wpex-{$el_display} {$label_margin}";
			$output .= "<span class='{$label_class}'>{$label_safe}</span>";
		}
	}

	$output .= "<span class='vcex-tribe-event-data__val wpex-{$el_display}'>{$data}</span>";

$output .= '</div>';

// @codingStandardsIgnoreLine
echo $output;
