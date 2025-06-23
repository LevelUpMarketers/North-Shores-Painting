<?php

namespace TotalThemeCore\Meta;

defined( 'ABSPATH' ) || exit;

/**
 * Creates a gallery metabox for WordPress.
 *
 * Credits: http://wordpress.org/plugins/easy-image-gallery/
 */
final class Gallery_Metabox {

	/**
	 * Array of post types to add the gallery to.
	 */
	private $post_types = [];

	/*
	 * Check if scripts have been loaded.
	 */
	private $scripts_loaded = false;

	/**
	 * Our single Gallery_Metabox instance.
	 */
	private static $instance = null;

	/**
	 * Create or retrieve the instance of Gallery_Metabox.
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
		\add_action( 'admin_init', [ $this, 'admin_init' ] );
		\add_action( 'admin_enqueue_scripts', [ $this, 'maybe_enqueue_scripts' ] );
	}

	/**
	 * Admin Actions.
	 */
	public function admin_init() {
		$post_types = [
			'post',
			'page',
		];

		$this->post_types = (array) \apply_filters( 'wpex_gallery_metabox_post_types', $post_types );

		if ( ! $this->post_types ) {
			return;
		}

		foreach ( $this->post_types as $post_type ) {
			\add_action( "add_meta_boxes_{$post_type}", [ $this, 'add_meta' ], 20 );
			\add_action( "save_post_{$post_type}", [ $this, 'save_meta' ] );
		}
	}

	/**
	 * Maybe enqueue scripts.
	 */
	public function maybe_enqueue_scripts( $hook_suffix ) {
		if ( $this->post_types && in_array( $hook_suffix, [ 'post.php', 'post-new.php' ] ) ) {
			$screen = get_current_screen();
			if ( is_object( $screen ) && ! empty( $screen->post_type ) && in_array( $screen->post_type, $this->post_types ) ) {
				$this->load_scripts();
			}
		}
	}

	/**
	 * Adds the gallery metabox.
	 */
	public function add_meta( $post ) {
		\add_meta_box(
			'wpex-gallery-metabox-ttc',
			\esc_html__( 'Image Gallery', 'total-theme-core' ),
			[ $this, 'display_metabox' ],
			$post->post_type,
			'side',
			'default'
		);
	}

	/**
	 * Render the gallery metabox.
	 */
	public function display_metabox() {
		global $post;

		if ( ! $post || ! \is_a( $post, 'WP_Post' ) ) {
			return;
		}

		if ( ! $this->scripts_loaded ) {
			$this->load_scripts();
		}

		?>
		<div id="wpex_gallery_images_container">
			<ul class="wpex_gallery_images">
				<?php
				$image_gallery = \get_post_meta( $post->ID, '_easy_image_gallery', true );
				if ( $image_gallery && \is_string( $image_gallery ) ) {
					$attachments = \explode( ',', $image_gallery );
					$attachments = $attachments ? \array_filter( $attachments ) : [];
					if ( $attachments ) {
						foreach ( $attachments as $attachment_id ) {
							if ( \wp_attachment_is_image ( $attachment_id  ) ) {
								echo '<li class="image" data-attachment_id="' . \absint( $attachment_id ) . '"><div class="attachment-preview"><div class="thumbnail">
											' . \wp_get_attachment_image( $attachment_id, 'thumbnail' ) . '</div>
											<a href="#" class="wpex-gmb-remove">' . \esc_html__( 'Remove image', 'total-theme-core' ) . '</a>
										</div></li>';
							}
						}
					}
				} ?>
			</ul>
			<input type="hidden" id="wpex_image_gallery_field" name="wpex_image_gallery" value="<?php echo \esc_attr( $image_gallery ); ?>">
			<?php \wp_nonce_field( 'wpex_gallery_metabox_nonce', 'wpex_gallery_metabox_nonce' ); ?>
		</div>

		<p class="add_wpex_gallery_images hide-if-no-js">
			<a href="#" class="button-primary"><?php \esc_html_e( 'Add/Edit Images', 'total-theme-core' ); ?></a>
		</p>

		<p>
			<label for="easy_image_gallery_link_images">
				<input type="checkbox" id="easy_image_gallery_link_images" value="on" name="easy_image_gallery_link_images"<?php echo \checked( \get_post_meta( \get_the_ID(), '_easy_image_gallery_link_images', true ), 'on', false ); ?>> <?php \esc_html_e( 'Single post lightbox?', 'total-theme-core' )?>
			</label>
		</p>

	<?php
	}

	/**
	 * Render the gallery metabox.
	 */
	public function save_meta( $post_id ) {

		// Check nonce.
		if ( ! isset( $_POST[ 'wpex_gallery_metabox_nonce' ] )
			|| ! \wp_verify_nonce( \sanitize_text_field( \wp_unslash( $_POST['wpex_gallery_metabox_nonce'] ) ), 'wpex_gallery_metabox_nonce' )
		) {
			return;
		}

		// Check auto save.
		if ( \defined( '\DOING_AUTOSAVE' ) && \DOING_AUTOSAVE ) {
			return;
		}

		// Check the user's permissions.
		if ( isset( $_POST['post_type'] ) && 'page' === $_POST['post_type'] ) {
			if ( ! \current_user_can( 'edit_page', $post_id ) ) {
				return;
			}
		} elseif ( ! \current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Update post meta.
		if ( ! empty( $_POST[ 'wpex_image_gallery' ] ) ) {
			// Sanitize field value.
			$attachment_ids = \sanitize_text_field( \wp_unslash( $_POST['wpex_image_gallery'] ) );
			if ( $attachment_ids ) {
				// Turn into array.
				$attachment_ids = \explode( ',', $attachment_ids );
				// Remove empty values.
				$attachment_ids = \array_filter( $attachment_ids );
				// Make sure all selected items are images.
				$attachment_ids = \array_filter( $attachment_ids, 'wp_attachment_is_image' );
				// Turn back into string.
				$attachment_ids = \implode( ',', $attachment_ids );
				// Finally lets update the meta value.
				\update_post_meta( $post_id, '_easy_image_gallery', \sanitize_text_field( $attachment_ids ) );
			}
		}

		// Delete gallery, but make sure the gallery is actually enabled, we don't want to potentially delete items if the form isn't even on the page.
		elseif ( isset( $_POST[ 'wpex_image_gallery' ] ) ) {
			\delete_post_meta( $post_id, '_easy_image_gallery' );
		}

		if ( isset( $_POST[ 'easy_image_gallery_link_images' ] ) ) {
			\update_post_meta( $post_id, '_easy_image_gallery_link_images', \sanitize_text_field( $_POST[ 'easy_image_gallery_link_images' ] ) );
		} else {
			\update_post_meta( $post_id, '_easy_image_gallery_link_images', 'off' );
		}

		\do_action( 'wpex_save_gallery_metabox', $post_id );
	}

	/**
	 * Load needed scripts.
	 */
	public function load_scripts() {
		\wp_enqueue_style(
			'totalthemecore-admin-gallery-metabox',
			\totalthemecore_get_css_file( 'admin/gallery-metabox' ),
			false,
			TTC_VERSION
		);

		\wp_enqueue_script( 'jquery-ui-sortable' );

		\wp_enqueue_script(
			'totalthemecore-admin-gallery-metabox',
			\totalthemecore_get_js_file( 'admin/gallery-metabox' ),
			[ 'jquery', 'jquery-ui-sortable' ],
			'1.0',
			true
		);

		\wp_localize_script( 'totalthemecore-admin-gallery-metabox', 'wpexGalleryMetaboxL10n', [
			'title'  => \esc_html__( 'Add Images to Gallery', 'total-theme-core' ),
			'button' => \esc_html__( 'Add to gallery', 'total-theme-core' ),
			'remove' => \esc_html__( 'Remove image', 'total-theme-core' ),
		] );

		$this->scripts_loaded = true;
	}

}
