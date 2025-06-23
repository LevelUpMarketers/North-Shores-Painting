<?php

namespace TotalTheme\Integration;

\defined( 'ABSPATH' ) || exit;

/**
 * Lifter LMS Integration.
 */
class LifterLMS {

	/**
	 * Stores array of plugin post types to prevent extra checks.
	 */
	protected $post_types = null;

	/**
	 * Instance.
	 */
	private static $instance = null;

	/**
	 * Create or retrieve the instance of Learn_Dash.
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
		if ( \is_customize_preview() ) {
			\add_filter( 'wpex_customizer_panels', [ $this, '_add_customizer_settings' ] );
		}

		\remove_action( 'lifterlms_before_main_content', 'lifterlms_output_content_wrapper', 10 );
		\remove_action( 'lifterlms_after_main_content', 'lifterlms_output_content_wrapper_end', 10 );
		\add_filter( 'lifterlms_before_main_content', [ $this, '_on_lifterlms_before_main_content' ] );
		\add_filter( 'lifterlms_after_main_content', [ $this, '_on_lifterlms_after_main_content' ], 10 );

		\add_action( 'wp_enqueue_scripts', [ $this, '_on_wp_enqueue_scripts' ] );
		\add_filter( 'wpex_post_layout_class', [ $this, '_filter_post_layout' ] );
		\add_filter( 'wpex_get_post_type_cat_tax', [ $this, '_filter_post_type_taxonomy' ], 10, 2 );
		\add_filter( 'wpex_breadcrumbs_trail', [ $this, '_filter_breadcrumbs' ] );
		\add_action( 'widgets_init', [ $this, '_on_widgets_init' ], 6 );

		if ( \totaltheme_is_integration_active( 'wpbakery' ) ) {
			\add_filter( 'vc_check_post_type_validation', [ $this, '_disable_wpbakery' ], 10, 2 );
		}
	}

	/**
	 * Hooks into lifterlms_before_main_content.
	 */
	public function _on_lifterlms_before_main_content(): void {
		?>
		<div id="content-wrap" <?php totaltheme_content_wrap_class(); ?>>

			<?php wpex_hook_primary_before(); ?>

			<div id="primary" class="content-area wpex-clr">

				<?php wpex_hook_content_before(); ?>

				<div id="content" class="site-content wpex-clr">

					<?php wpex_hook_content_top(); ?>
		<?php
	}

	/**
	 * Hooks into lifterlms_after_main_content.
	 */
	public function _on_lifterlms_after_main_content(): void {
		?>
				<?php wpex_hook_content_bottom(); ?>
				</div>
				<?php wpex_hook_content_after(); ?>
			</div>
			<?php wpex_hook_primary_after(); ?>
		</div>
		<?php
	}

	/**
	 * Hooks into wp_enqueue_scripts.
	 *
	 * @note using lifterlms() won't work because the filter doesn't correctly
	 * check for pages like the purchase page.
	 */
	public function _on_wp_enqueue_scripts(): void {
		\wp_enqueue_style( 'wpex-lifterlms', totaltheme_get_css_file( 'frontend/lifterlms' ), [], WPEX_THEME_VERSION );
	}

	/**
	 * Filter post layout.
	 */
	public function _filter_post_layout( $layout ) {
		if ( $this->is( 'lifterlms' ) ) {
			if ( is_singular() ) {
				if ( $default_layout = \get_theme_mod( 'llms_single_layout' ) ) {
					$post_type = \get_post_type();
					if ( ! get_theme_mod( "{$post_type}_single_layout" ) ) {
						$layout = $default_layout;
					}
				}
			} else {
				$layout = \get_theme_mod( 'llms_archive_layout' ) ?: $layout;
			}
		}
		return $layout;
	}
	
	/**
	 * Filter post type taxonomy.
	 */
	public function _filter_post_type_taxonomy( $tax, $post_type ) {
		if ( 'course' === $post_type ) {
			$tax = 'course_cat';
		}
		return $tax;
	}

	/**
	 * Filter breadcrumbs trail.
	 */
	public function _filter_breadcrumbs( $trail ) {
		if ( $this->is( 'lesson' ) ) {
			if ( $courses_archive = \get_post_type_archive_link( 'course' ) ) {
				$trail['post_type_archive'] = \WPEX_Breadcrumbs::get_crumb_html( \get_post_type_object( 'course' )->labels->name ?? '', $courses_archive, 'trail-courses' );
			}
		}
		if ( $this->is( 'lesson' ) || $this->is( 'quiz' ) ) {
			if ( \function_exists( 'llms_get_post_parent_course' ) && $course = \llms_get_post_parent_course( \get_post() ) ) {
				$course_id = $course->get( 'id' ) ?? '';
				if ( $course_id ) {
					$trail['pre_trail_end'] = \WPEX_Breadcrumbs::get_crumb_html( \get_the_title( $course_id ), \get_permalink( $course_id ), 'trail-course' );
				}
			}
		}
		return $trail;
	}

	/**
	 * Disable wpbakery for certain post types.
	 */
	public function _disable_wpbakery( $check, $type ) {
		if ( 'llms_engagement' === $type
			|| 'llms_achievement' === $type
			|| 'llms_my_achievement' === $type
			|| 'llms_certificate' === $type
		//	|| 'llms_email' === $type
		) {
			$check = false;
		}
		return $check;
	}

	/**
	 * Conditional check.
	 */
	protected function is( string $what ): bool {
		$callback = "is_{$what}";
		return \function_exists( $callback ) && $callback();
	}

	/**
	 * Check if it's a course archive.
	 */
	protected function is_course_archive(): bool {
		return $this->is( 'courses' ) || $this->is( 'course_taxonomy' );
	}

	/**
	 * Returns array of plugin types.
	 */
	protected function get_post_types(): array {
		if ( null === $this->post_types ) {
			$this->post_types = \array_filter( [
				'course',
				'lesson',
				'llms_quiz',
				'llms_membership',
			], 'post_type_exists' );
		}
		return $this->post_types;
	}

	/**
	 * Hooks into widgets_init/
	 */
	public function _on_widgets_init(): void {
		unregister_sidebar( 'llms_course_widgets_side' );
		unregister_sidebar( 'llms_lesson_widgets_side' );

		if ( \wp_validate_boolean( \get_theme_mod( 'llms_custom_sidebar', true ) ) ) {
			\totaltheme_call_static( 'Sidebars\Primary', 'register_sidebar', [
				'id'          => 'lifterlms_sidebar',
				'name'        => \esc_html__( 'LifterLMS', 'total' ),
				'condition'   => 'is_lifterlms',
			] );
		}

		if ( \wp_validate_boolean( \get_theme_mod( 'course_custom_sidebar', true ) ) ) {
			\totaltheme_call_static( 'Sidebars\Primary', 'register_sidebar', [
				'id'          => 'llms_course_widgets_side',
				'name'        => \esc_html__( 'Course', 'lifterlms' ),
				'condition'   => 'is_course',
			] );
		}

		if ( \wp_validate_boolean( \get_theme_mod( 'lesson_custom_sidebar', true ) ) ) {
			\totaltheme_call_static( 'Sidebars\Primary', 'register_sidebar', [
				'id'          => 'llms_lesson_widgets_side',
				'name'        => \esc_html__( 'Lesson', 'lifterlms' ),
				'condition'   => 'is_lesson',
			] );
		}

		if ( \wp_validate_boolean( \get_theme_mod( 'llms_quiz_custom_sidebar', true ) ) ) {
			\totaltheme_call_static( 'Sidebars\Primary', 'register_sidebar', [
				'id'          => 'llms_quiz_widgets_side',
				'name'        => \esc_html__( 'Quiz', 'lifterlms' ),
				'condition'   => 'is_quiz',
			] );
		}

		if ( \wp_validate_boolean( \get_theme_mod( 'llms_membership_custom_sidebar', true ) ) ) {
			\totaltheme_call_static( 'Sidebars\Primary', 'register_sidebar', [
				'id'          => 'llms_membership_widgets_side',
				'name'        => \esc_html__( 'Membership', 'lifterlms' ),
				'condition'   => 'is_membership',
			] );
		}

	}

	/**
	 * Adds Customizer settings.
	 */
	public function _add_customizer_settings( $panels ) {
		$branding = ( $branding = \wpex_get_theme_branding() ) ? " ({$branding})" : '';
		$panels['totaltheme_lifterlms'] = [
			'title'      => "Lifter LMS{$branding}",
			'icon'       => "data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9Im5vIj8+PCFET0NUWVBFIHN2ZyBQVUJMSUMgIi0vL1czQy8vRFREIFNWRyAxLjEvL0VOIiAiaHR0cDovL3d3dy53My5vcmcvR3JhcGhpY3MvU1ZHLzEuMS9EVEQvc3ZnMTEuZHRkIj48c3ZnIHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiIHZpZXdCb3g9IjAgMCA4NSA4NCIgdmVyc2lvbj0iMS4xIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIiB4bWw6c3BhY2U9InByZXNlcnZlIiB4bWxuczpzZXJpZj0iaHR0cDovL3d3dy5zZXJpZi5jb20vIiBzdHlsZT0iZmlsbC1ydWxlOmV2ZW5vZGQ7Y2xpcC1ydWxlOmV2ZW5vZGQ7c3Ryb2tlLWxpbmVqb2luOnJvdW5kO3N0cm9rZS1taXRlcmxpbWl0OjEuNDE0MjE7Ij48cmVjdCBpZD0iSWNvbi1Db2xvci0tU3F1YXJlLSIgc2VyaWY6aWQ9Ikljb24gQ29sb3IgKFNxdWFyZSkiIHg9IjAuMDE1IiB5PSIwIiB3aWR0aD0iODQiIGhlaWdodD0iODQiIHN0eWxlPSJmaWxsOm5vbmU7Ii8+PGcgaWQ9Ikljb24iPjxwYXRoIGQ9Ik0yOS4wNjEsNTAuNDYybC0yLjI1OCwtMS4yOWwtNi4wNjYsMTAuNDUyYy01LjQ4MywtNy42MTMgLTYuNTgsLTE3Ljg3MyAtMi4zMjIsLTI2LjcxMmwwLjA2NCwtMC4wNjVjMC4yNTgsLTAuNTggMC41ODEsLTEuMDk3IDAuODM5LC0xLjYxM2M0LjMyMywtNy40ODUgMTEuODczLC0xMi4wNjYgMTkuODczLC0xMi45MDVjMS40MiwtMS45MzUgMi45NjksLTMuNjEzIDQuNzExLC01LjIyNmMtMTEuNDIxLC0wLjY0NSAtMjIuODQzLDUuMDMyIC0yOC45NzIsMTUuNjE1Yy03Ljg3MiwxMy42NzkgLTQuMjU4LDMwLjg0MSA3Ljg3Miw0MC4yNjNsNi4wNjUsLTE4LjAwM2MwLjA2NSwtMC4xMjggMC4xMywtMC4zMjMgMC4xOTQsLTAuNTE2bTM2LjkwOCwtMTYuNzEyYzMuMjI3LDcuNDIxIDMuMDMzLDE2LjE5NSAtMS4yOTEsMjMuNjgxYy0wLjI1NywwLjUxNiAtMC41OCwxLjAzMSAtMC45MDMsMS41NDhsLTAuMDY0LDAuMDY2Yy01LjU0OSw4LjEyOSAtMTQuOTcsMTIuMzIzIC0yNC4zMjYsMTEuMzU1bDYuMDY2LC0xMC40NTNsLTIuMjU5LC0xLjI5MWMtMC4xMjksMC4xMzEgLTAuMjU4LDAuMjU5IC0wLjM4NywwLjM4OWwtMTIuNTE4LDE0LjI1OWMxNC4xOTYsNS44MDggMzAuOTA3LDAuMzIzIDM4Ljc3OSwtMTMuMzU3YzYuMTMsLTEwLjU4MSA1LjM1NiwtMjMuMjkzIC0wLjk2NywtMzIuODQyYy0wLjUxNywyLjI1OCAtMS4xNjIsNC41MTYgLTIuMTMsNi42NDUiIHN0eWxlPSJmaWxsOiM0NjZkZDg7Ii8+PHBhdGggZD0iTTQ0Ljk5OSw1MC4wNzRjLTEuNjE0LDIuMTMgLTQuMTk0LDMuMjI4IC02Ljk2OCwzLjQ4NmMtMC44MzksMC4wNjUgLTEuNjE0LC0wLjM4OCAtMi4wMDEsLTEuMTYyYy0xLjE2MiwtMi41MTcgLTEuNTQ4LC01LjI5MSAtMC40NTEsLTcuNzQzbC0xMi42NDgsLTcuMjkxYy0wLjgzOCwtMC41MTYgLTEuMjI1LC0xLjM1NSAtMC45NjcsLTIuMjU4YzAuMTkzLC0wLjkwNCAwLjk2NywtMS41NSAxLjg3MSwtMS41NWwxMi44NCwtMC40NWMwLjk2OCwtMy45MzcgMi41ODEsLTcuNjc5IDQuOTA0LC0xMS4xNjRjMy42NzgsLTUuNDg0IDguOTA0LC05LjU0OSAxNS4wMzQsLTEyLjAwMWMxLjQ4NSwtMC41ODEgMi45NjgsLTEuMDk2IDQuNDUzLC0xLjQ4NGMxLjA5NiwtMC4yNTggMi4xOTMsMC4zODggMi40NTEsMS40MjFjMC40NTIsMS40ODMgMC43NzUsMy4wMzIgMS4wMzMsNC41OGMwLjkwMyw2LjU4MiAtMC4wNjUsMTMuMTYzIC0yLjkwMywxOS4wOThjLTEuODA3LDMuNzQ0IC00LjMyNCw2Ljk3IC03LjIyOCw5LjgwOGw2LjAwMSwxMS4yOTJjMC40NTIsMC44MzkgMC4zMjMsMS44MDcgLTAuMzg3LDIuNDUyYy0wLjY0NSwwLjY0NiAtMS42MTQsMC43MSAtMi4zODcsMC4yNThsLTEyLjY0NywtNy4yOTJabTkuNTQ5LC0yNy4wMzVjMS45MzYsMS4xNjIgMi41ODEsMy42MTQgMS40ODUsNS41NDljLTEuMDk4LDEuOTM2IC0zLjYxMywyLjU4MiAtNS41NSwxLjQ4NWMtMS45MzUsLTEuMDk4IC0yLjU4LC0zLjYxNCAtMS40ODQsLTUuNTVjMS4xNjIsLTEuOTM1IDMuNjE0LC0yLjU4MSA1LjU0OSwtMS40ODQiIHN0eWxlPSJmaWxsOiMyMjk1ZmY7Ii8+PHBhdGggZD0iTTI2LjA5Myw3MS45NDlsMTMuNjc5LC0xNS41NTFjLTAuNTE2LDAuMDY1IC0xLjAzMiwwLjEyOSAtMS41NDksMC4xOTRjLTIuMDY0LDAuMTI5IC00LC0wLjk2OCAtNC45MDIsLTIuOTAzYy0wLjI1OSwtMC40NTIgLTAuNDUzLC0wLjkwNCAtMC42NDYsLTEuNDJsLTYuNTgyLDE5LjY4WiIgc3R5bGU9ImZpbGw6I2Y4OTU0ZjsiLz48L2c+PC9zdmc+",
			'sections_callback' => [ $this, '_get_customizer_sections' ]
		];
		return $panels;
	}

	/**
	 * Returns customizer sections.
	 */
	public function _get_customizer_sections(): array {
		$sections = [
			'general' => [
				'title' => \esc_html__( 'General', 'total' ),
				'settings' => [
					[
						'id' => 'llms_custom_sidebar',
						'default' => true,
						'control' => [
							'label' => \esc_html__( 'Custom Sidebar', 'total' ),
							'type' => 'checkbox',
						],
					],
					[
						'id' => 'llms_archive_layout',
						'control' => [
							'label' => \esc_html__( 'Archives Layout', 'total' ),
							'type' => 'select',
							'choices' => 'post_layout',
						],
					],
					[
						'id' => 'llms_single_layout',
						'control' => [
							'label' => \esc_html__( 'Single Layout', 'total' ),
							'type' => 'select',
							'choices' => 'post_layout',
						],
					],
				],
			],
		];

		foreach ( $this->get_post_types() as $post_type ) {
			$name = \get_post_type_object( $post_type )->labels->name ?? '';
			if ( ! $name ) {
				continue;
			}
			$sections[ $post_type ] = [
				'title' => \esc_html( $name ),
				'settings' => [
					[
						'id' => "{$post_type}_custom_sidebar",
						'default' => true,
						'control' => [
							'label' => \esc_html__( 'Custom Sidebar', 'total' ),
							'type' => 'checkbox',
						],
					],
					[
						'id' => "{$post_type}_next_prev",
						'default' => true,
						'control' => [
							'label' => \esc_html__( 'Next/Previous Links', 'total' ),
							'type' => 'checkbox',
						],
					],
					[
						'id' => "{$post_type}_single_layout",
						'control' => [
							'label' => \esc_html__( 'Layout', 'total' ),
							'type' => 'select',
							'choices' => 'post_layout',
						],
					],
					[
						'id' => "{$post_type}_single_blocks",
						'default' => \totaltheme_call_static( 'CPT\Single_Blocks', 'default_blocks' ),
						'control' => [
							'label' => \esc_html__( 'Post Blocks', 'total' ),
							'type' => 'totaltheme_blocks',
							'choices' => \totaltheme_call_static( 'CPT\Single_Blocks', 'choices' ),
						],
					],
					[
						'id' => "{$post_type}_single_meta_blocks",
						'default' => \totaltheme_call_static( 'CPT\Meta_Blocks', 'default_blocks' ),
						'control' => [
							'label' => \esc_html__( 'Meta Blocks', 'total' ),
							'type' => 'totaltheme_blocks',
							'choices' => \totaltheme_call_static( 'CPT\Meta_Blocks', 'choices' ),
						],
					],
				],
			];
		}

		return $sections;
	}

	/**
	 * Prevent cloning.
	 */
	private function __clone() {}

	/**
	 * Prevent unserializing.
	 */
	public function __wakeup() {
		\trigger_error( 'Cannot unserialize a Singleton.', \E_USER_WARNING);
	}

}
