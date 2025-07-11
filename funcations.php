<?php
/**
 * Astra Child Theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Astra Child
 * @since 1.0.0
 */

/**
 * Define Constants
 */
define( 'CHILD_THEME_ASTRA_CHILD_VERSION', '1.0.0' );

/**
 * Enqueue styles
 */
function child_enqueue_styles() {

	wp_enqueue_style( 'astra-child-theme-css', get_stylesheet_directory_uri() . '/style.css', array('astra-theme-css'), CHILD_THEME_ASTRA_CHILD_VERSION, 'all' );

    wp_enqueue_script('jquery');
    if (!is_single()) {
        wp_enqueue_script('privacy-components', 'https://whatif-assets-cdn.s3.amazonaws.com/static/c4rmedia/privacy_update/form_components_condensed.js', array(), null, true);
    }

}

add_action( 'wp_enqueue_scripts', 'child_enqueue_styles', 15 );

// Search Query Modification
function search_only_posts($query)
{
    if ($query->is_search && ! is_admin()) {
        $query->set('post_type', 'post');
    }
    return $query;
}
add_action('pre_get_posts', 'search_only_posts');

// SVG Support
function mw_mime_types($mimes)
{
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
}
add_filter('upload_mimes', 'mw_mime_types');

// External link open in new tab
function force_external_links_new_tab_script() {
    $site_host = parse_url(home_url(), PHP_URL_HOST);
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const currentHost = '<?php echo esc_js($site_host); ?>';
        const allLinks = document.querySelectorAll('a[href^="http"]');

        allLinks.forEach(link => {
            try {
                const linkHost = new URL(link.href).hostname;

                if (linkHost && linkHost !== currentHost) {
                    link.setAttribute('target', '_blank');
                    link.setAttribute('rel', 'noopener noreferrer');
                }
            } catch (e) {
                // Invalid URL or other parsing error, ignore
            }
        });
    });
    </script>
    <?php
}

