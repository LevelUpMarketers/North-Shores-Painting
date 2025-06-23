<?php

namespace TotalThemeCore\Cpt;

use TotalThemeCore\Custom_Post_Type_Abstract;

\defined( 'ABSPATH' ) || exit;

/**
 * Testimonials Post Type.
 */
if ( class_exists( 'TotalThemeCore\Custom_Post_Type_Abstract' ) ) {
	final class Testimonials extends Custom_Post_Type_Abstract {

		/**
		 * Constructor.
		 */
		public function __construct() {
			parent::__construct( 'testimonials' );
			$this->init_hooks();
		}

		/**
		 * Register action hooks.
		 */
		private function init_hooks(): void {
			if ( \is_admin() ) {
				\add_filter( 'wpex_metabox_array', array( self::class, 'add_meta' ), 5 );
			}
			if ( ! \is_admin() || \wp_doing_ajax() ) {
				\add_action( 'wpex_page_header_title_args', array( self::class, 'alter_title' ) );
				\add_filter( 'wpex_prev_post_link_text', array( self::class, 'prev_post_link_text' ) );
				\add_filter( 'wpex_next_post_link_text', array( self::class, 'next_post_link_text' ) );
			}
		}

		/**
		 * Alters the default page title.
		 */
		public static function alter_title( $args ) {
			if ( \is_singular( 'testimonials' ) ) {
				if ( ! \get_theme_mod( 'testimonials_labels' )
					&& $author = \get_post_meta( \get_the_ID(), 'wpex_testimonial_author', true )
				) {
					$title = \sprintf( \esc_html__( 'Testimonial by: %s', 'total-theme-core' ), $author );
				} else {
					$title = \single_post_title( '', false );
				}
				$args['string']   = $title;
				$args['html_tag'] = 'h1';
			}
			return $args;
		}

		/**
		 * Alter previous post link title.
		 */
		public static function prev_post_link_text( $text ) {
			if ( \is_singular( 'testimonials' ) ) {
				$text = \esc_html__( 'Previous', 'total-theme-core' );
			}
			return $text;
		}

		/**
		 * Alter next post link title.
		 */
		public static function next_post_link_text( $text ) {
			if ( \is_singular( 'testimonials' ) ) {
				$text = \esc_html__( 'Next', 'total-theme-core' );
			}
			return $text;
		}

		/**
		 * Adds testimonials meta options.
		 */
		public static function add_meta( array $meta_settings ): array {
			$meta_settings['testimonials'] = [
				'title'                   => \get_post_type_object( 'testimonials' )->labels->singular_name,
				'post_type'               => [ 'testimonials' ],
				'settings'                => [
					'testimonial_author'  => [
						'title'           => \esc_html__( 'Author', 'total-theme-core' ),
						'description'     => \esc_html__( 'Enter the name of the author for this testimonial.', 'total-theme-core' ),
						'id'              => 'wpex_testimonial_author',
						'type'            => 'text',
					],
					'testimonial_company' => [
						'title'           => \esc_html__( 'Company', 'total-theme-core' ),
						'description'     => \esc_html__( 'Enter the name of the company for this testimonial.', 'total-theme-core' ),
						'id'              => 'wpex_testimonial_company',
						'type'            => 'text',
					],
					'testimonial_url'     => [
						'title'           => \esc_html__( 'Company URL', 'total-theme-core' ),
						'description'     => \esc_html__( 'Enter the URL for the company for this testimonial.', 'total-theme-core' ),
						'id'              => 'wpex_testimonial_url',
						'type'            => 'text',
					],
					'post_rating'         => [
						'title'           => \esc_html__( 'Rating', 'total-theme-core' ),
						'description'     => \esc_html__( 'Enter a rating for this testimonial.', 'total-theme-core' ),
						'id'              => 'wpex_post_rating',
						'type'            => 'number',
						'max'             => '10',
						'min'             => '1',
						'step'            => '0.1',
					],
				],
			];
			return $meta_settings;
		}

		/**
		 * Instance.
		 */
		public static function instance() {
			return new self; // soft deprecated in 1.7.1
		}

		/**
		 * Return testimonials icon.
		 */
		public static function get_admin_menu_icon(): string {
			\_deprecated_function( __METHOD__, 'Total Theme Core 2.0' );
			return 'testimonial';
		}

		/**
		 * Return testimonials name.
		 */
		public static function get_post_type_name(): string {
			\_deprecated_function( __METHOD__, 'Total Theme Core 2.0' );
			return 'Testimonial';
		}

		/**
		 * Return testimonials singular name.
		 */
		public static function get_singular_name(): void {
			\_deprecated_function( __METHOD__, 'Total Theme Core 2.0' );
		}

		/**
		 * Check if the REST API is enabled for the post type.
		 */
		public static function show_in_rest(): void {
			\_deprecated_function( __METHOD__, 'Total Theme Core 2.0' );
		}

		/**
		 * Check if this post type has front-end posts.
		 */
		public static function has_single(): void {
			\_deprecated_function( __METHOD__, 'Total Theme Core 2.0' );
		}

	}
}