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

function fun_cat_lists() {
    ob_start();

    $current_category = get_queried_object();
	
    if (!isset($current_category->term_id)) {
        return '<p>This shortcode only works on category archive pages.</p>';
    }

    $args = [
        'cat' => $current_category->term_id,
        'posts_per_page' => -1,
        'post_status' => 'publish',
    ];

    $query = new WP_Query($args);

    if ($query->have_posts()) {
        $posts = $query->posts;
        $total_posts = count($posts);
        $index = 0;
        $block_toggle = true;
     
        echo '<div class="cat-posts-container">';

        while ($index < $total_posts) {

            echo '<div class="post-block flex flex-wrap md:flex-nowrap mb-8">';

            if ($block_toggle) {
                // Pattern A: Left Big, Right 4 Small
                if ($index < $total_posts) {
                    $big_post = $posts[$index++];
                    setup_postdata($big_post);
                    echo '<div class="w-60 md:w-1/2 pr-4 mb-4 md:mb-0">';
                    echo '<div class="big-post" style="position:relative">';

                    $categories = get_the_category($big_post->ID);
                    if (!empty($categories)) {
                        echo '<span class="featured-tag" style="position:absolute; bottom: 160px; left:20px; background:#fce96a; color:#000; padding:4px 10px; border-radius:5px; font-weight:bold;">' . esc_html($categories[0]->name) . '</span>';
                    }

                    if (has_post_thumbnail($big_post)) {
                        echo '<a href="' . get_permalink($big_post) . '">';
                        echo '<img class="123" src="' . esc_url(get_the_post_thumbnail_url($big_post, 'full')) . '" alt="' . esc_attr(get_the_title($big_post)) . '" />';
                        echo '</a>';
                    }
                    echo '<p style="margin-top:10px;">' . get_the_date('', $big_post) . ' | ' . esc_html(get_the_author($big_post)) . '</p>';
                    echo '<h2><a href="' . get_permalink($big_post) . '">' . get_the_title($big_post) . '</a></h2>';
                    echo '</div>';
                    echo '</div>';
                }

                echo '<div class="w-40 md:w-1/2 grid grid-cols-1 gap-4">';
                for ($i = 0; $i < 4 && $index < $total_posts; $i++, $index++) {
                    $small_post = $posts[$index];
                    setup_postdata($small_post);
                    echo '<div class="small-post flex">';
                    if (has_post_thumbnail($small_post)) {
                        echo '<img src="' . esc_url(get_the_post_thumbnail_url($small_post, 'full')) . '" alt="' . esc_attr(get_the_title($big_post)) . '" />';
                    }
                    echo '<div class="w-2/3">';
                    echo '<p class="text-xs">' . get_the_date('', $small_post) . ' | ' . esc_html(get_the_author($big_post)) . '</p>';
                    echo '<h4 class="text-sm font-semibold"><a href="' . get_permalink($small_post) . '">' . get_the_title($small_post) . '</a></h4>';
                    echo '</div>';
                    echo '</div>';
                }
                echo '</div>';

            } else {
                // Pattern B: Left 4 Small, Right Big
                echo '<div class="w-40 md:w-1/2 grid grid-cols-1 gap-4 pr-4">';
                for ($i = 0; $i < 4 && $index < $total_posts; $i++, $index++) {
                    $small_post = $posts[$index];
                    setup_postdata($small_post);
                    echo '<div class="small-post flex">';
                    if (has_post_thumbnail($small_post)) { 
                        echo '<img src="' . esc_url(get_the_post_thumbnail_url($small_post, 'full')) . '" alt="' . esc_attr(get_the_title($big_post)) . '" />';
                    }
                    echo '<div class="w-2/3">';
                    echo '<p class="text-xs">' . get_the_date('', $small_post) . ' | ' . esc_html(get_the_author($big_post)) . '</p>';
                    echo '<h4 class="text-sm font-semibold"><a href="' . get_permalink($small_post) . '">' . get_the_title($small_post) . '</a></h4>';
                    echo '</div>';
                    echo '</div>';
                }
                echo '</div>';

                if ($index < $total_posts) {
                    $big_post = $posts[$index++];
                    setup_postdata($big_post);
                    echo '<div class="w-60 md:w-1/2">';
                    echo '<div class="big-post" style="position:relative">';

                    $categories = get_the_category($big_post->ID);
                    if (!empty($categories)) {
                        echo '<span class="featured-tag" style="position:absolute; bottom: 160px; left:20px; background:#fce96a; color:#000; padding:4px 10px; border-radius:5px; font-weight:bold;">' . esc_html($categories[0]->name) . '</span>';
                    }
                    if (has_post_thumbnail($big_post)) {
                        echo '<img class="901" src="' . esc_url(get_the_post_thumbnail_url($big_post, 'full')) . '" alt="' . esc_attr(get_the_title($big_post)) . '" />';
                    }
                    echo '<p style="margin-top:10px;">' . get_the_date('', $big_post) . '| ' . esc_html(get_the_author($big_post)) . '</p>';
                    echo '<h2><a href="' . get_permalink($big_post) . '">' . get_the_title($big_post) . '</a></h2>';
                    echo '</div>';
                    echo '</div>';
                }
            }

            echo '</div>'; // Close block
            $block_toggle = !$block_toggle; // alternate pattern

            if ($index < $total_posts) {
                echo '<div style="border: 1px solid #C7C7C7;"></div>';
            }
        }

        echo '</div>'; // .cat-posts-container
        wp_reset_postdata();
    } else {
        echo '<p>No posts found in this category.</p>';
    }

    return ob_get_clean();
}
add_shortcode('cat_lists', 'fun_cat_lists');
