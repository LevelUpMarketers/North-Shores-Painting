<?php

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WPEX_Font_Manager' ) ) {

	/**
	 * WPEX Font Manager.
	 */
	final class WPEX_Font_Manager {

		/**
		 * Post type used to store custom fonts.
		 */
		const POST_TYPE = 'wpex_font';

		/**
		 * Check if the font post type is registered.
		 */
		protected $is_post_type_registered = false;

		/**
		 * Check if we have gotten registered fonts or not.
		 */
		protected $registered_fonts_check = false;

		/**
		 * Holds array of registered fonts.
		 */
		protected $registered_fonts = [];

		/**
		 * Instance.
		 */
		private static $instance = null;

		/**
		 * Create or retrieve the instance of WPEX_Font_Manager.
		 */
		public static function instance() {
			if ( null === static::$instance ) {
				static::$instance = new self();
			}
			return static::$instance;
		}

		/**
		 * Private constructor.
		 */
		private function __construct() {
			$this->global_hooks();
			if ( is_admin() && $this->check_user_cap() ) {
				$this->admin_hooks();
			}
		}

		/**
		 * Global hooks.
		 */
		public function global_hooks() {
			add_action( 'init', [ $this, 'register_type' ] );
		}

		/**
		 * Get required user capability.
		 */
		private function get_user_cap(): string {
			return 'edit_theme_options';
		}

		/**
		 * Check user capabilities.
		 */
		private function check_user_cap(): bool {
			return (bool) current_user_can( $this->get_user_cap() );
		}

		/**
		 * Admin hooks.
		 */
		public function admin_hooks() {
			add_action( 'admin_head', [ $this, 'remove_admin_column_filter' ] );
			add_filter( 'manage_' . self::POST_TYPE . '_posts_columns', [ $this, 'set_admin_columns' ] );
			add_action( 'manage_' . self::POST_TYPE . '_posts_custom_column', [ $this, 'show_admin_columns' ], 10, 2 );

			add_action( 'admin_head-post.php', [ $this, 'add_back_button' ] );

			if ( class_exists( 'Vc_Manager' ) ) {
				add_filter( 'vc_is_valid_post_type_be', [ $this, 'disable_wpbakery' ], 10, 2 );
				add_filter( 'vc_show_button_fe', [ $this, 'remove_wpbakery_button_fe' ], 10, 3 );
			}

			if ( class_exists( 'WPEX_Meta_Factory' ) ) {
				add_action( 'admin_init', [ $this, 'register_metaboxes' ] );
			}

			add_filter( 'plupload_default_settings', [ $this, '_filter_plupload_default_settings' ] );
		}

		/**
		 * Register wpex_fonts type.
		 */
		public function register_type() {
			$user_cap = $this->get_user_cap();

			register_post_type( self::POST_TYPE, [
				'labels' => [
					'name'               => esc_html__( 'Font Manager', 'total-theme-core' ),
					'singular_name'      => esc_html__( 'Font', 'total-theme-core' ),
					'add_new'            => esc_html__( 'Add New Font' , 'total-theme-core' ),
					'add_new_item'       => esc_html__( 'Add New Font' , 'total-theme-core' ),
					'edit_item'          => esc_html__( 'Edit Font' , 'total-theme-core' ),
					'new_item'           => esc_html__( 'New Font' , 'total-theme-core' ),
					'view_item'          => esc_html__( 'View Font', 'total-theme-core' ),
					'search_items'       => esc_html__( 'Search Fonts', 'total-theme-core' ),
					'not_found'          => esc_html__( 'No Fonts found', 'total-theme-core' ),
					'not_found_in_trash' => esc_html__( 'No Fonts found in Trash', 'total-theme-core' ),
				],
				'public'          => false,
				'show_ui'         => $this->check_user_cap(),
				'_builtin'        => false,
				'capability_type' => 'page',
				'hierarchical'    => false,
				'rewrite'         => false,
				'supports'        => [ 'title' ],
				'show_in_menu'    => defined( 'WPEX_THEME_PANEL_SLUG' ) ? WPEX_THEME_PANEL_SLUG : 'themes.php',
				// Admins only
				'capabilities' => [
					'edit_post'          => $user_cap,
					'read_post'          => $user_cap,
					'delete_post'        => $user_cap,
					'edit_posts'         => $user_cap,
					'edit_others_posts'  => $user_cap,
					'delete_posts'       => $user_cap,
					'publish_posts'      => $user_cap,
					'read_private_posts' => $user_cap,
				],
			] );

			$this->is_post_type_registered = true;
		}

		/**
		 * Remove the admin columns sort filter.
		 */
		public function remove_admin_column_filter() {
			$screen = get_current_screen();

			if ( self::POST_TYPE == $screen->post_type ) {
				add_filter( 'months_dropdown_results', '__return_empty_array' );
			}
		}

		/**
		 * Set admin columns.
		 */
		public function set_admin_columns( $columns ) {
			$columns['font_name'] = esc_html__( 'Font Name', 'total-theme-core' );
			$columns['type']      = esc_html__( 'Type', 'total-theme-core' );
			$columns['fallback']  = esc_html__( 'Fallback', 'total-theme-core' );
			$columns['assign_to'] = esc_html__( 'Assigned To', 'total-theme-core' );
			$columns['is_global'] = esc_html__( 'Global?', 'total-theme-core' );
			unset( $columns['date'] );
			return $columns;
		}

		/**
		 * Show admin columns.
		 */
		public function show_admin_columns( $column, $post_id ) {
			$inline_style = 'font-size:24px;line-height:normal;';
			switch ( $column ) {
				case 'font_name':
					$type                = get_post_meta( $post_id, 'type', true );
					$font_name           = get_post_meta( $post_id, 'name', true );
					$font_name_sanitized = $this->sanitize_font_name( $font_name, $type );
					$add_font_family_css = false;
					switch ( $type ) {
						case 'custom':
							if ( function_exists( 'wpex_render_custom_font_css' )
								&& $custom_font_css = wpex_render_custom_font_css( $font_name )
							) {
								$add_font_family_css = true;
								echo '<style>' . wp_strip_all_tags( $custom_font_css ) . '</style>';
							}
							break;
						case 'adobe':
						case 'google':
							if ( function_exists( 'wpex_enqueue_font' ) ) {
								$add_font_family_css = true;
								wpex_enqueue_font( $font_name_sanitized );
							}
							break;
					}
					if ( $add_font_family_css ) {
						if ( function_exists( 'wpex_sanitize_font_family' ) ) {
							$font_name_sanitized = wpex_sanitize_font_family( $font_name_sanitized );
						}
						if ( $font_name_sanitized ) {
							$inline_style .= "font-family:{$font_name_sanitized};";
						}
					}
					if ( $inline_style ) {
						echo '<div style="' . esc_attr( $inline_style ) . '">' . sanitize_text_field( $font_name ) . '</div>';
					}
				break;
				case 'type':
					if ( $type = get_post_meta( $post_id, 'type', true ) ) {
						echo esc_html( self::choices_font_types()[ $type ] ?? $type );
					} else {
						echo '&#8212;';
					}
				break;
				case 'fallback':
					if ( $fallback = get_post_meta( $post_id, 'fallback', true ) ) {
						echo esc_html( sanitize_text_field( $fallback ) );
					} else {
						echo '&#8212;';
					}
				break;
				case 'assign_to':
					$assign_to = $this->parse_list( sanitize_text_field( get_post_meta( $post_id, 'assign_to', true ) ) );
					if ( $assign_to ) {
						foreach ( $assign_to as $el ) {
							echo '<p><code>' . esc_html( $el ) . '</code></p>';
						}
					} else {
						echo '&#8212;';
					}
				break;
				case 'is_global':
					$type            = get_post_meta( $post_id, 'type', true );
					$is_global       = (bool) get_post_meta( $post_id, 'is_global', true );
					$has_assigned_to = (bool) get_post_meta( $post_id, 'assign_to', true );

					if ( $is_global || $has_assigned_to || 'custom' === $type ) {
						echo '<span class="dashicons dashicons-yes" aria-hidden="true" style="color:green;"><div class="screen-reader-text">' . esc_html__( 'Yes', 'total-theme-core' ) . '</div>';
					} else {
						echo '<span class="dashicons dashicons-no-alt" aria-hidden="true" style="color:red;"></span><div class="screen-reader-text">' . esc_html__( 'No', 'total-theme-core' ) . '</div>';
					}
				break;
			}
		}

		/**
		 * Add a back button to the Font Manager main page.
		 */
		public function add_back_button() {
			global $current_screen;

			if ( 'wpex_font' !== $current_screen->post_type ) {
				return;
			}

			wp_enqueue_script( 'jQuery' );

			?>

			<script>
				jQuery( function() {
					jQuery( 'body.post-type-wpex_font .wrap h1' ).append( '<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=wpex_font' ) ); ?>" class="page-title-action"><?php esc_html_e( 'Back to Font Manager', 'total-theme-core' ); ?></a>' );
				} );
			</script>
			<?php
		}

		/**
		 * Disable wpbakery builder from post type.
		 */
		public function disable_wpbakery( $check, $type ) {
			if ( self::POST_TYPE === $type ) {
				return false;
			}
			return $check;
		}

		/**
		 * Removes the edit with wpbakery button from the admin screen.
		 */
		public function remove_wpbakery_button_fe( $result, $post_id, $type ) {
			if ( self::POST_TYPE === $type ) {
				return false;
			}
			return $result;
		}

		/**
		 * Register metaboxes.
		 */
		public function register_metaboxes() {
			$metaboxes = [
				$this->general_metabox(),
				$this->google_metabox(),
				$this->adobe_metabox(),
				$this->custom_metabox(),
				$this->assign_metabox(),
			];

			foreach ( $metaboxes as $metabox ) {
				new WPEX_Meta_Factory( $metabox );
			}
		}

		/**
		 * General metabox.
		 */
		protected function general_metabox(): array {
			return [
				'id'       => 'general',
				'title'    => esc_html__( 'Font Settings', 'total-theme-core' ),
				'screen'   => [ self::POST_TYPE ],
				'context'  => 'normal',
				'priority' => 'high',
				'scripts'  => [ self::class, 'general_metabox_scripts' ],
				'fields'   => [ self::class, 'general_metabox_fields' ]
			];
		}

		/**
		 * General metabox scripts.
		 */
		public static function general_metabox_scripts() {
			return [
				[
					'wpex-font-manager',
					totalthemecore_get_js_file( 'admin/font-manager' ),
					[ 'jquery' ],
					'1.0',
					true
				],
			];
		}

		/**
		 * General metabox fields.
		 */
		public static function general_metabox_fields(): array {
			return [
				[
					'name'     => esc_html__( 'Type', 'total-theme-core' ),
					'id'       => 'type',
					'type'     => 'select',
					'required' => true,
					'desc'     => esc_html__( 'Select your font type.', 'total-theme-core' ),
					'choices'  => [ self::class, 'choices_font_types' ],
					'after_hook' => '<a href="https://fonts.google.com/" target="_blank" rel="nofollow noopener noreferrer" class="wpex-visit-google-btn wpex-mf-hidden button button-primary">' . esc_html__( 'Visit Google Fonts', 'total-theme-core' ) . ' &#8599;</a><a href="https://fonts.adobe.com/fonts" target="_blank" rel="nofollow noopener noreferrer" class="wpex-visit-adobe-btn wpex-mf-hidden button button-primary">' . esc_html__( 'Visit Adobe Fonts', 'total-theme-core' ) . ' &#8599;</a>',
				],
				[
					'name'       => esc_html__( 'Font Name', 'total-theme-core' ),
					'id'         => 'name',
					'type'       => 'text',
					'desc'       => esc_html__( 'Your exact font name (case sensitive).', 'total-theme-core' ),
					'required'   => true
				],
				[
					'name'    => esc_html__( 'Font Display', 'total-theme-core' ),
					'id'      => 'display',
					'type'    => 'select',
					'desc'    => esc_html__( 'Select your font-display value.', 'total-theme-core' ),
					'choices' => [ self::class, 'choices_font_display' ],
				],
				[
					'name'    => esc_html__( 'Fallback', 'total-theme-core' ),
					'id'      => 'fallback',
					'type'    => 'select',
					'desc'    => esc_html__( 'Select your fallback font.', 'total-theme-core' ),
					'choices' => [ self::class, 'choices_fallback_fonts' ],
				],
				[
					'name'    => esc_html__( 'Load Font Site Wide?', 'total-theme-core' ),
					'id'      => 'is_global',
					'type'    => 'checkbox',
					'desc'    => esc_html__( 'Check the box to load this font on the entire site.', 'total-theme-core' ),
					'default' => false,
				],
				[
					'name'     => esc_html__( 'Preload Font CSS File?', 'total-theme-core' ),
					'id'       => 'preload',
					'type'     => 'checkbox',
					'default'  => false,
				//	'desc_tip' => true,
					'desc'     => esc_html__( 'You can preload web fonts that are required immediately to improve loading speed. This is recommended for fonts used "above the fold".', 'total-theme-core' ),
				],
			];
		}

		/**
		 * Google metabox.
		 */
		protected function google_metabox(): array {
			return [
				'id'       => 'google',
				'title'    => esc_html__( 'Google Font Settings', 'total-theme-core' ),
				'screen'   => [ self::POST_TYPE ],
				'context'  => 'normal',
				'priority' => 'default',
				'fields'   => [ self::class, 'google_metabox_fields' ]
			];
		}

		/**
		 * Google metabox fields.
		 */
		public static function google_metabox_fields(): array {
			return [
				[
					'name' => esc_html__( 'Load Italics', 'total-theme-core' ),
					'id'   => 'google_italic',
					'type' => 'checkbox',
					'desc' => esc_html__( 'Load italic styles for this font?', 'total-theme-core' ),
				],
				[
					'name'    => esc_html__( 'Font Weights', 'total-theme-core' ),
					'id'      => 'google_font_weights',
					'type'    => 'multi_select',
					'desc'    => esc_html__( 'Select the font weights to load. Make sure to only select font weights available for the desired font family.', 'total-theme-core' ),
					'choices' => [
						'100' => \esc_html__( 'Extra Light: 100', 'total-theme-core' ),
						'200' => \esc_html__( 'Light: 200', 'total-theme-core' ),
						'300' => \esc_html__( 'Book: 300', 'total-theme-core' ),
						'400' => \esc_html__( 'Normal: 400', 'total-theme-core' ),
						'500' => \esc_html__( 'Medium: 500', 'total-theme-core' ),
						'600' => \esc_html__( 'Semibold: 600', 'total-theme-core' ),
						'700' => \esc_html__( 'Bold: 700', 'total-theme-core' ),
						'800' => \esc_html__( 'Extra Bold: 800', 'total-theme-core' ),
						'900' => \esc_html__( 'Black: 900', 'total-theme-core' ),
					],
				],
				[
					'name'    => esc_html__( 'Font Subsets', 'total-theme-core' ),
					'id'      => 'google_subsets',
					'type'    => 'multi_select',
					'desc'    => esc_html__( 'Select the font subsets to load for browsers that do not suppot unicode-range.', 'total-theme-core' ),
					'choices' => [
						'latin'        => 'latin',
						'latin-ext'    => 'latin-ext',
						'cyrillic'     => 'cyrillic',
						'cyrillic-ext' => 'cyrillic-ext',
						'greek'        => 'greek',
						'greek-ext'    => 'greek-ext',
						'vietnamese'   => 'vietnamese',
					],
				],
			];
		}

		/**
		 * Adobe metabox.
		 */
		protected function adobe_metabox(): array {
			return [
				'id'       => 'adobe',
				'title'    => esc_html__( 'Adobe Settings', 'total-theme-core' ),
				'screen'   => [ self::POST_TYPE ],
				'context'  => 'normal',
				'priority' => 'default',
				'fields'   => [ self::class, 'adobe_metabox_fields' ]
			];
		}

		/**
		 * Adobe metabox fields.
		 */
		public static function adobe_metabox_fields(): array {
			return [
				[
					'name' => esc_html__( 'Project ID', 'total-theme-core' ),
					'id'   => 'adobe_project_id',
					'type' => 'text',
					'desc' => esc_html__( 'Enter your adobe project ID.', 'total-theme-core' ),
				],
			];
		}

		/**
		 * Upload files metabox.
		 */
		protected function custom_metabox(): array {
			return [
				'id'       => 'custom',
				'title'    => esc_html__( 'Font Files', 'total-theme-core' ),
				'screen'   => [ self::POST_TYPE ],
				'context'  => 'normal',
				'priority' => 'default',
				'fields'   => [ self::class, 'custom_metabox_fields' ],
			];
		}

		/**
		 * Custom metabox fields.
		 */
		public static function custom_metabox_fields(): array {
			return [
				[
					'id'          => 'custom_fonts',
					'type'        => 'group',
					'name'        => esc_html__( 'Font Variations', 'total-theme-core' ),
					'desc'        => esc_html__( 'Upload or select your custom font files from the Media Library.', 'total-theme-core' ),
					'group_title' => esc_html__( 'Variation', 'total-theme-core' ),
					'group_sort'  => true,
					'fields'      => [
						[
							'name'     => esc_html__( 'Preload', 'total-theme-core' ),
							'id'       => 'preload',
							'type'     => 'checkbox',
							'desc_tip' => true,
							'desc'     => esc_html__( 'You can preload web fonts that are required immediately to improve loading speed. This is recommended for fonts used "above the fold".', 'total-theme-core' ),
						],
						[
							'name'    => esc_html__( 'Font Weight', 'total-theme-core' ),
							'id'      => 'weight',
							'type'    => 'select',
							'choices' => [
								'100' => \esc_html__( 'Extra Light: 100', 'total-theme-core' ),
								'200' => \esc_html__( 'Light: 200', 'total-theme-core' ),
								'300' => \esc_html__( 'Book: 300', 'total-theme-core' ),
								'400' => \esc_html__( 'Normal: 400', 'total-theme-core' ),
								'500' => \esc_html__( 'Medium: 500', 'total-theme-core' ),
								'600' => \esc_html__( 'Semibold: 600', 'total-theme-core' ),
								'700' => \esc_html__( 'Bold: 700', 'total-theme-core' ),
								'800' => \esc_html__( 'Extra Bold: 800', 'total-theme-core' ),
								'900' => \esc_html__( 'Black: 900', 'total-theme-core' ),
							],
						],
						[
							'name'    => esc_html__( 'Font Style', 'total-theme-core' ),
							'id'      => 'style',
							'type'    => 'select',
							'choices' => array(
								'normal' => esc_html__( 'Normal', 'total-theme-core' ),
								'italic' => esc_html__( 'Italic', 'total-theme-core' ),
							),
						],
						[
							'name'       => esc_html__( 'WOFF2 File', 'total-theme-core' ),
							'id'         => 'woff2',
							'type'       => 'upload',
							'media_type' => 'font',
							'preview'    => true,
						],
						[
							'name'       => esc_html__( 'WOFF File (optional)', 'total-theme-core' ),
							'id'         => 'woff',
							'type'       => 'upload',
							'media_type' => 'font',
							'preview'    => true,
						],
					],
				],
			];
		}

		/**
		 * Assign metabox.
		 */
		protected function assign_metabox() {
			return [
				'id'       => 'assign',
				'title'    => esc_html__( 'Target Elements (Optional)', 'total-theme-core' ),
				'screen'   => [ self::POST_TYPE ],
				'context'  => 'normal',
				'priority' => 'default',
				'fields'   => [ self::class, 'assign_metabox_fields' ],
			];
		}

		/**
		 * Asign metabox fields.
		 */
		public static function assign_metabox_fields(): array {
			return [
				[
					'name' => esc_html__( 'Assign Font to Elements', 'total-theme-core' ),
					'id'   => 'assign_to',
					'type' => 'textarea',
					'desc' => esc_html__( 'Enter a list of ID\'s, classnames or element tags to target with this Font Family. Hit enter after each element or separate using commas.', 'total-theme-core' ),
				],
			];
		}

		/**
		 * Return array of font types.
		 */
		public static function choices_font_types(): array {
			return [
				''       => esc_html__( '— Select —', 'total-theme-core' ),
				'google' => 'Google',
				'adobe'  => 'Adobe',
				'custom' => esc_html__( 'Custom/Upload', 'total-theme-core' ),
				'other'  => esc_html__( 'Child Theme or Other', 'total-theme-core' ),
			];
		}

		/**
		 * Return fallback font choices.
		 */
		public static function choices_fallback_fonts(): array {
			$fallback_fonts = [
				''           => esc_html__( 'No Fallback', 'total-theme-core' ),
				'sans-serif' => 'sans-serif',
				'serif'      => 'serif',
				'monospace'  => 'monospace',
				'cursive'    => 'cursive',
			];
			return (array) apply_filters( 'wpex_font_manager_choices_fallback_fonts', $fallback_fonts );
		}

		/**
		 * Return font-display choices.
		 */
		public static function choices_font_display(): array {
			return [
				'swap'     => 'swap',
				'auto'     => 'auto',
				'block'    => 'block',
				'fallback' => 'fallback',
				'optional' => 'optional',
			];
		}

		/**
		 * Return all registered fonts.
		 */
		public function get_registered_fonts() {
			if ( $this->registered_fonts_check && $this->is_post_type_registered ) {
				return $this->registered_fonts;
			}

			// Used to get all posts while on the admin screen so it can still display the preview font in the admin column.
			$is_admin_page = is_admin() && isset( $_GET['post_type'] ) && self::POST_TYPE === $_GET['post_type'];

			$fonts = get_posts( [
				'numberposts' 	   => 50,
				'post_type' 	   => self::POST_TYPE,
				'post_status'      => $is_admin_page ? 'all' : 'publish',
				'suppress_filters' => false,
				'fields'           => 'ids',
			] );

			if ( ! $fonts ) {
				return;
			}

			foreach ( $fonts as $font ) {

				$type = sanitize_text_field( get_post_meta( $font, 'type', true ) );

				if ( ! $type ) {
					continue;
				}

				$name = $this->sanitize_font_name( get_post_meta( $font, 'name', true ), $type );

				if ( ! $name ) {
					continue;
				}

				$font_args = [
					'type' => $type,
				];

				$fallback = sanitize_text_field( get_post_meta( $font, 'fallback', true ) );

				if ( $fallback ) {
					$font_args['fallback'] = $fallback;
				}

				$is_global = wp_validate_boolean( get_post_meta( $font, 'is_global', true ) );

				$assign_to = $this->parse_list( get_post_meta( $font, 'assign_to', true ) );

				if ( $assign_to ) {
					$font_args['assign_to'] = array_map( 'sanitize_text_field', $assign_to );
				}

				switch ( $type ) {

					case 'google';

						$font_args['display'] = $this->sanitize_font_display( get_post_meta( $font, 'display', true ) );

						$font_args['italic']  = (bool) get_post_meta( $font, 'google_italic', true );

						if ( $weights = get_post_meta( $font, 'google_font_weights', true ) ) {
							$font_args['weights'] = (array) $weights;
						}

						if ( $subsets = get_post_meta( $font, 'google_subsets', true ) ) {
							$font_args['subset'] = (array) $subsets;
						}

						if ( $is_global ) {
							$font_args['is_global'] = true;
						}

						$font_args['preload'] = wp_validate_boolean( get_post_meta( $font, 'preload', true ) );

					break;

					case 'adobe';

						if ( $project_id = get_post_meta( $font, 'adobe_project_id', true ) ) {
							$font_args['project_id'] = sanitize_text_field( $project_id );
						}

						if ( $is_global ) {
							$font_args['is_global'] = true;
						}

						$font_args['preload'] = wp_validate_boolean( get_post_meta( $font, 'preload', true ) );

					break;

					case 'custom';

						$files = get_post_meta( $font, 'custom_fonts', true );

						if ( is_array( $files ) && $files ) {
							$font_args['custom_fonts'] = $files;
						}

						$font_args['display'] = $this->sanitize_font_display( get_post_meta( $font, 'display', true ) );

					break;

				}

				$this->registered_fonts[$name] = $font_args;

			} // end foreach

			$this->registered_fonts_check = true;

			return $this->registered_fonts;
		}

		/**
		 * Validate font-display.
		 */
		public function sanitize_font_display( $display = null, $fallback = 'swap' ) {
			if ( ! $display && $fallback ) {
				return $fallback;
			} elseif ( in_array( $display, self::choices_font_display() ) ) {
				return $display;
			}
		}

		/**
		 * Sanitize Font Name.
		 */
		public function sanitize_font_name( $font_name, $type = '' ) {
			switch ( $type ) {
				case 'adobe':
					$font_name = strtolower( str_replace( ' ', '-', $font_name ) );
					break;
			}
			return sanitize_text_field( $font_name );
		}

		/**
		 * parse list.
		 */
		public function parse_list( $list = [] ) {
			if ( is_string( $list ) && $list ) {
				if ( false !== strpos( $list, PHP_EOL ) ) {
					$list = explode( PHP_EOL, $list );
				} else {
					$list = explode( ',', $list );
				}
			}
			if ( is_array( $list ) ) {
				$list = array_map( 'trim', $list );
			}
			return $list;
		}

		/**
		 * Filter the upload settings to allow font files.
		 */
		public function _filter_plupload_default_settings( $settings ) {
			if ( \function_exists( 'get_current_screen' ) ) {
				$current_screen_type = \get_current_screen()->post_type ?? '';
				if ( self::POST_TYPE !== $current_screen_type ) {
					return $settings;
				}
				if ( ! empty( $settings['filters']['mime_types'][0]['extensions'] ) ) {
					$settings['filters']['mime_types'][0]['extensions'] .= ',woff,woff2';
				} else {
					$settings['filters']['mime_types'][0]['extensions'] = 'woff,woff2';
				}
			}
			return $settings;
		}

	}

	WPEX_Font_Manager::instance();

}
