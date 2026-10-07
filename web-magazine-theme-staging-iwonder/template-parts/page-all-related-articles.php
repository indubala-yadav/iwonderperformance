<?php
/*
Template Name: All Articles
*/

get_header();

// ✅ Get current language
$current_lang = apply_filters('wpml_current_language', null);

// ✅ Get reference post ID
$original_post_id = isset($_GET['from']) ? intval($_GET['from']) : 0;

// Keep original ID for relation logic
$post_id = $original_post_id;

// Get translated ID ONLY when needed
$translated_post_id = apply_filters('wpml_object_id', $original_post_id, 'post', true);

// ✅ Convert to current language post ID (VERY IMPORTANT)
$post_id = apply_filters('wpml_object_id', $post_id, 'post', true);

// Base args
$args = array(
    'post_type' => 'post',
    'post__not_in' => [$post_id],
    'posts_per_page' => -1,
    'ignore_sticky_posts' => 1,
    'lang' => $current_lang, // ✅ WPML filter
);

// Search support
if (isset($_REQUEST['search_query']) && $_REQUEST['search_query'] !== '') {
    $args['s'] = sanitize_text_field($_REQUEST['search_query']);
    $args['relevanssi'] = true;
}

$related_ids = [];
$related_posts = [];

/* =========================
   STEP 1: TAGS
========================= */
$tags = wp_get_post_tags($post_id);
if ($tags) {

    // ✅ Convert tags to current language
    $tag_ids = [];
    foreach ($tags as $tag) {
        $translated_tag = apply_filters('wpml_object_id', $tag->term_id, 'post_tag', true);
        if ($translated_tag) {
            $tag_ids[] = $translated_tag;
        }
    }

    if (!empty($tag_ids)) {
        $args_tag = $args;
        $args_tag['tag__in'] = $tag_ids;

        $query = new WP_Query($args_tag);
        while ($query->have_posts()) {
            $query->the_post();
            if (!in_array(get_the_ID(), $related_ids)) {
                $related_ids[] = get_the_ID();
                $related_posts[] = get_post();
            }
        }
        wp_reset_postdata();
    }
}

/* =========================
   STEP 2: AUTHORS (ACF)
========================= */
$authors = get_field('article_author', $post_id);

if ($authors) {

    $author_ids = [];

    foreach ((array)$authors as $author_post) {
        $id = is_object($author_post) ? $author_post->ID : $author_post;

        // ✅ Translate author post ID
        $translated_author = apply_filters('wpml_object_id', $id, 'authors_bio', true);

        if ($translated_author) {
            $author_ids[] = $translated_author;
        }
    }

    if (!empty($author_ids)) {
        $args_author = $args;

        $args_author['meta_query'] = [[
            'key' => 'article_author',
            'value' => $author_ids,
            'compare' => 'IN',
        ]];

        $args_author['post__not_in'] = array_merge([$post_id], $related_ids);

        $query = new WP_Query($args_author);
        while ($query->have_posts()) {
            $query->the_post();
            if (!in_array(get_the_ID(), $related_ids)) {
                $related_ids[] = get_the_ID();
                $related_posts[] = get_post();
            }
        }
        wp_reset_postdata();
    }
}

/* =========================
   STEP 3: CATEGORIES
========================= */
$categories = get_the_category($post_id);

if ($categories) {

    // ✅ Translate categories
    $cat_ids = [];
    foreach ($categories as $cat) {
        $translated_cat = apply_filters('wpml_object_id', $cat->term_id, 'category', true);
        if ($translated_cat) {
            $cat_ids[] = $translated_cat;
        }
    }

    if (!empty($cat_ids)) {
        $args_cat = $args;
        $args_cat['category__in'] = $cat_ids;
        $args_cat['post__not_in'] = array_merge([$post_id], $related_ids);

        $query = new WP_Query($args_cat);
        while ($query->have_posts()) {
            $query->the_post();
            if (!in_array(get_the_ID(), $related_ids)) {
                $related_ids[] = get_the_ID();
                $related_posts[] = get_post();
            }
        }
        wp_reset_postdata();
    }
}

/* =========================
   PAGINATION
========================= */
$paged = max(1, get_query_var('paged'));
$posts_per_page = get_option('posts_per_page');

$total_related = count($related_posts);
$total_pages = ceil($total_related / $posts_per_page);

$offset = ($paged - 1) * $posts_per_page;
$posts_to_display = array_slice($related_posts, $offset, $posts_per_page);
?>

<?php get_template_part('template-parts/main-wrapper-start'); ?>

<div class="pb-4">

    <div class="d-flex flex-column justify-items-center title-area add-motif mb-4">
        <h1 class="mb-3"><?php echo esc_html(t('iw_more_articles')); ?></h1>

        <?php
        $acf_authors = get_field('article_author', $post_id);
        $categories = get_the_category($post_id);
        $tags = get_the_tags($post_id);

        $author_names = [];
        if ($acf_authors) {
            foreach ((array)$acf_authors as $author_post) {
                $id = is_object($author_post) ? $author_post->ID : $author_post;

                $translated_author = apply_filters('wpml_object_id', $id, 'authors_bio', true);

                if ($translated_author) {
                    $author_names[] = get_the_title($translated_author);
                }
            }
        }
        ?>

        <?php if (!empty($posts_to_display)) : ?>
            <div class="reference-meta text-muted">
                <span><strong><?php echo esc_html(t('iw_relatedwith')); ?></strong></span>

                <?php if (!empty($tags)) : ?>
                    <span class="me-3"><strong><?php echo esc_html(t('iw_tags')); ?>:</strong>
                        <?php
                        $tag_output = array_map(function ($tag) {
                            return '<span>' . esc_html($tag->name) . '</span>';
                        }, $tags);
                        echo implode(', ', $tag_output);
                        ?>
                    </span>
                <?php endif; ?>

                <?php if (!empty($author_names)) : ?>
                    <span class="me-3"><strong><?php echo esc_html(t('iw_authors')); ?>:</strong>
                        <?php echo implode(', ', $author_names); ?>
                    </span>
                <?php endif; ?>

                <?php if (!empty($categories)) : ?>
                    <span><strong><?php echo esc_html(t('iw_categories')); ?>:</strong>
                        <?php
                        $cat_output = array_map(function ($cat) {
                            return '<span>' . esc_html($cat->name) . '</span>';
                        }, $categories);
                        echo implode(', ', $cat_output);
                        ?>
                    </span>
                <?php endif; ?>
            </div>
        <?php endif; ?>

    </div>

    <?php get_template_part('template-parts/search-and-filter'); ?>

    <div class="apum-animation-fadeInUp">

        <?php if (!empty($posts_to_display)) : ?>

            <div class="d-grid grid-col-3 gap-24">
                <?php foreach ($posts_to_display as $post) : setup_postdata($post); ?>
                    <?php
                    $author_title = get_field('author_title', $post->ID);
                    $additional_classes = 'card-column bg-white';
                    $show_author = true;
                    $image_size = [1280, 720];

                    get_template_part(
                        'template-parts/articlecard',
                        null,
                        compact('author_title', 'additional_classes', 'show_author', 'image_size')
                    );
                    ?>
                <?php endforeach; wp_reset_postdata(); ?>
            </div>

        <?php else : ?>
            <p><?php echo esc_html(t('iw_no_related_articles_found')); ?></p>
        <?php endif; ?>

    </div>

</div>
<script>
document.addEventListener("DOMContentLoaded", function () {

    const params = new URLSearchParams(window.location.search);

    if (!params.toString()) return;

    // ✅ Target ALL WPML links (dropdown + menu)
    document.querySelectorAll('.wpml-ls-item a').forEach(link => {

        try {
            let url = new URL(link.href);

            params.forEach((value, key) => {
                url.searchParams.set(key, value);
            });

            link.href = url.toString();

        } catch (e) {}
    });

});
</script>
<?php get_template_part('template-parts/main-wrapper-end'); ?>
<?php get_template_part('template-parts/section', 'info-cards'); ?>
<?php get_footer(); ?>