<?php
/*
Template Name: All Resources
*/

get_header();

$post_id = isset($_GET['from']) ? intval($_GET['from']) : 0;

if (!$post_id || get_post_type($post_id) !== 'resource') {
    wp_redirect(home_url('/resources/'));
    exit;
}

$search_query = isset($_REQUEST['search_query']) ? sanitize_text_field($_REQUEST['search_query']) : '';

$related_ids = [];
$related_posts = [];

// STEP 1: Related by Tags
$tags = wp_get_post_tags($post_id);
if ($tags) {
    $tag_ids = wp_list_pluck($tags, 'term_id');
    $query_args = [
        'post_type' => 'resource',
        'tag__in' => $tag_ids,
        'post__not_in' => [$post_id],
        'posts_per_page' => -1,
        'ignore_sticky_posts' => 1,
    ];
    if ($search_query) {
        $query_args['s'] = $search_query;
        $query_args['relevanssi'] = true; // Only if Relevanssi is used
    }
    $query = new WP_Query($query_args);
    while ($query->have_posts()) {
        $query->the_post();
        if (!in_array(get_the_ID(), $related_ids)) {
            $related_ids[] = get_the_ID();
            $related_posts[] = get_post();
        }
    }
    wp_reset_postdata();
}

// STEP 2: Related by resources_author ACF
$authors = get_field('resources_author', $post_id);
if ($authors) {
    $author_ids = [];
    foreach ((array)$authors as $author_post) {
        $author_ids[] = is_object($author_post) ? $author_post->ID : $author_post;
    }

    $query_args = [
        'post_type' => 'resource',
        'post__not_in' => array_merge([$post_id], $related_ids),
        'meta_query' => [[
            'key' => 'resources_author',
            'value' => $author_ids,
            'compare' => 'IN',
        ]],
        'posts_per_page' => -1,
        'ignore_sticky_posts' => 1,
    ];
    if ($search_query) {
        $query_args['s'] = $search_query;
        $query_args['relevanssi'] = true;
    }
    $query = new WP_Query($query_args);
    while ($query->have_posts()) {
        $query->the_post();
        if (!in_array(get_the_ID(), $related_ids)) {
            $related_ids[] = get_the_ID();
            $related_posts[] = get_post();
        }
    }
    wp_reset_postdata();
}

// STEP 3: Related by resource_category
$categories = get_the_terms($post_id, 'resource_type');
if ($categories && !is_wp_error($categories)) {
    $cat_ids = wp_list_pluck($categories, 'term_id');
    $query_args = [
        'post_type' => 'resource',
        'tax_query' => [[
            'taxonomy' => 'resource_type',
            'field' => 'term_id',
            'terms' => $cat_ids,
        ]],
        'post__not_in' => array_merge([$post_id], $related_ids),
        'posts_per_page' => -1,
        'ignore_sticky_posts' => 1,
    ];
    if ($search_query) {
        $query_args['s'] = $search_query;
        $query_args['relevanssi'] = true;
    }
    $query = new WP_Query($query_args);
    while ($query->have_posts()) {
        $query->the_post();
        if (!in_array(get_the_ID(), $related_ids)) {
            $related_ids[] = get_the_ID();
            $related_posts[] = get_post();
        }
    }
    wp_reset_postdata();
}

// PAGINATION
$paged = max(1, get_query_var('paged'));
$posts_per_page = get_option('posts_per_page');
$total_related = count($related_posts);
$total_pages = ceil($total_related / $posts_per_page);
$offset = ($paged - 1) * $posts_per_page;
$posts_to_display = array_slice($related_posts, $offset, $posts_per_page);
?>
<?php get_template_part('template-parts/main-wrapper-start'); ?>

            <div class="px-3 px-md-0 pb-4">
            <div class="d-flex flex-column justify-items-center title-area add-motif mb-4">
				<h1 class="mb-5"><?php echo esc_html( t('iw_more_resources') ); ?></h1>

            </div>

            <!-- Search & Filters -->
            <?php get_template_part('template-parts/search-and-filter'); ?>

            <div class="apum-animation-fadeInUp">
                <?php if (!empty($posts_to_display)): ?>
                    <div class="d-grid grid-col-3 gap-24">
                        <?php foreach ($posts_to_display as $post): setup_postdata($post); ?>
                            <?php
                            $author_title = get_field('author_title', $post->ID);
                            $additional_classes = 'card-column bg-white';
                            $show_author = true;
                            $image_size = [1280, 720];
                            $external_url = get_field('external_url');
                            $short_note = get_field('resource_short_note');
                            get_template_part('template-parts/articlecard', null, compact('author_title', 'additional_classes', 'show_author', 'image_size','external_url','short_note'));
                            ?>
                        <?php endforeach; wp_reset_postdata(); ?>
                    </div>

                    <!-- Pagination -->
                    <nav class="pagination-container mt-5 apum-animation-fadeInUp">
                        <?php
                        $big = 999999999;
                        $current_page = max(1, get_query_var('paged'));
                        if ($total_pages > 1) {
                            echo '<ul class="pagination">';

                            // Previous
                            if ($current_page > 1) {
                                echo '<li class="prev"><a href="' . esc_url(get_pagenum_link($current_page - 1)) . '"><i class="fas fa-arrow-left"></i><span class="btn-text">' . esc_html( t('iw_previous') ) . '</span></a></li>';
                            } else {
                                echo '<li class="prev disabled"><span><i class="fas fa-arrow-left"></i><span class="btn-text">' . esc_html( t('iw_previous') ) . '</span></span></li>';
                            }

                            // Page numbers
                            $pagination_links = paginate_links([
                                'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
                                'format' => '?paged=%#%',
                                'current' => $current_page,
                                'total' => $total_pages,
                                'mid_size' => 1,
                                'end_size' => 1,
                                'type' => 'array',
                                'prev_next' => false,
                            ]);
                            if ($pagination_links) {
                                foreach ($pagination_links as $link) {
                                    echo '<li>' . $link . '</li>';
                                }
                            }

                            // Next
                            if ($current_page < $total_pages) {
                                echo '<li class="next"><a href="' . esc_url(get_pagenum_link($current_page + 1)) . '"><span class="btn-text">' . esc_html( t('iw_next') ) . '</span><i class="fas fa-arrow-right"></i></a></li>';
                            } else {
                                echo '<li class="next disabled"><span class="btn-text">' . esc_html( t('iw_next') ) . '</span><i class="fas fa-arrow-right"></i></li>';
                            }

                            echo '</ul>';
                        }
                        ?>
                    </nav>

                <?php else: ?>
				<p><?php echo esc_html( t('iw_no_related_resources_found') ); ?></p>

                <?php endif; ?>
            </div>
            

<?php $has_search_query = isset($_REQUEST['search_query']) && !empty($_REQUEST['search_query']); ?>
<?php if ($has_search_query) : ?>
<div class="text-center mt-5">
    <div class="mt-3 ms-md-3">
        <a class="btn btn-primary btn-animate" href="<?php echo site_url('/all-resources/?from=' . $post_id); ?>" role="button">
            <i class="fas fa-arrow-left"></i> <?php echo esc_html( t('iw_go_back') ); ?>

        </a>
    </div>
</div>
<?php endif; ?>
</div>
<?php
get_template_part('template-parts/main-wrapper-end');
?>

<?php get_template_part('template-parts/section', 'info-cards'); ?>
<?php get_footer(); ?>