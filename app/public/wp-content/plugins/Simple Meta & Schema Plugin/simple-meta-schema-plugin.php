<?php
/*
Plugin Name: Simple Meta & Schema Plugin
Description: Adds a simple metabox for custom meta title, meta description, and schema markup (JSON‑LD) for posts and pages.
Version: 1.0
Author: Your Name
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Add metabox to post and page edit screens.
 */
function smsp_add_meta_box() {
	add_meta_box(
		'smsp_meta',
		'SEO Settings',
		'smsp_meta_box_callback',
		['post', 'page'],
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'smsp_add_meta_box' );

/**
 * Render the metabox fields.
 *
 * @param WP_Post $post The post object.
 */
function smsp_meta_box_callback( $post ) {
	// Add a nonce field for security.
	wp_nonce_field( 'smsp_save_meta', 'smsp_meta_nonce' );

	// Retrieve existing meta values if any.
	$meta_title       = get_post_meta( $post->ID, '_smsp_meta_title', true );
	$meta_description = get_post_meta( $post->ID, '_smsp_meta_description', true );
	$schema_markup    = get_post_meta( $post->ID, '_smsp_schema_markup', true );
	?>
	<p>
		<label for="smsp_meta_title">Meta Title</label><br>
		<input type="text" name="smsp_meta_title" id="smsp_meta_title" value="<?php echo esc_attr( $meta_title ); ?>" style="width:100%;" />
	</p>
	<p>
		<label for="smsp_meta_description">Meta Description</label><br>
		<textarea name="smsp_meta_description" id="smsp_meta_description" rows="4" style="width:100%;"><?php echo esc_textarea( $meta_description ); ?></textarea>
	</p>
	<p>
		<label for="smsp_schema_markup">Schema Markup (JSON‑LD)</label><br>
		<textarea name="smsp_schema_markup" id="smsp_schema_markup" rows="6" style="width:100%;"><?php echo esc_textarea( $schema_markup ); ?></textarea>
	</p>
	<?php
}

/**
 * Save the metabox data.
 *
 * @param int $post_id The post ID.
 */
function smsp_save_meta_box_data( $post_id ) {
	// Verify nonce.
	if ( ! isset( $_POST['smsp_meta_nonce'] ) || ! wp_verify_nonce( $_POST['smsp_meta_nonce'], 'smsp_save_meta' ) ) {
		return;
	}

	// Prevent autosave.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// Check permissions.
	if ( isset( $_POST['post_type'] ) && 'page' === $_POST['post_type'] ) {
		if ( ! current_user_can( 'edit_page', $post_id ) ) {
			return;
		}
	} else {
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
	}

	// Save Meta Title.
	if ( isset( $_POST['smsp_meta_title'] ) ) {
		update_post_meta( $post_id, '_smsp_meta_title', sanitize_text_field( $_POST['smsp_meta_title'] ) );
	}

	// Save Meta Description.
	if ( isset( $_POST['smsp_meta_description'] ) ) {
		update_post_meta( $post_id, '_smsp_meta_description', sanitize_text_field( $_POST['smsp_meta_description'] ) );
	}

	// Save Schema Markup.
	if ( isset( $_POST['smsp_schema_markup'] ) ) {
		// Since schema markup is typically JSON, we allow it to be saved as raw input.
		update_post_meta( $post_id, '_smsp_schema_markup', wp_unslash( $_POST['smsp_schema_markup'] ) );
	}
}
add_action( 'save_post', 'smsp_save_meta_box_data' );

/**
 * Override the document title if a custom meta title is provided.
 *
 * @param array $title_parts The title parts.
 * @return array
 */
function smsp_modify_document_title( $title_parts ) {
	if ( is_singular() ) {
		global $post;
		if ( $post ) {
			$meta_title = get_post_meta( $post->ID, '_smsp_meta_title', true );
			if ( $meta_title ) {
				$title_parts['title'] = $meta_title;
			}
		}
	}

	return $title_parts;
}
add_filter( 'document_title_parts', 'smsp_modify_document_title' );

/**
 * Output meta description and schema markup in the head.
 */
function smsp_add_meta_tags() {
	if ( is_singular() ) {
		global $post;
		if ( $post ) {
			// Output meta description.
			$meta_description = get_post_meta( $post->ID, '_smsp_meta_description', true );
			if ( $meta_description ) {
				echo '<meta name="description" content="' . esc_attr( $meta_description ) . '">' . "\n";
			}
			// Output schema markup.
			$schema_markup = get_post_meta( $post->ID, '_smsp_schema_markup', true );
			if ( $schema_markup ) {
				echo '<script type="application/ld+json">' . "\n";
				echo $schema_markup . "\n";
				echo '</script>' . "\n";
			}
		}
	}
}
add_action( 'wp_head', 'smsp_add_meta_tags' );
