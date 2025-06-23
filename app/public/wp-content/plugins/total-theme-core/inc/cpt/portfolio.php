<?php

namespace TotalThemeCore\Cpt;

use TotalThemeCore\Custom_Post_Type_Abstract;

\defined( 'ABSPATH' ) || exit;

/**
 * Portfolio Post Type.
 */
if ( class_exists( 'TotalThemeCore\Custom_Post_Type_Abstract' ) ) {
	final class Portfolio extends Custom_Post_Type_Abstract {

		/**
		 * Constructor.
		 */
		public function __construct() {
			parent::__construct( 'portfolio' );
			$this->init_hooks();
		}
		
		/**
		 * Register action hooks.
		 */
		private function init_hooks(): void {
			if ( \is_admin() ) {
				\add_filter( 'wpex_metabox_array', array( self::class, 'add_meta' ), 5, 2 );
			}

			// Register translation strings - @todo deprecate, it's only used for portfolio for some reason
			// and it's not needed because you can translate registered post types.
			\add_filter( 'wpex_register_theme_mod_strings', array( self::class, 'register_theme_mod_strings' ) );

		}

		/**
		 * Register portfolio theme mod strings.
		 */
		public static function register_theme_mod_strings( array $strings ): array {
			if ( \is_array( $strings ) ) {
				$strings['portfolio_labels']        = 'Portfolio';
				$strings['portfolio_singular_name'] = 'Portfolio Item';
			}
			return $strings;
		}

		/**
		 * Adds portfolio meta options.
		 */
		public static function add_meta( $meta_settings, $post ): array {
			$meta_settings['portfolio'] = array(
				'title'     => \get_post_type_object( 'portfolio' )->labels->singular_name ?? '',
				'post_type' => array( 'portfolio' ),
				'settings'  => array(
					'budget'           => array(
						'title'        => \esc_html__( 'Budget', 'total-theme-core' ),
						'id'           => 'wpex_portfolio_budget',
						'type'         => 'text',
					),
					'company'          => array(
						'title'        => \esc_html__( 'Company Name', 'total-theme-core' ),
						'id'           => 'wpex_portfolio_company',
						'type'         => 'text',
					),
					'url'              => array(
						'title'        => \esc_html__( 'Company URL', 'total-theme-core' ),
						'id'           => 'wpex_portfolio_url',
						'type'         => 'text',
					),
					'featured_video'   => array(
						'title'        => \esc_html__( 'oEmbed URL', 'total-theme-core' ),
						'description'  => \esc_html__( 'Enter a URL that is compatible with WP\'s built-in oEmbed feature. This setting is used for your video and audio post formats.', 'total-theme-core' ) . '<br><a href="http://codex.wordpress.org/Embeds" target="_blank">' . \esc_html__( 'Learn More', 'total-theme-core' ) . ' &rarr;</a>',
						'id'           => 'wpex_post_video',
						'type'         => 'text',
					),
					'post_video_embed' => array(
						'title'        => \esc_html__( 'Embed Code', 'total-theme-core' ),
						'description'  => \esc_html__( 'Insert your embed/iframe code.', 'total-theme-core' ),
						'id'           => 'wpex_post_video_embed',
						'type'         => 'iframe',
						'rows'         => 4,
					),
				),
			);
			return $meta_settings;
		}

		/**
		 * Instance.
		 */
		public static function instance() {
			return new self; // soft deprecated in 1.7.1
		}

		/**
		 * Return portfolio icon.
		 */
		public static function get_admin_menu_icon(): string {
			\_deprecated_function( __METHOD__, 'Total Theme Core 2.0' );
			return 'portfolio';
		}

		/**
		 * Return portfolio name.
		 */
		public static function get_post_type_name(): string {
			\_deprecated_function( __METHOD__, 'Total Theme Core 2.0' );
			return 'Portfolio';
		}

		/**
		 * Return portfolio singular name.
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
