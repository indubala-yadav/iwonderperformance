<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
/*
Template Name: Archives
*/
function iw_get_wpml_default_term_ids_by_slugs( $slugs, $taxonomy ) {

    if ( empty( $slugs ) ) {
        return [];
    }

    $default_lang = apply_filters( 'wpml_default_language', null );
    $term_ids     = [];

    foreach ( $slugs as $slug ) {

        $term = get_term_by( 'slug', $slug, $taxonomy );
        if ( ! $term || is_wp_error( $term ) ) {
            continue;
        }

        $default_term_id = apply_filters(
            'wpml_object_id',
            $term->term_id,
            $taxonomy,
            true,
            $default_lang
        );

        if ( $default_term_id ) {
            $term_ids[] = (int) $default_term_id;
        }
    }

    return array_unique( $term_ids );
}

get_header();

/* -------------------------------------------
 * READ FILTER VALUES
 * ------------------------------------------- */

// Category slugs (WPML-safe sanitization)
$selected_categories = [];
if ( isset( $_GET['category'] ) ) {
    $raw = wp_unslash( $_GET['category'] );
    $selected_categories = array_map( 'sanitize_title', explode( ',', $raw ) );
}

// Search
$search_query = isset( $_GET['search_query'] )
    ? sanitize_text_field( wp_unslash( $_GET['search_query'] ) )
    : '';

// Pagination
$paged = max( 1, get_query_var( 'paged' ) );

// Post type
$post_type = 'post';

/* -------------------------------------------
 * BUILD QUERY
 * ------------------------------------------- */

$args = array(
    'post_type'      => $post_type,
    'posts_per_page' => get_option( 'posts_per_page' ),
    'paged'          => $paged,
);

// Search filter
if ( ! empty( $search_query ) ) {
    $args['s'] = $search_query;
    $args['relevanssi'] = true; // keep if using Relevanssi
}

// Category filter (WPML SAFE)
if ( ! empty( $selected_categories ) ) {

    $term_ids = iw_get_wpml_default_term_ids_by_slugs(
        $selected_categories,
        'category'
    );

    if ( ! empty( $term_ids ) ) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'category',
                'field'    => 'term_id',
                'terms'    => $term_ids,
            ),
        );
    }
}

/* -------------------------------------------
 * EXCLUDE "UNCATEGORIZED"
 * ------------------------------------------- */

$args['tax_query'][] = array(
    'taxonomy' => 'category',
    'field'    => 'slug',
    'terms'    => array( 'uncategorized' ),
    'operator' => 'NOT IN',
);

// Query
$custom_query = new WP_Query( $args );
?>

<?php get_template_part('template-parts/main-wrapper-start'); ?>

<div class="pb-4">
    <div class="d-flex flex-column justify-items-center title-area add-motif mb-4">
        <?php custom_breadcrumb(); ?>
        <h1><?php the_archive_title(); ?></h1>
        <h2 class="sr-only d-none" >At Right Angles, A Resource for school Mathematics</h2>
      <div class="category-description maxw-720 ms-0 fs-18">
    <?php echo iw_trim_content( t('iw_articles-description') ); ?>
</div>
    </div>

    <!-- Search & Filters -->
    <?php if (false) : ?>
    <?php get_template_part('template-parts/search-and-filter'); ?>
<?php endif; ?>

    <div class="apum-animation-fadeInUp">
        <?php if ($custom_query->have_posts()) : ?>
            <div class="d-grid grid-col-3">
                <?php while ($custom_query->have_posts()) : $custom_query->the_post(); ?>
                    <?php
                    $author_title = get_field('author_title');
                    $additional_classes = 'card-column h-100';
                    $show_author = true;
                    $image_size = [1280, 720];

                    get_template_part('template-parts/articlecard', null, compact(
                        'author_title',
                        'additional_classes',
                        'show_author',
                        'image_size'
                    ));
                    ?>
                <?php endwhile; ?>
            </div>

            <!-- Pagination -->
            <nav class="pagination-container mt-5" aria-label="<?php echo esc_attr(t('iw_pagination')); ?>">
                <?php
                $big = 999999999;
                $current_page = max(1, get_query_var('paged'));
                $total_pages = $custom_query->max_num_pages;

                if ($total_pages > 1) {
                    echo '<ul class="pagination">';

                    // Previous
                    if ($current_page > 1) {
                        echo '<li class="prev"><a href="' . esc_url(get_pagenum_link($current_page - 1)) . '" aria-label="' . esc_attr(t('iw_previous_page')) . '"><i class="fas fa-arrow-left" ></i><span class="btn-text">' . esc_html(t('iw_previous')) . '</span></a></li>';
                    } else {
                        echo '<li class="prev disabled"><i class="fas fa-arrow-left" ></i><span class="btn-text">' . esc_html(t('iw_previous')) . '</span></li>';
                    }

                    // Page numbers
                    $pagination_links = paginate_links(array(
                        'base'      => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
                        'format'    => '?paged=%#%',
                        'current'   => $current_page,
                        'total'     => $total_pages,
                        'mid_size'  => 1,
                        'end_size'  => 1,
                        'type'      => 'array',
                        'prev_next' => false,
                    ));

                    if (!empty($pagination_links)) {
                        foreach ($pagination_links as $link) {
                            echo '<li>' . $link . '</li>';
                        }
                    }

                    // Next
                    if ($current_page < $total_pages) {
                        echo '<li class="next"><a href="' . esc_url(get_pagenum_link($current_page + 1)) . '" aria-label="' . esc_attr(t('iw_next_page')) . '"><span class="btn-text">' . esc_html(t('iw_next')) . '</span><i class="fas fa-arrow-right"></i></a></li>';
                    } else {
                        echo '<li class="next disabled"><span class="btn-text">' . esc_html(t('iw_next')) . '</span><i class="fas fa-arrow-right"></i></li>';
                    }

                    echo '</ul>';
                }
                ?>
            </nav>

        <?php else : ?>
            <div class="text-center">
                <h4><?php echo esc_html(t('iw_no_articles_found')); ?></h4>

                <?php get_template_part('template-parts/screenscananimation'); ?>

                <div class="text-center mt-48-124">
                    <div class="mt-3 ms-md-3">
                        <?php
                        // Reset URL without filters
                        $current_url = home_url(add_query_arg(null, null));
                        $clean_url = remove_query_arg(array('search_query', 'category', 'paged'), $current_url);

                        ?>
                        <a class="btn btn-primary" href="<?php echo esc_url($clean_url); ?>" role="button">
                            <i class="fa-solid fa-arrow-rotate-right me-1"></i>
                            <span><?php echo esc_html(t('iw_reset')); ?></span>
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php wp_reset_postdata(); ?>
    </div>
</div>
<?php get_template_part('template-parts/main-wrapper-end'); ?>

<!-- Info Cards Section -->
<?php get_template_part('template-parts/section', 'info-cards'); ?>

<?php get_footer(); ?>
