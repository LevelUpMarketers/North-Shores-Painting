<?php

defined( 'ABSPATH') || exit;

$choices = [
	'' => esc_html( '— Select —', 'total-theme-core' ),
];

if ( function_exists( 'acf_get_field_groups' ) && function_exists( 'acf_get_fields' ) ) {
	foreach ( (array) acf_get_field_groups() as $group ) {
		if ( isset( $group['ID'] ) ) {
			foreach ( (array) acf_get_fields( $group['ID'] ) as $field ) {
				if ( isset( $field['type'] ) && 'repeater' === $field['type'] && isset( $field['key'] ) ) {
					$choices[ $field['key'] ] = $field['label'] ?? $field['key'];
				}
			}
		}
    }
}

return $choices;
