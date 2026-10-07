<?php
/**
 * Helper Functions & Utilities
 * Business logic, translations, navigation, validation, redirects
 */

if (!defined('ABSPATH')) exit;

// ===== TRANSLATION FUNCTIONS =====

// Load strings from the theme's strings.php file
function theme_get_strings() {
    static $strings = null;
    if ($strings === null) {
        $file = get_template_directory() . '/iwstrings.php';
        if (file_exists($file)) {
            $strings = include $file;
        } else {
            $strings = [];
        }
    }
    return $strings;
}

// Translate a string key using WPML
function t($key) {
    $strings = theme_get_strings();
    $text = $strings[$key] ?? $key;
    return apply_filters('wpml_translate_single_string', $text, 'iwstrings', $key);
}

// Register all strings with WPML
add_action('init', function () {
    $strings = theme_get_strings();
    foreach ($strings as $key => $value) {
        do_action('wpml_register_single_string', 'iwstrings', $key, $value);
    }
});

// ===== ARTICLE & AUTHOR FUNCTIONS =====

// Reusable helper function
function get_article_authors_html($post_id) {
    $output = '';
    if (function_exists('get_field') && $post_id) {
        $author_post_ids = get_field('article_author', $post_id);
        if (!empty($author_post_ids) && is_array($author_post_ids)) {
            foreach ($author_post_ids as $aid) {
                if (get_post_status($aid)) {
                    $output .= '<a href="' . esc_url(get_permalink($aid)) . '">' . esc_html(get_the_title($aid)) . '</a>, ';
                }
            }
            $output = rtrim($output, ', ');
        }
    }
    return $output;
}



/**
 * Display dynamic linked authors
 * - Uses 'article_author' for standard posts
 * - Uses 'resources_author' for 'resource' post type
 */
function display_dynamic_linked_authors($post_id) {
    // Get post type
    $post_type = get_post_type($post_id);
    
    // Determine which field to use based on post type
    $field_name = ($post_type === 'resource') ? 'resources_author' : 'article_author';
    
    // Get author IDs from the appropriate field
    $author_post_ids = get_field($field_name, $post_id);
    $author_links = [];

    if ($author_post_ids) {
        foreach ((array)$author_post_ids as $author_post_id) {
            if (is_object($author_post_id)) {
                $author_links[] = sprintf(
                    '<a href="%s">%s</a>',
                    esc_url(get_permalink($author_post_id->ID)),
                    esc_html($author_post_id->post_title)
                );
            } elseif (is_numeric($author_post_id)) {
                $author_links[] = sprintf(
                    '<a href="%s">%s</a>',
                    esc_url(get_permalink($author_post_id)),
                    esc_html(get_the_title($author_post_id))
                );
            }
        }
    }

    return implode(', ', $author_links);
}

// ===== CATEGORY & STYLING FUNCTIONS =====

function get_category_class($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }

    $post_type = get_post_type($post_id);

    // Determine the taxonomy based on post type
    $taxonomy = ($post_type === 'resource') ? 'resource_type' : 'category';

    $terms = get_the_terms($post_id, $taxonomy);
    if (!empty($terms) && !is_wp_error($terms)) {
        foreach ($terms as $term) {
            $term_key = ($taxonomy === 'resource_type') ? "resource_type_{$term->term_id}" : "category_{$term->term_id}";
            $category_color = get_field("category_color_class", $term_key);
            if (!empty($category_color)) {
                return esc_attr($category_color);
            }
        }
    }

    return "brand-bg"; // Fallback class
}

function get_resource_category_class($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }

    $terms = get_the_terms($post_id, 'resource_type');
    if (!empty($terms) && !is_wp_error($terms)) {
        foreach ($terms as $term) {
            $category_color = get_field("category_color_class", "resource_type_" . $term->term_id);
            if (!empty($category_color)) {
                return esc_attr($category_color); // Always sanitize output
            }
        }
    }

    return "brand-bg"; // Fallback class
}

//Display category name
function get_styled_category_link() {
    global $post;

    // Get post type
    $post_type = get_post_type($post);

    // Define taxonomy based on post type
    switch ($post_type) {
        case 'resource':
            $taxonomy = 'resource_categories';
            break;
        case 'magazine_issue':
            $taxonomy = 'magazine_issue';
            break;
        default:
            $taxonomy = 'category'; // For regular posts
            break;
    }

   
    $terms = get_the_terms($post->ID, $taxonomy);

    if (!empty($terms) && !is_wp_error($terms)) {
        $term = $terms[0];
        $class = esc_attr(get_category_class());
        $name  = esc_html($term->name);
        $url   = esc_url(get_term_link($term));

        
        if ($post_type === 'resource') {
            return '<a href="javascript:void(0)" class="badge ' . $class . '"><span>' . $name . '</span></a>';
        }

        return '<a href="' . $url . '" class="badge ' . $class . '"><span>' . $name . '</span></a>';
    }

    return '';
}

// ===== NAVIGATION & MENU FUNCTIONS =====

// active class to nav menu
add_filter('nav_menu_css_class', 'add_current_menu_classes_to_singles_and_custom_search', 10, 2);
function add_current_menu_classes_to_singles_and_custom_search($classes, $item) {
    global $post;

    // Reuse already registered and translated slugs
    $resources_slug      = apply_filters('wpml_translate_single_string', 'resources', 'Custom Post Type', 'Resource Slug');
    $magazine_slug       = apply_filters('wpml_translate_single_string', 'magazine-issues', 'Custom Post Type', 'Magazine Issue Slug');
    $articles_slug       = apply_filters('wpml_translate_single_string', 'articles', 'Custom Post Type', 'Articles Slug'); // If you registered this

    // Get current URL path
    $current_path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

    // Check if we're on search result for a post
    if ((is_single() && get_post_type() === 'post') || $current_path === $articles_slug) {
        $blog_page_id = get_option('page_for_posts');
        if ($blog_page_id && isset($item->object_id) && $item->object_id == $blog_page_id) {
            $classes = add_current_classes($classes);
        }
    }

    // Check if we're on singular or search for 'resource'
    if (is_singular('resource') || strpos($current_path, $resources_slug) === 0) {
        $resource_archive = get_page_by_path($resources_slug);
        if ($resource_archive && isset($item->object_id) && $item->object_id == $resource_archive->ID) {
            $classes = add_current_classes($classes);
        } elseif (isset($item->url) && strpos($item->url, '/' . $resources_slug) !== false) {
            $classes = add_current_classes($classes);
        }
    }

    // Check if we're on singular or search for 'magazine_issue'
    if (is_singular('magazine_issue') || strpos($current_path, $magazine_slug) === 0) {
        $magazine_archive = get_page_by_path($magazine_slug);
        if ($magazine_archive && isset($item->object_id) && $item->object_id == $magazine_archive->ID) {
            $classes = add_current_classes($classes);
        } elseif (isset($item->url) && strpos($item->url, '/' . $magazine_slug) !== false) {
            $classes = add_current_classes($classes);
        }
    }

    if (is_page('old-magazine-issues')) {
        // Match the Magazine Issues menu item by its URL
        if (isset($item->url) && strpos($item->url, '/' . $magazine_slug) !== false) {
            $classes = add_current_classes($classes);
        }
    }
	
    return $classes;
}

function add_current_classes($classes) {
    $new_classes = [
        'current_page_parent',
        'current-menu-ancestor',
        'current-menu-parent',
        'current_page_ancestor'
    ];

    foreach ($new_classes as $class) {
        if (!in_array($class, $classes)) {
            $classes[] = $class;
        }
    }

    return $classes;
}

// header menu dropdown icon code
function add_menu_dropdown_arrow($items, $args) {
    if ($args->theme_location === 'primary') {
        $dropdown_icon_url = "/wp-content/uploads/2025/08/dropdownicon.png"; // Update path to your image
        $items = preg_replace('/(<li[^>]*class="[^"]*menu-item-has-children[^"]*"[^>]*>\s*<a[^>]+>)(.*?)(<\/a>)/', '$1$2<img src="' . $dropdown_icon_url . '" width="24" height="7" class="dropdown-arrow" alt="Dropdown Arrow">$3', $items);
    }
    return $items;
}
add_filter('wp_nav_menu_items', 'add_menu_dropdown_arrow', 10, 2);

function add_submenu_class($classes, $item, $args, $depth) {
    if ($depth >= 1 && in_array('menu-item-has-children', $classes)) {
        $classes[] = 'dropdown-submenu';
    }
    return $classes;
}
add_filter('nav_menu_css_class', 'add_submenu_class', 10, 4);



// ===== POST ORDER ENFORCEMENT & DISPLAY =====

//Post order home page with acf reset
add_action('acf/save_post', 'enforce_unique_post_order', 20);
function enforce_unique_post_order($post_id) {
    // Bail early on ACF field group save, autosave, or WP cron
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (defined('DOING_AJAX') && DOING_AJAX && isset($_POST['action']) && $_POST['action'] === 'acf/ajax/upgrade_plugin') return;
    if ($post_id === 'options' || strpos($post_id, 'acf-field-group') !== false) return;
    if (wp_is_post_revision($post_id) || wp_is_post_autosave($post_id)) return;

    // Only run on 'post' or 'resource' post types
    $post_type = get_post_type($post_id);
    if (!in_array($post_type, ['post', 'resource'])) {
        return;
    }

    // Get the current post_order value just saved
    $current_order = get_field('post_order', $post_id);
    if (!$current_order) return;

    // Find all other posts of the same type with this post_order
    $query = new WP_Query([
        'post_type'      => $post_type,
        'post_status'    => 'any',
        'posts_per_page' => -1,
        'post__not_in'   => [$post_id],
        'meta_query'     => [
            [
                'key'     => 'post_order',
                'value'   => $current_order,
                'compare' => '='
            ]
        ]
    ]);

    if ($query->have_posts()) {
        foreach ($query->posts as $conflicting_post) {
            // Clear the value from older/conflicting posts
            delete_post_meta($conflicting_post->ID, 'post_order');
        }
    }

    wp_reset_postdata();
}

// Show post order acf in All Posts list
add_filter('manage_post_posts_columns', 'add_post_order_column_after_categories');
function add_post_order_column_after_categories($columns) {
    $new_columns = [];

    foreach ($columns as $key => $value) {
        $new_columns[$key] = $value;

        // Insert 'post_order' column right after 'categories'
        if ($key === 'categories') {
            $new_columns['post_order'] = 'Post Order';
        }
    }

    return $new_columns;
}

add_action('manage_post_posts_custom_column', 'show_post_order_column_content', 10, 2);
function show_post_order_column_content($column, $post_id) {
    if ($column === 'post_order') {
        $post_order = get_field('post_order', $post_id);
        echo esc_html($post_order ?: '—');
    }
}

add_filter('manage_edit-post_sortable_columns', 'make_post_order_column_sortable');
function make_post_order_column_sortable($columns) {
    $columns['post_order'] = 'post_order';
    return $columns;
}

add_action('pre_get_posts', 'sort_by_post_order_column');
function sort_by_post_order_column($query) {
    if (!is_admin() || !$query->is_main_query()) {
        return;
    }

    if ($query->get('orderby') === 'post_order') {
        $query->set('meta_key', 'post_order');
        $query->set('orderby', 'meta_value'); // or 'meta_value_num' if numeric
    }
}

// ===== DISPLAY FUNCTIONS =====

//function to display the copyright
function display_dynamic_copyright() {
    $copyright_text = get_theme_mod('footer_copyright', '© ' . date('Y') . ' Azim Premji University');
    
    // If the text contains [year], replace it (backward compatibility)
    if (strpos($copyright_text, '[year]') !== false) {
        $copyright_text = str_replace('[year]', date('Y'), $copyright_text);
    }
    // Else if the year is missing, prepend it
    elseif (!preg_match('/©\s*\d{4}/', $copyright_text)) {
        $copyright_text = '© ' . date('Y') . ' ' . $copyright_text;
    }
    
    echo esc_html($copyright_text);
}



// readmore and read less resulabe function
function iw_trim_content($text, $limit = 34) {

    $read_more = esc_html( t('iw_read_more') );
    $read_less = esc_html( t('iw_read_less') );

    $text  = wp_strip_all_tags($text);
    $words = explode(' ', $text);

    if (count($words) <= $limit) {
        return '<p class="cat-wrap">'.$text.'</p>';
    }

    $short = implode(' ', array_slice($words, 0, $limit));
    $rest  = implode(' ', array_slice($words, $limit));

    return '
    <p class="cat-wrap">
        '.$short.'
        <span class="cat-dots">...</span>
        <span class="cat-more">'.$rest.'</span>
        <button 
            class="rm-btn" 
            data-more="'.$read_more.'" 
            data-less="'.$read_less.'" 
            onclick="catToggle(this)">
            '.$read_more.'
        </button>
    </p>';
}


// ===== PDF & SHORTCODE FUNCTIONS =====

// Function to return magazine category names
function pdfprnt_show_magazine_category() {
    $terms = get_the_terms(get_the_ID(), 'magazine_category');
    if ($terms && !is_wp_error($terms)) {
        $term_names = wp_list_pluck($terms, 'name');
        return implode(', ', $term_names);
    }
    return 'No Magazine Category';
}

// Register custom shortcode tag for BestWebSoft PDF plugin
function register_pdf_custom_tags($tags) {
    $tags['magazine_category'] = 'pdfprnt_show_magazine_category';
    return $tags;
}
add_filter('pdfprnt_custom_shortcodes', 'register_pdf_custom_tags');

// acf short note for pdf generation
function pdf_acf_short_note_shortcode( $atts ) {
	if ( is_singular() ) {
		$short_note = get_field( 'short_note' );
		if ( $short_note ) {
			return '<div class="acf-short-note">' . esc_html( $short_note ) . '</div>';
		}
	}
	return '';
}
add_shortcode( 'acf_short_note', 'pdf_acf_short_note_shortcode' );

// ===== SEARCH & RELEVANSSI FUNCTIONS =====

/*
add_filter('relevanssi_content_to_index', 'rlv_relationship_content', 10, 2);
function rlv_relationship_content($content, $post) {
	// Fetching the post data by the relationship field.
	$relationships = get_post_meta($post->ID, 'resources_author', true);
	if (!is_array($relationships)) {
		$relationships = array($relationships);
	}
	foreach ($relationships as $related_post) {
		$content .= ' ' . get_the_title($related_post);
	}
	return $content;
}
*/
add_filter('relevanssi_content_to_index', 'rlv_relationship_content', 10, 2);

function rlv_relationship_content($content, $post) {
    // Check post type and get appropriate relationship field
    if ($post->post_type == 'resource') {
        $relationships = get_post_meta($post->ID, 'resources_author', true);
    } elseif ($post->post_type == 'post') {
        $relationships = get_post_meta($post->ID, 'article_author', true);
    } else {
        return $content; // Exit early if not relevant post type
    }

    if (!is_array($relationships)) {
        $relationships = [$relationships];
    }

    foreach ($relationships as $related_post) {
        if ($related_post) {
            $content .= ' ' . get_the_title($related_post);
        }
    }

    return $content;
}

// Sanitize the search query before running it
function secure_search_query($query) {
    if ($query->is_search() && !is_admin()) {
        $query->set('s', sanitize_text_field($query->get('s')));
    }
}
add_action('pre_get_posts', 'secure_search_query');

// ===== COMMENT VALIDATION =====

// Block special characters in WordPress comment content
add_filter('preprocess_comment', 'wpdiscuz_block_special_chars_in_comment');

function wpdiscuz_block_special_chars_in_comment($commentdata) {
    $content = $commentdata['comment_content'];

    // Block unwanted special characters
    if (preg_match('/[<>{}\[\]^`~|\/@#$%&*_=+]+/', $content)) {
        wp_die(
            'Special characters are not allowed in comments. Please revise and submit again.',
            'Comment Blocked',
            ['response' => 403]
        );
    }

    return $commentdata;
}

// Proper hook for comment validation (Jetpack + wpDiscuz safe)
add_filter('preprocess_comment', 'wpdiscuz_block_special_chars_in_comment');

function wpdiscuz_strict_validation_block() {
    if (!is_singular() || !comments_open()) return;
    ?>
    <script>
    (function () {
        function initValidation() {
            const form = document.querySelector('.wpd-form');
            if (!form) return;

            const submitBtn = form.querySelector('.wc_comm_submit');
            if (!submitBtn) return;

            // Prevent double binding
            if (submitBtn.dataset.validationBound === "true") return;
            submitBtn.dataset.validationBound = "true";

            submitBtn.addEventListener('click', function (e) {
                const nameField = form.querySelector('input[name="wc_name"]');
                const commentField = form.querySelector('.ql-editor');

                const name = nameField?.value.trim() || '';
                const comment = commentField?.innerText.trim() || '';

                const disallowed = /[<>{}\[\]^~|\\`$%*=+]/;
                let errorMessage = '';

                if (disallowed.test(name)) {
                    errorMessage += 'Special characters are not allowed in the name.\n';
                }
                if (disallowed.test(comment)) {
                    errorMessage += 'Special characters are not allowed in the comment.';
                }

                if (errorMessage) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    alert(errorMessage);
                    return false;
                }
            }, true);
        }

        // Watch for dynamic wpDiscuz load
        const observer = new MutationObserver(() => {
            initValidation();
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true
        });

        // Try once on DOM load
        document.addEventListener('DOMContentLoaded', initValidation);
    })();
    </script>
    <?php
}

// ===== CF7 VALIDATION FUNCTIONS =====

// Form Main validation filter for text, email, textarea, and file fields
add_filter('wpcf7_validate_text*', 'cf7_custom_validations', 10, 2);
add_filter('wpcf7_validate_text', 'cf7_custom_validations', 10, 2);
add_filter('wpcf7_validate_email*', 'cf7_custom_validations', 10, 2);
add_filter('wpcf7_validate_email', 'cf7_custom_validations', 10, 2);
add_filter('wpcf7_validate_textarea*', 'cf7_custom_validations', 10, 2);
add_filter('wpcf7_validate_textarea', 'cf7_custom_validations', 10, 2);
add_filter('wpcf7_validate_file', 'cf7_custom_validations', 10, 2);

function cf7_custom_validations($result, $tag) {
    $tag = new WPCF7_FormTag($tag);
    $name = $tag->name;
    $value = isset($_POST[$name]) ? trim($_POST[$name]) : '';

    switch ($name) {
        case 'full-name':
        case 'your-name':
            if (!preg_match('/^[a-zA-Z\s]+$/', $value)) {
                $result->invalidate($tag, 'Only letters and spaces are allowed in the name.');
            }
            break;

        case 'your-email':
            if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $result->invalidate($tag, 'Invalid email format.');
            }
            break;

        case 'authors-bio':
        case 'short-summary':
            // Special character check
            if (preg_match('/[<>{}\[\];|$#]/', $value)) {
                $result->invalidate($tag, 'Special characters are not allowed in the message.');
            }
        
            $word_count = str_word_count($value);
            if ($word_count > 100) {
                $result->invalidate($tag, "Maximum 100 words allowed. You entered $word_count words.");
            }
            break;

        case 'your-message':
            if (preg_match('/[<>{}\[\];|$#]/', $value)) {
                $result->invalidate($tag, 'Special characters are not allowed in the message.');
            }
            break;

        case 'file-upload':
            if (!empty($_FILES[$name]['name'])) {
                $file = $_FILES[$name];
                $allowed_types = [
                    'application/zip',
                    'application/pdf',
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
                ];

                if (!in_array($file['type'], $allowed_types)) {
                    $result->invalidate($tag, 'Only ZIP, PDF, or Word documents are allowed.');
                }

                if ($file['size'] > 5 * 1024 * 1024) {
                    $result->invalidate($tag, 'File size should not exceed 5MB.');
                }
            }
            break;
    }

    return $result;
}

// Remove default validation rules for text and textarea fields
remove_action('wpcf7_swv_create_schema', 'wpcf7_swv_add_text_rules', 10);
remove_action('wpcf7_swv_create_schema', 'wpcf7_swv_add_textarea_rules', 10);

// Add custom validation rules for specific fields (text, email, textarea)
add_action('wpcf7_swv_create_schema', function ($schema, $contact_form) {
    $tags = $contact_form->scan_form_tags();

    foreach ($tags as $tag) {
        $error_message = wpcf7_get_message('invalid_required');

        if ($tag->is_required()) {
            if ($tag->name === 'full-name') {
                $error_message = 'Please enter your name.';
            } elseif ($tag->name === 'email-id') {
                $error_message = 'Please enter an email address.';
            } elseif ($tag->name === 'authors-bio') {
                $error_message = 'Please fill in the author bio.';
            } elseif ($tag->name === 'short-summary') {
                $error_message = 'Please provide a short summary.';
            }

            // Don't apply SWV 'required' to file fields — handled separately
            if ($tag->basetype !== 'file') {
                $schema->add_rule(
                    wpcf7_swv_create_rule('required', [
                        'field' => $tag->name,
                        'error' => $error_message,
                    ])
                );
            }
        }

        // Only apply email validation for email-id
        if ('email' === $tag->basetype && $tag->name === 'email-id') {
            $schema->add_rule(
                wpcf7_swv_create_rule('email', [
                    'field' => $tag->name,
                    'error' => wpcf7_get_message('invalid_email'),
                ])
            );
        }
    }
}, 10, 2);

// ===== REDIRECT & ARCHIVE FUNCTIONS =====

// Redirect logic for magazine_issue and resource post types
function custom_redirect_single_cpt() {
    if (is_admin() || !is_singular()) return;

    global $post;

    // Redirect single magazine_issue if 'old_magazine_issue' is 'yes'
    if ($post->post_type === 'magazine_issue') {
        $is_old_issue = get_field('old_magazine_issue', $post->ID); // Checkbox returns array
        $old_link = get_field('old_magazine_link', $post->ID);

        if (is_array($is_old_issue) && in_array('yes', $is_old_issue) && !empty($old_link)) {
            wp_redirect(esc_url($old_link), 301);
            exit;
        }
    }

   
  // Detect preview mode
$is_preview = isset($_GET['preview']) && $_GET['preview'] === 'true' && isset($_GET['token']);

// Redirect single resource to external_url, but skip preview/pending
if ($post->post_type === 'resource') {

    // Only redirect if NOT previewing
    if (!$is_preview && $post->post_status !== 'pending') {
        $external_url = get_field('external_url', $post->ID);
        if (!empty($external_url)) {
            wp_redirect(esc_url($external_url), 301);
            exit;
        }
    }

}
}
add_action('template_redirect', 'custom_redirect_single_cpt');

//Disable tag archives
function disable_tag_archives() {
    if (is_tag()) {
        wp_redirect(home_url(), 301);
        exit;
    }
}
add_action('template_redirect', 'disable_tag_archives');

// ===== PAGINATION & REWRITE RULES =====

function authors_bio_single_pagination_rewrite() {
    add_rewrite_rule(
        '^authors_bio/([^/]+)/pa/([0-9]+)/?$',
        'index.php?authors_bio=$matches[1]&pa=$matches[2]',
        'top'
    );
}
add_action('init', 'authors_bio_single_pagination_rewrite');

function authors_bio_add_query_vars($vars) {
    $vars[] = 'pa';
    return $vars;
}
add_filter('query_vars', 'authors_bio_add_query_vars');

// ===== ERROR HANDLING & DEBUGGING =====

// PHP Deprecated: ltrim():
add_action('init', function() {
    set_error_handler(function($errno, $errstr, $errfile, $errline) {
        if ($errno === E_DEPRECATED && 
            strpos($errstr, 'ltrim()') !== false && 
            strpos($errfile, 'wp-includes/formatting.php') !== false) {
            return true; // Suppress ltrim deprecation warnings
        }
        return false;
    }, E_DEPRECATED);
}, 1);

// Potential wpml debugging hook
function custom_wpml_debugging() {
    if (defined('WP_DEBUG') && WP_DEBUG) {
        add_action('wpml_tm_ate_job_receive_error', function($job_id, $error) {
            error_log("WPML Translation Job {$job_id} Failed: " . print_r($error, true));
        }, 10, 2);

        add_filter('wpml_tm_ate_job_receive_args', function($args) {
            error_log('ATE Job Receive Args: ' . print_r($args, true));
            return $args;
        });
    }
}
add_action('init', 'custom_wpml_debugging');


// ===== ADMIN STYLING =====

function my_column_width() {
    echo '<style type="text/css">';
    echo '.table-view-list.posts .column-title { width:150px !important; overflow:hidden }';
    echo '.table-view-list.posts .column-icl_translations { width:50px !important; overflow:hidden }';
    echo '.table-view-list.posts .column-post_order { width:50px !important; overflow:hidden }';
    echo '.table-view-list.posts .column-taxonomy-magazine_category { width:150px !important; overflow:hidden }';
    echo '</style>';
}
add_action('admin_head', 'my_column_width');

// Flush rewrite rules on init
/**
 * Change the email recipient for comment notifications and moderation.
 */
function custom_comment_notification_email( $emails, $comment_id ) {

    // Replace with your desired email address
    $emails = array(
        'madhukara.putty@apu.edu.in',
        'iwonder@apu.edu.in',
    );

    return $emails;
}

add_filter( 'comment_notification_recipients', 'custom_comment_notification_email', 10, 2 );
add_filter( 'comment_moderation_recipients', 'custom_comment_notification_email', 10, 2 );


/**
 * Make email clickable in Contact Form 7 success message.
 */
function custom_cf7_clickable_email() {
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function () {

        const observer = new MutationObserver(function () {

            const outputs = document.querySelectorAll('.wpcf7-response-output');

            outputs.forEach(function (el) {
                if (!el.dataset.emailConverted) {
                    el.innerHTML = el.innerHTML.replace(
                        /([A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,})/g,
                        '<a href="mailto:$1">$1</a>'
                    );
                    el.dataset.emailConverted = "true";
                }
            });

        });

        observer.observe(document.body, {
            childList: true,
            subtree: true
        });

    });
    </script>
    <?php
}
add_action('wp_footer', 'custom_cf7_clickable_email');