<?php
/**
 * Custom Post Types & Taxonomies
 * Resources, Magazine Issues, Authors Bio, Announcements, Editorial Team, From The Editor
 */

if (!defined('ABSPATH')) exit;

// ===== RESOURCES POST TYPE =====
function create_resources_cpt() {
	
	// Register slug for translation
    //do_action('wpml_register_single_string', 'Custom Post Type', 'Resource Slug', 'resources');
    //$resources_slug = apply_filters('wpml_translate_single_string', 'resources', 'Custom Post Type', 'Resource Slug');

    $labels = array(
        'name'               => _x('Resources', 'post type general name'),
        'singular_name'      => _x('Resource', 'post type singular name'),
        'menu_name'          => __('Resources'),
        'name_admin_bar'     => __('Resource'),
        'add_new'            => __('Add New'),
        'add_new_item'       => __('Add New Resource'),
        'new_item'           => __('New Resource'),
        'edit_item'          => __('Edit Resource'),
        'view_item'          => __('View Resource'),
        'all_items'          => __('All Resources'),
        'search_items'       => __('Search Resources'),
        'parent_item_colon'  => __('Parent Resources:'),
        'not_found'          => __('No resources found.'),
        'not_found_in_trash' => __('No resources found in Trash.')
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'exclude_from_search'=> false,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        //'rewrite'            => array('slug' => $resources_slug),
        'rewrite' => array('slug' => 'resources','with_front' => false),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => 5,
        'menu_icon'          => 'dashicons-portfolio',
        'supports'           => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
        'show_in_rest'       => true,
        'taxonomies'         => array('resource_type', 'resource_categories', 'post_tag'),
    );

    register_post_type('resource', $args);
}

function create_resource_type_taxonomy() {
    register_taxonomy('resource_type', 'resource', array(
        'label'         => __('Resource Type'),
        'hierarchical'  => true,
        'show_ui'       => true,
        'show_in_rest'  => true,
        'rewrite'       => array('slug' => 'resource-type'),
    ));
}

function create_resource_taxonomies() {
    register_taxonomy('resource_categories', 'resource', array(
        'label'         => __('Resource Categories'),
        'hierarchical'  => true,
        'show_ui'       => true,
        'show_in_rest'  => true,
        'rewrite'       => array('slug' => 'resource-categories'),
    ));
}

function add_tags_to_resources() {
    register_taxonomy_for_object_type('post_tag', 'resource');
}
add_action('init', 'create_resources_cpt');
add_action('init', 'create_resource_type_taxonomy');
add_action('init', 'create_resource_taxonomies');
add_action('init', 'add_tags_to_resources');

// Resources URL restriction
// add_action('template_redirect', function () {

//     // Check if this is a resource CPT single page
//     if (is_singular('resource')) {

//         $post = get_queried_object();

//         // Allow preview ONLY if:
//         // 1. preview=true
//         // 2. token exists
//         // 3. post is still 'pending'
//         if (
//             isset($_GET['preview']) && $_GET['preview'] === 'true' &&
//             isset($_GET['token']) &&
//             $post->post_status === 'pending'
//         ) {
//             return; // ALLOW preview
//         }

//         // Otherwise → Force 404
//         global $wp_query;
//         $wp_query->set_404();
//         status_header(404);
//         nocache_headers();
//         include(get_query_template('404'));
//         exit;
//     }
// });

add_action('template_redirect', function () {
    if (is_singular('resource')) {
        $post = get_queried_object();
        // ✅ Allow published posts (ALL languages)
        if ($post && $post->post_status === 'publish') {
            return;
        }
        // ✅ Allow preview for pending
        if (
            isset($_GET['preview']) && $_GET['preview'] === 'true' &&
            isset($_GET['token']) &&
            $post->post_status === 'pending'
        ) {
            return;
        }
        // ❌ Block everything else
        global $wp_query;
        $wp_query->set_404();
        status_header(404);
        nocache_headers();
        include(get_query_template('404'));
        exit;
    }
});


// ===== MAGAZINE ISSUE POST TYPE =====
function create_magazine_issues_cpt() {
	
	// Register translatable slugs
	//do_action('wpml_register_single_string', 'Custom Post Type', 'Magazine Issue Slug', 'magazine-issues');
    //$magazine_issue_slug = apply_filters('wpml_translate_single_string', 'magazine-issues', 'Custom Post Type', 'Magazine Issue Slug');

    register_post_type('magazine_issue', array(
        'labels' => array(
            'name' => __('Magazine Issues'),
            'singular_name' => __('Magazine Issue')
        ),
        'public' => true,
        'has_archive' => true,
        //'rewrite' => array('slug' => $magazine_issue_slug),
        'rewrite' => array('slug' => 'magazine-issues','with_front' => false),
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'tags'),
        'taxonomies' => array('post_tag'), // ✅ Enables tag support
        'show_in_rest' => true,
        'menu_position' => 5,
        'menu_icon' => 'dashicons-media-document'
    ));
}
add_action('init', 'create_magazine_issues_cpt');

// Register Taxonomy: Magazine Category
function create_magazine_taxonomy() {
    register_taxonomy('magazine_category', array('magazine_issue', 'post', 'resource'), array(
        'labels' => array(
            'name' => __('Magazine Categories'),
            'singular_name' => __('Magazine Category')
        ),
        'hierarchical' => true,
        'show_ui' => true,
        'show_in_rest' => true,
        'show_admin_column' => true,
        'rewrite' => array('slug' => 'magazine-category'),
    ));
}
add_action('init', 'create_magazine_taxonomy');

// ✅ Register default post_tag taxonomy for magazine_issue
function add_tags_to_magazine_issues() {
    register_taxonomy_for_object_type('post_tag', 'magazine_issue');
}
add_action('init', 'add_tags_to_magazine_issues');

// Display Taxonomy: Magazine Category
function display_magazine_categories($post_id = null) {
	
    if (!$post_id) {
        $post_id = get_the_ID();
    }

    $terms = get_the_terms($post_id, 'magazine_category');

    if (!empty($terms) && !is_wp_error($terms)) {
        foreach ($terms as $term) {
            // Get translated term
			
            $translated_term_id = apply_filters('wpml_object_id', $term->term_id, 'magazine_category', true, apply_filters('wpml_current_language', null));
            $translated_term = get_term($translated_term_id, 'magazine_category');
	
            // Find a magazine_issue post under this category
            $query = new WP_Query([
                'post_type' => 'magazine_issue',
                'tax_query' => [[
                    'taxonomy' => 'magazine_category',
                    'field'    => 'term_taxonomy_id',
                    'terms'    => array($translated_term_id),
                ]],
                'posts_per_page' => 1,
            ]);
           
            if ($query->have_posts()) {
                $query->the_post();
                echo '<div class="ms-2">';
                echo '<a href="' . esc_url(get_permalink()) . '" class="post-magzine-issue">' . esc_html($translated_term->name) . '</a>';
                echo '</div>';
                wp_reset_postdata();
            }
        }
    }
}

// ===== AUTHORS BIO POST TYPE =====
function create_authors_bio_cpt() {
	
	// Register translatable slugs
	//do_action('wpml_register_single_string', 'Custom Post Type', 'Authors Bio Slug', 'authors_bio');
	//$authors_bio_slug = apply_filters('wpml_translate_single_string', 'authors_bio', 'Custom Post Type', 'Authors Bio Slug');

	
    $labels = array(
        'name'               => _x('Authors Bio', 'post type general name'),
        'singular_name'      => _x('Author Bio', 'post type singular name'),
        'menu_name'          => __('Authors Bio'),
        'name_admin_bar'     => __('Author Bio'),
        'add_new'            => __('Add New'),
        'add_new_item'       => __('Add New Author Bio'),
        'new_item'           => __('New Author Bio'),
        'edit_item'          => __('Edit Author Bio'),
        'view_item'          => __('View Author Bio'),
        'all_items'          => __('All Authors Bio'),
        'search_items'       => __('Search Authors Bio'),
        'not_found'          => __('No bios found.'),
        'not_found_in_trash' => __('No bios found in Trash.')
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'has_archive'        => true,
        'menu_position'      => 9,
        'menu_icon'          => 'dashicons-id', // Bio icon
        'supports'           => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
        'show_in_rest'       => true,
		//'rewrite'            => array('slug' => $authors_bio_slug),
        'rewrite' => array('slug' => 'authors-bio','with_front' => false),
    );

    register_post_type('authors_bio', $args);
}
add_action('init', 'create_authors_bio_cpt');

function sort_authors_bio_alphabetically($query) {
    if (!is_admin() && $query->is_main_query() && is_post_type_archive('authors_bio')) {
        $query->set('orderby', 'title');
        $query->set('order', 'ASC');
        $query->set('posts_per_page', -1); // Show all authors
    }
}
add_action('pre_get_posts', 'sort_authors_bio_alphabetically');

// ===== ANNOUNCEMENTS POST TYPE =====
function create_announcements_cpt() {
    $labels = array(
        'name'               => _x('Announcements', 'post type general name'),
        'singular_name'      => _x('Announcement', 'post type singular name'),
        'menu_name'          => __('Announcements'),
        'name_admin_bar'     => __('Announcement'),
        'add_new'            => __('Add New'),
        'add_new_item'       => __('Add New Announcement'),
        'new_item'           => __('New Announcement'),
        'edit_item'          => __('Edit Announcement'),
        'view_item'          => __('View Announcement'),
        'all_items'          => __('All Announcements'),
        'search_items'       => __('Search Announcements'),
        'parent_item_colon'  => __('Parent Announcements:'),
        'not_found'          => __('No announcements found.'),
        'not_found_in_trash' => __('No announcements found in Trash.')
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'exclude_from_search'=> false,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => false, // Disable archives
        'capability_type'    => 'post',
        'has_archive'        => false, // Ensures no archive page
        'hierarchical'       => false,
        'menu_position'      => 7,
		'menu_icon'          => 'dashicons-megaphone',
        'supports'           => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
        'show_in_rest'       => true, // Enable for Gutenberg editor
    );

    register_post_type('announcement', $args);
}
add_action('init', 'create_announcements_cpt');

// ===== EDITORIAL TEAM POST TYPE =====
function register_editorial_team_cpt() {
    $labels = array(
        'name'               => 'Editorial Team',
        'singular_name'      => 'Editorial Member',
        'menu_name'          => 'Editorial Team',
        'name_admin_bar'     => 'Editorial Member',
        'add_new'            => 'Add New',
        'add_new_item'       => 'Add New Editorial Member',
        'new_item'           => 'New Editorial Member',
        'edit_item'          => 'Edit Editorial Member',
        'view_item'          => 'View Editorial Member',
        'all_items'          => 'All Editorial Members',
        'search_items'       => 'Search Editorial Members',
        'not_found'          => 'No members found.',
        'not_found_in_trash' => 'No members found in Trash.'
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'has_archive'        => false, // No archive page
        'rewrite'            => array('slug' => 'editorial-member'),
        'supports'           => array('title', 'editor', 'thumbnail', 'custom-fields'),
        'show_in_rest'       => true, // Enables Gutenberg
		'menu_position'      => 10,
        'menu_icon'          => 'dashicons-groups',
        'publicly_queryable' => true,
        'exclude_from_search'=> false,
        'show_in_nav_menus'  => true,
        'capability_type'    => 'post',
    );

    register_post_type('editorial_team', $args);
}
add_action('init', 'register_editorial_team_cpt');

// ===== FROM THE EDITOR POST TYPE =====
function register_from_the_editor_cpt() {
    $labels = array(
        'name'               => 'From The Editor',
        'singular_name'      => 'From The Editor',
        'menu_name'          => 'From The Editor',
        'name_admin_bar'     => 'From The Editor',
        'add_new'            => 'Add New',
        'add_new_item'       => 'Add New Entry',
        'new_item'           => 'New Entry',
        'edit_item'          => 'Edit Entry',
        'view_item'          => 'View Entry',
        'all_items'          => 'All Entries',
        'search_items'       => 'Search Entries',
        'not_found'          => 'No entries found.',
        'not_found_in_trash' => 'No entries found in Trash.'
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'exclude_from_search'=> false,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'from-the-editor'),
        'capability_type'    => 'post',
        'has_archive'        => false,
        'hierarchical'       => false,
        'menu_position'      => 11,
        'menu_icon'          => 'dashicons-edit',
        'supports'           => array('title', 'editor', 'thumbnail', 'excerpt')
    );

    register_post_type('from_the_editor', $args);
}
add_action('init', 'register_from_the_editor_cpt');

// ===== KEYWORD/TAG MANAGEMENT =====

/**
 * Change 'Tags' to 'Keywords' with case-insensitive deduplication
 */

// 1. Change all tag labels to "Keywords"
function change_tags_labels_to_keywords() {
    global $wp_taxonomies;
    
    if (!isset($wp_taxonomies['post_tag'])) {
        return;
    }

    $labels = &$wp_taxonomies['post_tag']->labels;
    
    $labels->name = _x('Keywords', 'taxonomy general name');
    $labels->singular_name = _x('Keyword', 'taxonomy singular name');
    $labels->search_items = __('Search Keywords');
    $labels->popular_items = __('Popular Keywords');
    $labels->all_items = __('All Keywords');
    $labels->edit_item = __('Edit Keyword');
    $labels->view_item = __('View Keyword');
    $labels->update_item = __('Update Keyword');
    $labels->add_new_item = __('Add New Keyword');
    $labels->new_item_name = __('New Keyword Name');
    $labels->separate_items_with_commas = __('Separate keywords with commas');
    $labels->add_or_remove_items = __('Add or remove keywords');
    $labels->choose_from_most_used = __('Choose from the most used keywords');
    $labels->not_found = __('No keywords found');
    $labels->no_terms = __('No keywords');
    $labels->menu_name = __('Keywords');
    $labels->name_admin_bar = _x('Keyword', 'add new on admin bar');
    
    $wp_taxonomies['post_tag']->label = __('Keywords');
}
add_action('init', 'change_tags_labels_to_keywords', 1);

// 2. Prevent duplicate keywords with different cases
function prevent_case_sensitive_keyword_duplicates($term, $taxonomy) {
    if ($taxonomy !== 'post_tag') {
        return $term;
    }

    // Check if any case variation exists
    $existing = get_terms([
        'taxonomy' => 'post_tag',
        'name__like' => $term,
        'hide_empty' => false,
        'fields' => 'ids'
    ]);

    if (!empty($existing) && !is_wp_error($existing)) {
        $first_term = get_term($existing[0]);
        if (!is_wp_error($first_term)) {
            return new WP_Error('term_exists', __('Keyword already exists'), $first_term->term_id);
        }
    }

    return $term;
}
add_filter('pre_insert_term', 'prevent_case_sensitive_keyword_duplicates', 10, 2);

// 3. Normalize keyword display (optional)
function normalize_keyword_display($term) {
    if (is_admin() || !is_object($term) || !isset($term->taxonomy) || $term->taxonomy !== 'post_tag') {
        return $term;
    }
    
    $term->name = mb_strtolower($term->name);
    return $term;
}
add_filter('get_term', 'normalize_keyword_display');
add_filter('get_terms', function($terms) {
    if (!is_admin() && is_array($terms)) {
        foreach ($terms as &$term) {
            if (is_object($term) && isset($term->taxonomy) && $term->taxonomy === 'post_tag') {
                $term->name = mb_strtolower($term->name);
            }
        }
    }
    return $terms;
});
