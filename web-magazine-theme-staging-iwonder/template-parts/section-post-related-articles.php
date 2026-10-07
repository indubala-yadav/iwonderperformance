<!-- related articles -->
<?php
$post_id = get_the_ID();
$post_type = get_post_type($post_id);
$related_ids = [$post_id];
$final_related_posts = [];

function add_related_post($post_obj, &$final_related_posts, &$related_ids) {
    if (count($final_related_posts) >= 6) return false;

    $pid = $post_obj->ID;

    if (!in_array($pid, $related_ids)) {
        $final_related_posts[$pid] = $post_obj;
        $related_ids[] = $pid;
        return true;
    }
    return false;
}

$remaining = function() use ($final_related_posts) {
    return 6 - count($final_related_posts);
};


// ==========================
// 1. TAGS
// ==========================
if ($remaining() > 0) {

    $tags = wp_get_post_tags($post_id);

    if ($tags) {

        $tag_ids = wp_list_pluck($tags, 'term_id');

        $query = new WP_Query([
            'post_type' => $post_type,
            'tag__in' => $tag_ids,
            'post__not_in' => $related_ids,
            'posts_per_page' => $remaining(),
            'ignore_sticky_posts' => 1,
        ]);

        while ($query->have_posts()) {
            $query->the_post();
            if (!add_related_post(get_post(), $final_related_posts, $related_ids)) break;
        }

        wp_reset_postdata();
    }
}


// ==========================
// 2. AUTHOR (ACF + WPML FIX)
// ==========================
if ($remaining() > 0) {

    $author_field = $post_type === 'resource' ? 'resources_author' : 'article_author';
    $authors = get_field($author_field, $post_id);

    if ($authors) {

        $current_lang = apply_filters('wpml_current_language', null);
        $meta_query = ['relation' => 'OR'];

        foreach ((array) $authors as $author_post) {

            $id = is_object($author_post) ? $author_post->ID : $author_post;

            // ✅ WPML translate ID
            $translated_id = apply_filters('wpml_object_id', $id, 'authors_bio', true, $current_lang);

            if ($translated_id) {
                // ✅ IMPORTANT: use LIKE for serialized ACF
                $meta_query[] = [
                    'key' => $author_field,
                    'value' => '"' . $translated_id . '"',
                    'compare' => 'LIKE'
                ];
            }
        }

        if (count($meta_query) > 1) {

            $query = new WP_Query([
                'post_type' => $post_type,
                'post__not_in' => $related_ids,
                'posts_per_page' => $remaining(),
                'ignore_sticky_posts' => 1,
                'meta_query' => $meta_query,
            ]);

            while ($query->have_posts()) {
                $query->the_post();
                if (!add_related_post(get_post(), $final_related_posts, $related_ids)) break;
            }

            wp_reset_postdata();
        }
    }
}


// ==========================
// 3. CATEGORY / TAXONOMY
// ==========================
if ($remaining() > 0) {

    if ($post_type === 'resource') {

        $terms = get_the_terms($post_id, 'resource_type');

        if ($terms && !is_wp_error($terms)) {

            $term_ids = wp_list_pluck($terms, 'term_id');

            $query = new WP_Query([
                'post_type' => $post_type,
                'tax_query' => [[
                    'taxonomy' => 'resource_type',
                    'field' => 'term_id',
                    'terms' => $term_ids,
                ]],
                'post__not_in' => $related_ids,
                'posts_per_page' => $remaining(),
                'ignore_sticky_posts' => 1,
            ]);
        }

    } else {

        $categories = get_the_category($post_id);

        if ($categories) {

            $cat_ids = wp_list_pluck($categories, 'term_id');

            $query = new WP_Query([
                'post_type' => $post_type,
                'category__in' => $cat_ids,
                'post__not_in' => $related_ids,
                'posts_per_page' => $remaining(),
                'ignore_sticky_posts' => 1,
            ]);
        }
    }

    if (isset($query) && $query->have_posts()) {

        while ($query->have_posts()) {
            $query->the_post();
            if (!add_related_post(get_post(), $final_related_posts, $related_ids)) break;
        }

        wp_reset_postdata();
    }
}


// ==========================
// OUTPUT
// ==========================
if (!empty($final_related_posts)) : ?>

<div class="theme-section-bg px-3 position-relative apum-section related-articles-section pt-48-96 pb-200">
    <div class="container d-flex flex-column gap-24-48">

        <h2 class="title-area add-motif fs-24-40">
            <?php echo esc_html($post_type === 'resource' ? t('iw_more_resources') : t('iw_more_articles')); ?>
        </h2>

        <div class="related-posts-slider common-slick">
            <?php
            foreach ($final_related_posts as $post) {
                setup_postdata($post);

                $author_title = get_field('author_title', $post->ID);
                $additional_classes = 'card-column bg-white';
                $show_author = true;
                $image_size = [1280, 720];
                $external_url = $post_type === 'resource' ? get_field('external_url') : null;

                get_template_part(
                    'template-parts/articlecard',
                    null,
                    compact('author_title', 'additional_classes', 'show_author', 'image_size', 'external_url')
                );
            }
            wp_reset_postdata();
            ?>
        </div>

        <?php
$current_lang = apply_filters('wpml_current_language', null);

// Get correct slug (from your translation function)
$slug = $post_type === 'resource'
    ? t('iw_more_resources_url')
    : t('iw_more_articles_url');

// Build language-aware URL
$url = home_url('/' . $slug . '/');

// If not default language, prepend language code
if ($current_lang && $current_lang !== apply_filters('wpml_default_language', null)) {
    $url = home_url('/' . $current_lang . '/' . $slug . '/');
}

// Add query param
$url = add_query_arg('from', $post_id, $url);
?>

<div class="text-center mt-0 mt-md-4">
    <a class="btn btn-primary btn-animate"
       href="<?php echo esc_url($url); ?>">
        <span>
            <?php echo esc_html($post_type === 'resource' ? t('iw_all_resources') : t('iw_more_articles')); ?>
        </span>
        <i class="fas fa-arrow-right ms-2"></i>
    </a>
</div>

    </div>
</div>

<?php endif; ?>