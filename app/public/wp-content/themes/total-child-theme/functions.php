<?php


/**
 * Load the parent style.css file.
 */
function preload_raleway_fonts() {
    // Preload the Raleway-Regular font
    echo '<link rel="preload" href="' . get_stylesheet_directory_uri() . '/fonts/Raleway-Regular.woff2" as="font" type="font/woff2" crossorigin="anonymous">' . "\n";
    echo '<link rel="preload" href="' . get_stylesheet_directory_uri() . '/fonts/Raleway-Regular.woff" as="font" type="font/woff" crossorigin="anonymous">' . "\n";
    
    // Preload the Raleway-Bold font
    echo '<link rel="preload" href="' . get_stylesheet_directory_uri() . '/fonts/Raleway-Bold.woff2" as="font" type="font/woff2" crossorigin="anonymous">' . "\n";
    echo '<link rel="preload" href="' . get_stylesheet_directory_uri() . '/fonts/Raleway-Bold.woff" as="font" type="font/woff" crossorigin="anonymous">' . "\n";
}
add_action('wp_head', 'preload_raleway_fonts');

function total_child_enqueue_parent_theme_style() {
    wp_enqueue_style(
        'parent-style',
        get_template_directory_uri() . '/style.css',
        [],
        wp_get_theme( 'Total' )->get( 'Version' )
    );
}
add_action( 'wp_enqueue_scripts', 'total_child_enqueue_parent_theme_style' );

/*
 * White list functions for use in Total Theme Core shortcodes.
 */
define( 'VCEX_CALLBACK_FUNCTION_WHITELIST', [] );



// Allow SVG uploads in WordPress
function enable_svg_uploads($mimes) {
    // Add SVG to the list of allowed mime types
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
}
add_filter('upload_mimes', 'enable_svg_uploads');

// Properly display SVGs in the media library
function fix_svg_media_library_display() {
    echo '<style>
        .attachment-266 .thumbnail img[src$=".svg"] {
            width: 100% !important;
            height: auto !important;
        }
    </style>';
}
add_action('admin_head', 'fix_svg_media_library_display');

// Securely sanitize SVG files before uploading
function sanitize_svg_uploads($file) {
    if ($file['type'] === 'image/svg+xml') {
        $svg = file_get_contents($file['tmp_name']);

        // Use a library to sanitize the SVG content
        if (function_exists('simplexml_load_string')) {
            libxml_use_internal_errors(true);
            $dom = new DOMDocument();
            $dom->loadXML($svg, LIBXML_NOENT | LIBXML_DTDLOAD | LIBXML_NOERROR);

            if (!$dom) {
                $file['error'] = 'Uploaded SVG is not valid XML.';
            } else {
                $sanitized_svg = $dom->saveXML();
                file_put_contents($file['tmp_name'], $sanitized_svg);
            }
        } else {
            $file['error'] = 'PHP XML support is required to upload SVGs.';
        }
    }
    return $file;
}
add_filter('wp_handle_upload_prefilter', 'sanitize_svg_uploads');

// Prevent direct access to SVG files
function restrict_direct_access_to_svg() {
    if (is_admin()) {
        return;
    }

    add_filter('template_redirect', function () {
        $extension = pathinfo(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), PATHINFO_EXTENSION);

        if ($extension === 'svg') {
            header('Content-Type: image/svg+xml');
            readfile($_SERVER['DOCUMENT_ROOT'] . $_SERVER['REQUEST_URI']);
            exit;
        }
    });
}
add_action('init', 'restrict_direct_access_to_svg');

function lum_google_analytics() {
echo '<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-1N840LC952"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag("js", new Date());

  gtag("config", "G-1N840LC952");
</script>';
}
add_action('wp_head', 'lum_google_analytics',99);

