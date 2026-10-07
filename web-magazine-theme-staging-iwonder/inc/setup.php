<?php
/**
 * Setup & Configuration
 * Theme initialization, security headers, breadcrumbs, widgets, SVG support
 */

if (!defined('ABSPATH')) exit;

// Security Headers
function add_strict_security_headers() {
    header("X-Frame-Options: SAMEORIGIN");
    header("X-Content-Type-Options: nosniff");
    header("Strict-Transport-Security: max-age=31536000; includeSubDomains; preload");
    header("Content-Security-Policy: frame-ancestors 'self'; object-src 'none';");
    //header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self'; object-src 'none'; frame-ancestors 'self';");
header("Referrer-Policy: no-referrer-when-downgrade");
header("Permissions-Policy: camera=(), microphone=(), geolocation=()");
header("X-XSS-Protection: 1; mode=block"); // optional, mostly obsolete
}

add_action('send_headers', 'add_strict_security_headers', 1);

// Theme setup function
function web_magazine_theme_setup() {
	
    // Add support for dynamic title tag
    add_theme_support('title-tag');

    // Add support for post thumbnails (featured images)
    add_theme_support('post-thumbnails');

    // Custom image sizes
add_image_size('article-web', 1280, 720, true);         // Featured article
add_image_size('article-card', 480, 270, true);          // Card image used across list layouts
add_image_size('article-mobile', 768, 432, true);       // Sidebar card
add_image_size('card-square', 480, 480, true);          // Square cards
add_image_size('hero-desktop', 1920, 960, true);        // Hero banner
add_image_size('thumb-small', 150, 150, true);          // Small thumbnail
add_image_size('article-mobile-small', 360, 203, true);

    // Register navigation menus
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'web-magazine'),
        'footer'  => __('Footer Menu', 'web-magazine'),
    ));

    // Add support for HTML5 elements
    add_theme_support('html5', array('search-form', 'comment-form', 'comment-list', 'gallery', 'caption'));

    // Add support for custom logo
    // add_theme_support('custom-logo', array(
    //     'height'      => 85,
    //     'width'       => 576,
    //     'flex-height' => true,
    //     'flex-width'  => true,
	// 	'header-text' => array( 'site-title', 'site-description' ),
    // ));

}
add_action('after_setup_theme', 'web_magazine_theme_setup');

// Register widget areas
function web_magazine_widgets_init() {
    register_sidebar(array(
        'name'          => __('Sidebar', 'web-magazine'),
        'id'            => 'sidebar-1',
        'description'   => __('Main Sidebar Area', 'web-magazine'),
        'before_widget' => '<div class="widget">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));
}
add_action('widgets_init', 'web_magazine_widgets_init');

// SVG Upload Support
function allow_svg_uploads($mimes) {
    // Restrict SVG uploads to administrators to reduce XSS risk
    if ( current_user_can( 'manage_options' ) ) {
        $mimes['svg'] = 'image/svg+xml';
    }
    return $mimes;
}
add_filter('upload_mimes', 'allow_svg_uploads');

function show_svg_in_media_library() {
    echo '<style>
        td.media-icon img[src$=".svg"], img[src$=".svg"].attachment-post-thumbnail {
            width: 100px;
            height: auto;
        }
    </style>';
}
add_action('admin_head', 'show_svg_in_media_library');

function custom_breadcrumb() {

    if (is_front_page()) return;

    echo '<nav class="breadcrumb" aria-label="' . esc_attr( t('iw_breadcrumb') ) . '">';

    // Home
    echo '<a href="' . esc_url(home_url()) . '">' . esc_html( t('iw_home') ) . '</a>';

    // =========================
    // OLD MAGAZINE PAGE
    // =========================
    if (is_page('old-magazine-issues')) {

        echo '<span class="mx-1">/</span>';
        echo '<a href="' . esc_url(get_post_type_archive_link('magazine_issue')) . '">' . esc_html( t('iw_magazine_issues') ) . '</a>';
    }

    // =========================
    // TAXONOMY (CATEGORY / TAG / CUSTOM)
    // =========================
    elseif (is_category() || is_tag() || is_tax()) {

        $term = get_queried_object();

        // =========================
        // RESOURCE CATEGORIES (MAIN FIX)
        // =========================
        if ($term->taxonomy === 'resource_categories') {

            // Resources archive
            echo '<span class="mx-1">/</span>';
            echo '<a href="' . esc_url(get_post_type_archive_link('resource')) . '">' . esc_html( t('iw_resources') ) . '</a>';

            // Parent terms
            if ($term->parent) {
                $parents = get_ancestors($term->term_id, $term->taxonomy);
                $parents = array_reverse($parents);

                foreach ($parents as $parent_id) {
                    $parent = get_term($parent_id, $term->taxonomy);

                    echo '<span class="mx-1">/</span>';
                    echo '<a href="' . esc_url(get_term_link($parent)) . '">' . esc_html($parent->name) . '</a>';
                }
            }

            // Current term
            // echo '<span class="mx-1">/</span>';
            // echo '<span>' . esc_html($term->name) . '</span>';
        }

        // =========================
        // RESOURCE TYPE
        // =========================
        elseif ($term->taxonomy === 'resource_type') {

            echo '<span class="mx-1">/</span>';
            echo '<a href="' . esc_url(get_post_type_archive_link('resource')) . '">' . esc_html( t('iw_resources') ) . '</a>';
        }

        // =========================
        // BLOG CATEGORY / TAG
        // =========================
        elseif ($term->taxonomy === 'category' || $term->taxonomy === 'post_tag') {

            echo '<span class="mx-1">/</span>';
            echo '<a href="' . esc_url(get_permalink(get_option('page_for_posts'))) . '">' . esc_html( t('iw_articles') ) . '</a>';
        }
    }

    // =========================
    // POST TYPE ARCHIVE
    // =========================
    elseif (is_post_type_archive()) {

        $post_type = get_post_type();

        if ($post_type === 'post') {

            echo '<span class="mx-1">/</span>';
            echo '<a href="' . esc_url(get_permalink(get_option('page_for_posts'))) . '">' . esc_html( t('iw_articles') ) . '</a>';
        }
    }

    // =========================
    // SINGLE POST / CPT
    // =========================
    elseif (is_singular()) {

        $post_type = get_post_type_object(get_post_type());

        // Authors
        if ($post_type->name === 'authors_bio') {

            echo '<span class="mx-1">/</span>';
            echo '<a href="' . esc_url(get_post_type_archive_link('authors_bio')) . '">' . esc_html( t('iw_authors') ) . '</a>';
        }

        // Blog posts
        elseif ($post_type->name === 'post') {

            echo '<span class="mx-1">/</span>';
            echo '<a href="' . esc_url(get_permalink(get_option('page_for_posts'))) . '">' . esc_html( t('iw_articles') ) . '</a>';
        }

        // Other CPT
        elseif ($post_type->has_archive) {

            echo '<span class="mx-1">/</span>';
            echo '<a href="' . esc_url(get_post_type_archive_link($post_type->name)) . '">' . esc_html($post_type->labels->name) . '</a>';
        }

        // Magazine category (no link)
        if ($post_type->name === 'magazine_issue') {

            $terms = get_the_terms(get_the_ID(), 'magazine_category');

            if (!empty($terms) && !is_wp_error($terms)) {

                echo '<span class="mx-1">/</span>';
                echo '<span>' . esc_html($terms[0]->name) . '</span>';
            }
        }

        // Parent pages
        $ancestors = get_post_ancestors(get_the_ID());

        if (!empty($ancestors)) {

            $ancestors = array_reverse($ancestors);

            foreach ($ancestors as $ancestor) {

                echo '<span class="mx-1">/</span>';
                echo '<a href="' . esc_url(get_permalink($ancestor)) . '">' . esc_html(get_the_title($ancestor)) . '</a>';
            }
        }
    }

    // =========================
    // AUTHOR
    // =========================
    elseif (is_author()) {

        echo '<span class="mx-1">/</span>';
        echo '<span>' . esc_html( t('iw_author') ) . ' ' . esc_html(get_the_author()) . '</span>';
    }

    // =========================
    // DATE ARCHIVES
    // =========================
    elseif (is_day()) {

        echo '<span class="mx-1">/</span>';
        echo '<a href="' . esc_url(get_year_link(get_the_time('Y'))) . '">' . esc_html(get_the_time('Y')) . '</a>';

        echo '<span class="mx-1">/</span>';
        echo '<a href="' . esc_url(get_month_link(get_the_time('Y'), get_the_time('m'))) . '">' . esc_html(get_the_time('F')) . '</a>';

        echo '<span class="mx-1">/</span>';
        echo '<span>' . esc_html(get_the_time('d')) . '</span>';
    }

    elseif (is_month()) {

        echo '<span class="mx-1">/</span>';
        echo '<a href="' . esc_url(get_year_link(get_the_time('Y'))) . '">' . esc_html(get_the_time('Y')) . '</a>';

        echo '<span class="mx-1">/</span>';
        echo '<span>' . esc_html(get_the_time('F')) . '</span>';
    }

    elseif (is_year()) {

        echo '<span class="mx-1">/</span>';
        echo '<span>' . esc_html(get_the_time('Y')) . '</span>';
    }

    // =========================
    // SEARCH
    // =========================
    elseif (is_search()) {

        echo '<span class="mx-1">/</span>';
        echo '<span>' . esc_html( t('iw_search_results_for') ) . ' "' . esc_html(get_search_query()) . '"</span>';
    }

    // =========================
    // 404
    // =========================
    elseif (is_404()) {

        echo '<span class="mx-1">/</span>';
        echo '<span>' . esc_html( t('iw_page_not_found') ) . '</span>';
    }

    echo '</nav>';
}
// Code for change default archive page title
add_filter('get_the_archive_title', 'custom_post_archive_title');
function custom_post_archive_title($title) {
	if (is_home()) {
		$title = esc_html( t('iw_articles') );
	}
	return $title;
}

// Remove all taxonomy prefixes from archive titles
add_filter('get_the_archive_title', 'remove_all_archive_title_prefixes');
function remove_all_archive_title_prefixes($title) {
    if (is_category() || is_tag() || is_tax()) {
        $title = single_term_title('', false);
    }
    return $title;
}

// Add page slug to body class
function add_page_slug_to_body_class($classes) {
    if (is_page()) {
        global $post;
        $classes[] =  $post->post_name; 
    }
    return $classes;
}
add_filter('body_class', 'add_page_slug_to_body_class');



