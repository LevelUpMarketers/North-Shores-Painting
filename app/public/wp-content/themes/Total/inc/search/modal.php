<?php

namespace TotalTheme\Search;

\defined( 'ABSPATH' ) || exit;

/**
 * Search Modal Class.
 */
final class Modal {

	/**
	 * Check if the scrips are loaded.
	 */
	private static $scripts_loaded = false;

	/**
	 * Class instance.
	 */
	private static $instance = null;

	/**
	 * Create or retrieve the instance of Error_404.
	 */
	public static function instance() {
		if ( null === static::$instance ) {
			static::$instance = new self();
		}
		return static::$instance;
	}

	/**
	 * Private Constructor.
	 */
	private function __construct() {
		\add_action( 'wp_footer', [ self::class, '_render_modal' ] );

		// Enqueue scripts - currently only used for the ajaxed modal
		if ( self::get_setting( 'use_ajax', true ) ) {
			if ( 'wp_enqueue_scripts' === \current_filter() ) {
				self::_load_scripts();
			} elseif ( ! \did_action( 'wp_enqueue_scripts' ) ) {
				\add_action( 'wp_enqueue_scripts', [ self::class, '_load_scripts' ] );
			}
		}
	}

	/**
	 * Enqueue scripts if needed.
	 */
	public static function _load_scripts(): void {
		if ( self::$scripts_loaded ) {
			return;
		}

		\wp_enqueue_script(
			'wpex-search-modal',
			\totaltheme_get_js_file( 'frontend/search/modal-ajax' ),
			[ \WPEX_THEME_JS_HANDLE ],
			\WPEX_THEME_VERSION,
			[
				'strategy' => 'defer',
			]
		);

		\wp_localize_script(
			'wpex-search-modal',
			'wpex_search_modal_params',
			\totaltheme_call_static( __NAMESPACE__ . '\Ajax', 'get_l10n' )
		);

		self::$scripts_loaded = true;
	}

	/**
	 * Get settings.
	 */
	protected static function get_settings(): array {
		return (array) \apply_filters( 'totaltheme/search/modal/settings', \get_theme_mod( 'search_modal', [] ) );
	}

	/**
	 * Get setting.
	 */
	protected static function get_setting( string $key, $default = null ) {
		return self::get_settings()[ $key ] ?? $default;
	}

	/**
	 * Render the modal HTML.
	 */
	public static function _render_modal(): void {
		if ( ! self::$scripts_loaded && self::get_setting( 'use_ajax', true ) ) {
			self::_load_scripts();
		}
		self::render_result_template();
		$placeholder = self::get_setting( 'input_placeholder' ) ?: \esc_html__( 'What are you looking for?', 'total' );
		$use_ajax    = self::get_setting( 'use_ajax', true );
		$inner_class = 'wpex-search-modal__inner wpex-modal__inner wpex-p-0 wpex-overflow-hidden';
		if ( $use_ajax ) {
			$inner_class .= ' wpex-flex wpex-flex-col wpex-gap-10';
		}
		?>
			<dialog id="wpex-search-modal" class="wpex-modal wpex-bg-transparent">
				<div class="<?php echo \esc_attr( $inner_class ); ?> ">
					<?php if ( $use_ajax ) { ?>
						<form class="wpex-search-modal__form wpex-relative" role="search" class="wpex-relative" autocomplete="off">
							<label for="wpex-search-modal-input" class="screen-reader-text"><?php echo wpex_get_aria_label( 'search' ); ?></label>
							<input id="wpex-search-modal-input" type="search" class="wpex-search-modal__input wpex-w-100 wpex-surface-1 wpex-text-2 wpex-unstyled-input wpex-outline-0 wpex-border-0 wpex-py-15 wpex-pr-15 wpex-rounded wpex-shadow" placeholder="<?php echo esc_attr( $placeholder ); ?>" required>
							<span class="wpex-search-modal__search-icon wpex-flex wpex-items-center wpex-pointer-events-none wpex-absolute wpex-inset-y-0 wpex-left-0 wpex-pl-15" aria-label="<?php echo wpex_get_aria_label( 'submit_search' ); ?>"><?php echo totaltheme_get_icon( 'search' ); ?></span>
							<span class="wpex-search-modal__loading wpex-flex wpex-items-center wpex-invisible wpex-absolute wpex-inset-y-0 wpex-left-0 wpex-pl-15"><?php echo \totaltheme_get_loading_icon( 'oval' ); ?></span>
							<button type="button" aria-label="<?php esc_attr( 'Clear search query'); ?>" class="wpex-search-modal__clear wpex-unstyled-button wpex-flex wpex-invisible wpex-opacity-0 wpex-items-center wpex-absolute wpex-text-3 wpex-dark-mode-text-2 wpex-hover-text-2 wpex-inset-y-0 wpex-right-0 wpex-pr-15" disabled><?php echo \totaltheme_get_icon( 'material-close-300', '', 'lg' ); ?></button>
						</form>
						<div class="wpex-search-modal__results wpex-hidden wpex-surface-1 wpex-text-2 wpex-rounded wpex-shadow wpex-overflow-auto wpex-last-mb-0"></div>
						<div class="wpex-search-modal__no-results wpex-hidden wpex-surface-1 wpex-text-2 wpex-rounded wpex-shadow wpex-last-mb-0 wpex-p-20"><?php echo ( $custom = get_theme_mod( 'modal_search_no_results_text' ) ) ? \esc_html( $custom ) : \esc_html( 'No results found...', 'total' ); ?></div>
					<?php } else {
						// Use standard search form for non-ajaxed search
						$form_args = [
							'placeholder'  => $placeholder,
							'style'        => 'modal',
							'form_class'   => 'wpex-flex wpex-gap-15 wpex-surface-1 wpex-text-2 wpex-p-30 wpex-shadow',
							'input_id'     => 'wpex-search-modal-input',
							'input_class'  => 'wpex-w-100',
							'submit_class' => 'theme-button',
							'submit_icon'  => false,

						];
						$form_args = \apply_filters( 'totaltheme/search/modal/form_args', $form_args );
						\get_search_form( $form_args );
					} ?>
				</div>
			</dialog>
		<?php
	}

	/**
	 * Search results template.
	 */
	private static function render_result_template(): void {
		$template = '<a href="{{permalink}}" class="wpex-search-modal-result wpex-text-current wpex-hover-text-current wpex-no-underline wpex-block wpex-py-20 wpex-px-30 wpex-border-0 wpex-border-b wpex-border-solid wpex-border-main wpex-text-1 wpex-transition-300"><div class="wpex-search-modal-result__type wpex-mb-10"><span class="wpex-inline-block wpex-surface-3 wpex-text-3 wpex-dark-mode-text-2 wpex-rounded-sm wpex-leading-none wpex-p-5 wpex-text-xs">{{tag}}</span></div><div class="wpex-search-modal-result__title wpex-heading wpex-mb-5">{{title}}</div><div class="wpex-search-modal-result__excerpt wpex-text-pretty wpex-last-mb-0">{{excerpt}}</div></a>';
		$template = (string) apply_filters( 'totaltheme/search/modal/result_template', $template );
		echo '<template id="wpex-search-modal-result-template">' . $template . '</template>';
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
