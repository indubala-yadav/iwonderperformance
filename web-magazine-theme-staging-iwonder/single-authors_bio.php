<?php get_header(); ?>

<div class="site-main container-fluid p-0 pb-5 position-relative apum-innerpage-section">

    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>

            <?php
            $post_id = get_the_ID();

            /* =====================================================
   ✅ STEP 1: GET ALL AUTHOR IDS (WPML FIX)
===================================================== */
            $current_author_id = get_the_ID();

            // Get TRID
            $trid = apply_filters('wpml_element_trid', null, $current_author_id, 'post_authors_bio');

            // Get all translations
            $translations = apply_filters('wpml_get_element_translations', null, $trid, 'post_authors_bio');

            $author_ids = [];

            if (!empty($translations)) {
                foreach ($translations as $translation) {
                    if (!empty($translation->element_id)) {
                        $author_ids[] = $translation->element_id;
                    }
                }
            }

            // fallback
            if (empty($author_ids)) {
                $author_ids[] = $current_author_id;
            }

            /* =====================================================
   ✅ STEP 2: PAGINATION
===================================================== */
            // Prefer WordPress's native singular pagination var so links resolve as /page/2/.
            $paged = get_query_var('page');

            // Backward compatibility for the older custom query string.
            if (!$paged && isset($_GET['page'])) {
                $paged = wp_unslash($_GET['page']);
            } elseif (!$paged && isset($_GET['pa'])) {
                $paged = wp_unslash($_GET['pa']);
            }

            $paged = max(1, absint($paged));

            /* =====================================================
   ✅ STEP 3: CATEGORY FILTER
===================================================== */
            $selected_categories = isset($_GET['category'])
                ? explode(',', sanitize_text_field($_GET['category']))
                : [];

            /* =====================================================
   ✅ STEP 4: META QUERY (FIXED)
===================================================== */
            $meta_query = ['relation' => 'OR'];

            foreach ($author_ids as $author_id) {

                $meta_query[] = [
                    'key' => 'article_author',
                    'value' => '"' . $author_id . '"',
                    'compare' => 'LIKE'
                ];

                $meta_query[] = [
                    'key' => 'resources_author',
                    'value' => '"' . $author_id . '"',
                    'compare' => 'LIKE'
                ];
            }

            /* =====================================================
   ✅ STEP 5: MAIN QUERY
===================================================== */
            $args = [
                'post_type'      => ['post', 'resource'],
                'posts_per_page' => 6,
                'paged'          => $paged,
                'post_status'    => 'publish',
                'meta_query'     => $meta_query,
                'tax_query'      => [],
                'orderby'        => 'date',
                'order'          => 'DESC'
            ];

            // taxonomy filter
            if (!empty($selected_categories)) {
                $args['tax_query'][] = [
                    'relation' => 'OR',
                    [
                        'taxonomy' => 'category',
                        'field'    => 'slug',
                        'terms'    => $selected_categories,
                    ],
                    [
                        'taxonomy' => 'resource_category',
                        'field'    => 'slug',
                        'terms'    => $selected_categories,
                    ]
                ];
            }

            $articles_query = new WP_Query($args);
            ?>

            <div class="theme-top-ct apply-top-pattern apply-bottom-pattern pattern-contrast i-wonder-theme-top-ct"></div>

            <main id="primary" class="site-main apum-section position-relative section-pad pt-4">

                <article id="post-<?php the_ID(); ?>" class="container bg-white pt-80 pb-4 rounded d-flex flex-column gap-0">

                    <div class="maxw-720 content-area">

                        <!-- TITLE -->
                        <div class="title-area add-motif mb-5">
                            <span class="print-no"><?php custom_breadcrumb(); ?></span>

                            <h1 class="entry-title mb-0"><?php the_title(); ?></h1>

                            <?php
                            $email = get_field('authors_bio_mail');
                            if ($email): ?>
                                <a href="mailto:<?php echo esc_attr($email); ?>" target="_blank">
                                    <?php echo esc_html($email); ?>
                                </a>
                            <?php endif; ?>
                        </div>

                        <!-- AUTHOR DETAILS -->
                        <div class="<?php echo has_post_thumbnail() ? 'd-flex flex-column flex-md-row gap-24-40' : ''; ?> single-author-details">

                            <?php if (has_post_thumbnail()) : ?>
                                <div class="post-thumbnail" style="width:220px;height:220px;flex-shrink:0;">
                                    <?php the_post_thumbnail('full', [
                                        'style' => 'width:100%;height:100%;object-fit:cover;',
                                        'alt' => get_the_title()
                                    ]); ?>
                                </div>
                            <?php endif; ?>

                            <div class="entry-content">
                                <?php the_content(); ?>
                            </div>

                        </div>
                        <!-- =========================================
     ✅ RELATED ARTICLES
========================================= -->
                        <?php if ($articles_query->have_posts()) : ?>

                            <div class="container-fluid p-0 position-relative mt-5">

                                <div class="theme-top-ct apply-top-pattern apply-bottom-pattern pattern-contrast i-wonder-theme-top-ct"></div>

                                <h2><?php echo esc_html(t('iw_from_the_author')); ?></h2>

                                <div class="d-grid grid-col-2">

                                    <?php while ($articles_query->have_posts()) : $articles_query->the_post();

                                        $author_title = get_field('author_title');
                                        $additional_classes = 'card-column h-100';
                                        $show_author = true;
                                        $image_size = [1280, 720];
                                        $external_url = get_field('external_url');

                                        $post_link = (get_post_type() === 'resource' && $external_url)
                                            ? $external_url
                                            : get_permalink();

                                        get_template_part(
                                            'template-parts/articlecard',
                                            null,
                                            compact(
                                                'author_title',
                                                'additional_classes',
                                                'show_author',
                                                'image_size',
                                                'external_url',
                                                'post_link'
                                            )
                                        );

                                    endwhile; ?>

                                </div>

                                <!-- PAGINATION -->
                                <nav class="pagination-container mt-5">
                                    <?php
                                    $paged_param = get_query_var('pa');
                                    if ($paged_param) {
                                        $current_page = absint($paged_param);
                                    } elseif (isset($_GET['pa'])) {
                                        $current_page = absint($_GET['pa']);
                                    } else {
                                        $current_page = 1;
                                    }
                                    $current_page = max(1, $current_page);

                                    $total_pages = $articles_query->max_num_pages;
                                    $pagination_args = [];

                                    if (!empty($selected_categories)) {
                                        $pagination_args['category'] = implode(',', $selected_categories);
                                    }

                                    $base_permalink = trailingslashit(get_permalink($post_id));
                                    $page_link = static function ($page) use ($base_permalink, $pagination_args) {
                                        $url = add_query_arg('pa', absint($page), $base_permalink);

                                        if (!empty($pagination_args)) {
                                            $url = add_query_arg($pagination_args, $url);
                                        }

                                        return $url;
                                    };
                                    $next_page_link = $page_link($current_page + 1);
                                    $prev_page_link = $page_link($current_page - 1);

                                    if ($total_pages > 1) {

                                        echo '<ul class="pagination">';

                                        // PREV
                                        if ($current_page > 1) {
                                            echo '<li class="prev"><a href="' . esc_url($prev_page_link) . '"><i class="fas fa-arrow-left"></i> <span class="btn-text">' . t('iw_previous') . '</span></a></li>';
                                        } else {
                                            echo '<li class="prev disabled"><span><i class="fas fa-arrow-left"></i><span class="btn-text">' . t('iw_previous') . '</span></span></li>';
                                        }

                                        // FIRST
                                        if ($current_page === 1) {
                                            echo '<li><span class="page-numbers current">1</span></li>';
                                        } else {
                                            echo '<li><a class="page-numbers" href="' . esc_url($page_link(1)) . '">1</a></li>';
                                        }

                                        // Show all remaining pages without ellipsis.
                                        for ($page = 2; $page <= $total_pages; $page++) {
                                            if ($current_page === $page) {
                                                echo '<li><span class="page-numbers current">' . $page . '</span></li>';
                                            } else {
                                                echo '<li><a class="page-numbers" href="' . esc_url($page_link($page)) . '">' . $page . '</a></li>';
                                            }
                                        }

                                        // NEXT
                                        if ($current_page < $total_pages) {
                                            echo '<li class="next"><a href="' . esc_url($next_page_link) . '"><span class="btn-text">' . t('iw_next') . '</span><i class="fas fa-arrow-right"></i></a></li>';
                                        } else {
                                            echo '<li class="next disabled"><span><span class="btn-text">' . t('iw_next') . '</span><i class="fas fa-arrow-right"></i></span></li>';
                                        }

                                        echo '</ul>';
                                    }
                                    ?>
                                </nav>

                            </div>

                        <?php endif;
                        wp_reset_postdata(); ?>
                    </div>



                    <!-- =========================================
     ✅ PREV / NEXT AUTHOR NAV
========================================= -->
                    <div class="single-post-nav maxw-720 mx-auto mt-96-144">

                        <?php
                        $current_lang = apply_filters('wpml_current_language', null);

                        $prev_post = get_previous_post();
                        $next_post = get_next_post();

                        if ($prev_post) {
                            $prev_post = get_post(apply_filters('wpml_object_id', $prev_post->ID, 'authors_bio', false, $current_lang));
                        }

                        if ($next_post) {
                            $next_post = get_post(apply_filters('wpml_object_id', $next_post->ID, 'authors_bio', false, $current_lang));
                        }
                        ?>

                        <?php if ($prev_post): ?>
                            <a href="<?php echo get_permalink($prev_post); ?>" class="nav-article nav-prev mb-3">
                                <span class="nav-arrow"><i class="fas fa-arrow-left"></i></span>
                                <div>
                                    <div class="nav-label"><?php echo t('iw_previous'); ?></div>
                                    <div class="nav-title"><?php echo wp_trim_words($prev_post->post_title, 8); ?></div>
                                </div>
                            </a>
                        <?php endif; ?>

                        <?php if ($next_post): ?>
                            <a href="<?php echo get_permalink($next_post); ?>" class="nav-article nav-next">
                                <div>
                                    <div class="nav-label"><?php echo t('iw_next'); ?></div>
                                    <div class="nav-title"><?php echo wp_trim_words($next_post->post_title, 8); ?></div>
                                </div>
                                <span class="nav-arrow"><i class="fas fa-arrow-right"></i></span>
                            </a>
                        <?php endif; ?>

                    </div>

                </article>
            </main>

        <?php endwhile;
    else : ?>
        <p>No author bio found.</p>
    <?php endif; ?>

</div>

<?php get_template_part('template-parts/section', 'info-cards'); ?>
<?php get_footer(); ?>