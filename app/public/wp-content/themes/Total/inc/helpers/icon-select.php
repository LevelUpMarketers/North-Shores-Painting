<?php

namespace TotalTheme\Helpers;

\defined( 'ABSPATH' ) || exit;

/**
 * Icon Select Field.
 */
class Icon_Select {

	/**
	 * Check if scripts have been loaded so they aren't localized multiple times.
	 */
	private static $scripts_enqueued = false;

	/**
	 * Check if modal has been appended already.
	 */
	private static $modal_added = false;

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Enqueues icon select fields.
	 */
	public static function enqueue_scripts( $force = false ): void {
		if ( self::$scripts_enqueued && ! $force ) {
			return;
		}

		wp_enqueue_media();

		\wp_enqueue_style(
			'totaltheme-module-icon-select',
			\totaltheme_get_css_file( 'module/icon-select' ),
			[ 'wp-components' ],
			WPEX_THEME_VERSION
		);

		\wp_enqueue_script(
			'totaltheme-module-icon-select',
			\totaltheme_get_js_file( 'module/icon-select' ),
			[ 'wp-components' ],
			WPEX_THEME_VERSION,
			[
				'strategy'  => 'defer',
			]
		);

		\wp_localize_script(
			'totaltheme-module-icon-select',
			'totaltheme_module_icon_select_vars',
			[
				'nonce'               => \wp_create_nonce( 'totaltheme_icon_select' ),
				'json_url'            => \totaltheme_call_static( 'Theme_Icons', 'get_json_url' ),
				'wrong_type_error'    => \esc_html__( 'Incorrect file type selected', 'total' ),
				'svg_uploads_notice'  => \esc_html__( 'SVG uploads are not allowed by default in WordPress. We recommend you install the "SVG Support" or "Safe SVG" plugin so you can upload custom SVG\'s to your site.', 'total' ),
				'svg_uploads_allowed' => \in_array( 'image/svg+xml', (array) \get_allowed_mime_types(), true ),
			]
		);

		self::$scripts_enqueued = true;
	}

	/**
	 * Helper function renders the add template form.
	 */
	public static function render_form( array $args, bool $insert_modal = true ): void {
		if ( ! self::$modal_added && $insert_modal ) {
			self::enqueue_scripts();
			if ( \is_admin() ) {
				\add_action( 'admin_footer', [ self::class, 'render_modal' ] );
			} else {
				\add_action( 'wp_footer', [ self::class, 'render_modal' ] );
			}
		}

		$args = \wp_parse_args( $args, [
			'choices'     => [],
			'selected'    => '',
			'input_name'  => '',
			'input_type'  => 'hidden',
			'input_id'    => '',
			'input_class' => '',
		]);

		$selected      = $args['selected'] ? \str_replace( 'ticon ticon-', '', $args['selected'] ) : '';
		$choices       = $args['choices'] ? \wp_json_encode( (array) $args['choices'] ) : '';
		$selected_html = $selected ? \totaltheme_get_icon( $selected ) : '';
		?>

		<div class="totaltheme-icon-select hidden" data-totaltheme-choices="<?php echo \esc_attr( $choices ); ?>">
			<div class="totaltheme-icon-select__form">
				<div class="totaltheme-icon-select__preview-wrap">
					<div class="totaltheme-icon-select__preview<?php echo $selected_html ? '' : ' totaltheme-icon-select__preview--empty'; ?>">
						<div class="totaltheme-icon-select__svg"><?php echo $selected_html; // @codingStandardsIgnoreLine ?></div>
						<div class="totaltheme-icon-select__preview-loading" hidden><?php echo \totaltheme_get_svg( 'spinner' ); ?></div>
						<a href="#" class="totaltheme-icon-select__remove" role="button"><?php echo \totaltheme_get_svg( 'xmark' ); ?><span class="screen-reader-text"><?php \esc_html_e( 'remove selected icon', 'total' ); ?></span></a>
					</div>
				</div>
				<input name="<?php echo \esc_attr( $args['input_name'] ); ?>" class="totaltheme-icon-select__input<?php echo $args['input_class'] ? ' ' . \esc_attr( $args['input_class'] ) : ''; ?>" type="<?php echo \esc_attr( $args['input_type'] ); ?>" value="<?php echo \esc_attr( $selected ); ?>">
				<div class="totaltheme-icon-select__actions">
					<button<?php echo ! empty( $args['input_id'] ) ? ' id="' . esc_attr( $args['input_id'] ) . '"' : ''; ?> type="button" class="totaltheme-icon-select__button totaltheme-icon-select__actions-library button button-primary"><?php \esc_html_e( 'Icon Library', 'total' ); ?></button>
					<button type="button" class="totaltheme-icon-select__button totaltheme-icon-select__actions-media button button-secondary"><?php \esc_html_e( 'Custom', 'total' ); ?></button>
				</div>
			</div>
		</div>

		<?php
	}

	/**
	 * Renders the modal element.
	 */
	public static function render_modal(): void {
		if ( self::$modal_added ) {
			return; // Incase this function is called directly.
		}
		?>
		<div class="totaltheme-icon-select-modal components-modal__screen-overlay" style="display: none";>
			<div class="totaltheme-icon-select-modal__frame components-modal__frame is-full-screen" tabindex="-1">
				<div class="totaltheme-icon-select-modal__content components-modal__content">
					<div class="totaltheme-icon-select-modal__header components-modal__header">
						<div class="components-search-control__input-wrapper">
							<input class="totaltheme-icon-select-modal__search components-search-control__input" type="search" placeholder="<?php \esc_html_e( 'Search for an icon', 'total' ); ?>"><div class="components-search-control__icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" aria-hidden="true" focusable="false"><path d="M13.5 6C10.5 6 8 8.5 8 11.5c0 1.1.3 2.1.9 3l-3.4 3 1 1.1 3.4-2.9c1 .9 2.2 1.4 3.6 1.4 3 0 5.5-2.5 5.5-5.5C19 8.5 16.5 6 13.5 6zm0 9.5c-2.2 0-4-1.8-4-4s1.8-4 4-4 4 1.8 4 4-1.8 4-4 4z"></path></svg></div>
						</div>
						<button class="totaltheme-icon-select-modal__close components-button has-icon" aria-label="<?php \esc_html_e( 'Close dialog', 'total' ); ?>"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" aria-hidden="true" focusable="false"><path d="M13 11.8l6.1-6.3-1-1-6.1 6.2-6.1-6.2-1 1 6.1 6.3-6.5 6.7 1 1 6.5-6.6 6.5 6.6 1-1z"></path></svg></button>
					</div>
					<div class="totaltheme-icon-select-modal__choices"></div>
					<div class="totaltheme-icon-select-modal__loader"><?php echo \totaltheme_get_icon( 'spinner', 'totaltheme-icon-select-modal__loader-icon' ); ?></div>
				</div>
			</div>
		</div>
		<?php
		self::$modal_added = true;
	}

}
