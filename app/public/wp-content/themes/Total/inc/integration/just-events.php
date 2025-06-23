<?php

namespace TotalTheme\Integration;

\defined( 'ABSPATH' ) || exit;

/**
 * Just Events Integration.
 */
class Just_Events {

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Init.
	 */
	public static function init(): void {
		// Global.
		\add_filter( 'totaltheme/overlays/styles', [ self::class, 'filter_overlay_styles' ] );

		// Admin only.
		if ( \wpex_is_request( 'admin' ) ) {
			\add_action( 'admin_init', [ self::class, 'on_admin_init' ] );
			\add_filter( 'wpex_gallery_metabox_post_types', [ self::class, 'filter_gallery_metabox_types' ] );
		}

		// Frontend only.
		if ( \wpex_is_request( 'frontend' ) ) {
			\add_filter( 'wpex_title', [ self::class, 'filter_title' ] );
			\add_filter( 'wpex_post_layout_class', [ self::class, 'filter_post_layout' ] );
			\add_filter( 'wpex_get_grid_entry_columns', [ self::class, 'filter_grid_entry_columns' ], 10, 2 );
			\add_filter( 'wpex_has_next_prev', [ self::class, 'filter_next_prev_check' ] );
			\add_filter( 'wpex_get_post_type_cat_tax', [ self::class, 'filter_primary_taxonomy' ], 10, 2 );
			\add_filter( 'totaltheme/page/header/style', [ self::class, 'filter_page_header_style' ] );
			\add_filter( 'totaltheme/cpt/single_blocks', [ self::class, 'filter_single_blocks' ], 10, 2 );
			\add_filter( 'totaltheme/cpt/entry_blocks', [ self::class, 'filter_entry_blocks' ], 10, 2 );
			\add_filter( 'totaltheme/cpt/meta_blocks/entry_blocks', [ self::class, 'filter_meta_blocks' ], 10, 2 );
			\add_filter( 'totaltheme/cpt/meta_blocks/singular_blocks', [ self::class, 'filter_meta_blocks' ], 10, 2 );
			\add_filter( 'totaltheme/theme_builder/location_template_id', [ self::class, 'filter_location_template_id' ], 10, 2 );
		}
	}

	/**
	 * Adds new overlay styles.
	 */
	public static function filter_overlay_styles( array $styles ): array {
		return \array_merge( $styles, [
			'just-events-date' => [
				'name' => \esc_html__( 'Event Date: Top Right', 'total' ),
			],
			'just-events-date-br' => [
				'name'     => \esc_html__( 'Event Date: Bottom Right', 'total' ),
				'template' => 'just-events-date',
				'args'     => [ 'align' => 'bottom_right' ],
			],
			'just-events-date-tl' => [
				'name'     => \esc_html__( 'Event Date: Top Left', 'total' ),
				'template' => 'just-events-date',
				'args'     => [ 'align' => 'top_left' ],
			],
			'just-events-date-bl' => [
				'name'     => \esc_html__( 'Event Date: Bottom Left', 'total' ),
				'template' => 'just-events-date',
				'args'     => [ 'align' => 'bottom_left' ],
			],
			'just-events-date-dark' => [
				'name'     => \esc_html__( 'Event Date: Top Right Dark', 'total' ),
				'template' => 'just-events-date',
				'args'     => [ 'color_scheme' => 'wpex-surface-dark' ],
			],
			'just-events-date-dark-br'=> [
				'name'     => \esc_html__( 'Event Date: Bottom Right Dark', 'total' ),
				'template' => 'just-events-date',
				'args'     => [ 'color_scheme' => 'wpex-surface-dark', 'align' => 'bottom_right' ],
			],
			'just-events-date-dark-tl' => [
				'name'     => \esc_html__( 'Event Date: Top Left Dark', 'total' ),
				'template' => 'just-events-date',
				'args'     => [ 'color_scheme' => 'wpex-surface-dark', 'align' => 'top_left' ],
			],
			'just-events-date-dark-bl' => [
				'name'     => \esc_html__( 'Event Date: Bottom Left Dark', 'total' ),
				'template' => 'just-events-date',
				'args'     => [ 'color_scheme' => 'wpex-surface-dark', 'align' => 'bottom_left' ],
			],
		] );
	}

	/**
	 * Runs on the "admin_menu" hook.
	 */
	public static function on_admin_init(): void {
		\add_filter( 'just_events/admin/settings', [ self::class, 'filter_settings' ], 1000 );
	}

	/**
	 * Filters the gallery metabox post types.
	 */
	public static function filter_gallery_metabox_types( array $types ): array {
		$types[] = 'just_event';
		return $types;
	}

	/**
	 * Runs on the "admin_menu" hook.
	 */
	public static function filter_settings( array $settings ): array {
		$new_settings = [
			[
				'id'          => 'totaltheme_events_page',
				'label'       => \esc_html__( 'Main Page', 'total' ),
				'description' => \esc_html__( 'Used for breadcrumbs.', 'total' ),
				'tab'         => \esc_html__( 'Theme', 'just-events' ),
				'type'        => 'select',
				'choices'     => self::page_select(),
			],
			[
				'id'          => 'totaltheme_primary_taxonomy',
				'label'       => \esc_html__( 'Main Taxonomy', 'total' ),
				'description' => \esc_html__( 'Used for breadcrumbs, post meta categories and related items.', 'total' ),
				'tab'         => \esc_html__( 'Theme', 'just-events' ),
				'type'        => 'select',
				'choices'     => self::tax_select(),
			],
			[
				'id'          => 'totaltheme_archive_template',
				'label'       => \esc_html__( 'Archive Template', 'total' ),
				'tab'         => \esc_html__( 'Theme', 'just-events' ),
				'type'        => 'select',
				'choices'     => self::select_template( 'archive' ),
			],
			[
				'id'          => 'totaltheme_archive_layout',
				'label'       => \esc_html__( 'Archive Layout', 'total' ),
				'tab'         => \esc_html__( 'Theme', 'just-events' ),
				'type'        => 'select',
				'choices'     => \wpex_get_post_layouts(),
			],
			[
				'id'          => 'totaltheme_single_template',
				'label'       => \esc_html__( 'Post Template', 'total' ),
				'tab'         => \esc_html__( 'Theme', 'just-events' ),
				'type'        => 'select',
				'choices'     => self::select_template( 'single' ),
			],
			[
				'id'          => 'totaltheme_single_layout',
				'label'       => \esc_html__( 'Post Layout', 'total' ),
				'tab'         => \esc_html__( 'Theme', 'just-events' ),
				'type'        => 'select',
				'choices'     => \wpex_get_post_layouts(),
			],
			[
				'id'          => 'totaltheme_single_page_header_style',
				'label'       => \esc_html__( 'Post Title Style', 'total' ),
				'tab'         => \esc_html__( 'Theme', 'just-events' ),
				'type'        => 'select',
				'choices'     => 'TotalTheme\Page\Header::style_choices',
			],
			[
				'id'          => 'totaltheme_single_page_header_title',
				'label'       => \esc_html__( 'Post Title', 'total' ),
				'description' => \sprintf( \esc_html__( 'This field supports %sdynamic variables%s', 'total-theme-core' ), '<a href="https://totalwptheme.com/docs/dynamic-variables/" target="_blank" rel="noopener noreferrer">', '&#8599;</a>' ),
				'tab'         => \esc_html__( 'Theme', 'just-events' ),
				'type'        => 'text',
			],
			[
				'id'          => 'totaltheme_single_next_prev',
				'label'       => \esc_html__( 'Post Next/Prev', 'total' ),
				'tab'         => \esc_html__( 'Theme', 'just-events' ),
				'type'        => 'checkbox',
				'default'     => true,
			],
		];
		return \array_merge( $settings, $new_settings );
	}

	/**
	 * Modify Title.
	 */
	public static function filter_title( $title ) {
		if ( \is_singular( 'just_event' ) ) {
			$title = self::get_option( 'single_page_header_title' ) ?: $title;
		}
		return $title;
	}

	/**
	 * Modify Layouts.
	 */
	public static function filter_post_layout( $layout ) {
		if ( \is_singular( 'just_event' ) ) {
			$layout = self::get_option( 'single_layout', $layout );
		} elseif ( \function_exists( '\Just_Events\is_archive' ) && \Just_Events\is_archive() ) {
			$layout = self::get_option( 'archive_layout', 'full-width' );
		}
		return $layout;
	}

	/**
	 * Modify page header style.
	 */
	public static function filter_page_header_style( $style ) {
		if ( \is_singular( 'just_event' ) ) {
			$style = self::get_option( 'single_page_header_style' ) ?: $style;
		}
		return $style;
	}

	/**
	 * Modify single blocks.
	 */
	public static function filter_single_blocks( $blocks, $post_type ) {
		if ( 'just_event' === $post_type ) {
			$custom_title = (string) self::get_option( 'single_page_header_title' );
			if ( $custom_title && \str_contains( $custom_title, '{{title}}' ) ) {
				$blocks = [ 'media', 'meta', 'content', [ self::class, 'single_button_block' ], 'share' ];
			} else {
				$blocks = [ 'media', 'title', 'meta', 'content', [ self::class, 'single_button_block' ], 'share' ];
			}
		}
		return $blocks;
	}

	/**
	 * Modify meta blocks.
	 */
	public static function filter_meta_blocks( $blocks, $post_type ) {
		if ( 'just_event' === $post_type ) {
			return [ 'date-event', 'categories' ];
		}
		return $blocks;
	}

	/**
	 * Filters the theme builder location template ID.
	 */
	public static function filter_location_template_id( $template_id, $location ) {
		switch ( $location ) {
			case 'archive':
				if ( \function_exists( '\Just_Events\is_archive' ) && \Just_Events\is_archive() ) {
					$template_id = self::get_option( 'archive_template' );
				}
				break;
			case 'single':
				if ( 'just_event' === \get_post_type() ) {
					$template_id = self::get_option( 'single_template' );
				}
				break;
		}
		return $template_id;
	}

	/**
	 * Filters whether the next/previous links should display.
	 */
	public static function filter_next_prev_check( $check ): bool {
		if ( 'just_event' === \get_post_type() ) {
			$check = self::get_option( 'single_next_prev', true );
		}
		return $check;
	}

	/**
	 * Filters the main taxonomy.
	 */
	public static function filter_primary_taxonomy( $taxonomy, $post_type ): string {
		if ( 'just_event' === $post_type ) {
			$taxonomy = self::get_option( 'primary_taxonomy', '' );
		}
		return $taxonomy;
	}

	/**
	 * Filters the entry blocks.
	 */
	public static function filter_grid_entry_columns( $columns, $post_type ) {
		if ( 'just_event' === $post_type ) {
			return '3';
		}
		return $columns;
	}

	/**
	 * Filters the entry blocks.
	 */
	public static function filter_entry_blocks( $blocks, $post_type ): array {
		if ( 'just_event' === $post_type ) {
			return [
				'media'    => 'media',
				'title'    => 'title',
				'meta'     => 'meta',
				'content'  => 'content',
			];
		}
		return $blocks;
	}

	/**
	 * Returns option value.
	 */
	public static function get_option( string $key, $default = '' ) {
		return \get_option( 'just_events' )[ "totaltheme_{$key}" ] ?? $default;
	}

	/**
	 * Renders the single button block.
	 */
	public static function single_button_block(): void {
		if ( ! function_exists( 'Just_Events\get_event_link' )
			|| ! function_exists( 'Just_Events\get_event_link_text' )
		) {
			return;
		}

		$link = \Just_Events\get_event_link();

		if ( ! $link ) {
			return;
		}

		$text = \Just_Events\get_event_link_text();

		?>
			<div class="single-button wpex-mb-40">
				<a href="<?php echo esc_url( $link ); ?>" class="theme-button"><?php echo esc_html( $text ); ?></a>
			</div>
		<?php
	}

	/**
	 * Page select choices.
	 */
	private static function page_select(): array {
		$choices = [
			\esc_html__( '— Select —', 'total' ),
		];
		foreach ( (array) get_pages() as $page ) {
			$choices[ $page->ID ] = $page->post_title;
		}
		return $choices;
	}

	/**
	 * Taxonomy select.
	 */
	private static function tax_select(): array {
		$choices = [
			'' => \esc_html__( '— Select —', 'total' ),
		];
		$taxonomies = \get_object_taxonomies( 'just_event' );
		if ( $taxonomies && ! \is_wp_error( $taxonomies ) ) {
			foreach ( $taxonomies as $taxonomy ) {
				$choices[ $taxonomy ] = $taxonomy;
			}
		}
		return $choices;
	}

	/**
	 * Return template choices.
	 */
	private static function select_template( string $type ): array {
		$choices = [
			'' => \esc_html__( '— Select —', 'total' ),
		];
		if ( $theme_builder = totaltheme_get_instance_of( 'Theme_Builder' ) ) {
			$templates = $theme_builder->get_template_choices( $type, false );
			if ( $templates ) {
				$choices = $choices + $templates;
			}
		}
		return $choices;
	}

}
