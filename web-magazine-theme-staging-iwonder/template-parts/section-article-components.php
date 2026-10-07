
<?php
/**
 * Article component section
 */

/* Map ACF values to position keys */
$post_order_map = [
    'one'   => 1,
    'two'   => 2,
    'three' => 3,
    'four'  => 4,
    'five'  => 5,
    'six'   => 6,
    'seven' => 7,
    'eight' => 8,
    'nine'  => 9,
];

$ordered_posts = [];
$posts_exclude = [];

$uncategorized_id = get_cat_ID('Uncategorized');

/* Step 1: Get all posts with post_order defined */
$ordered_query = new WP_Query([
    'post_type'      => 'post',
    'posts_per_page' => -1,
    'meta_query'     => [
        [
            'key'     => 'post_order',
            'compare' => 'EXISTS',
        ],
    ],
    'category__not_in' => [$uncategorized_id],
]);

if ($ordered_query->have_posts()) {

    while ($ordered_query->have_posts()) {

        $ordered_query->the_post();

        $order_value = get_field('post_order');

        if (isset($post_order_map[$order_value])) {

            $pos = $post_order_map[$order_value];

            if (!isset($ordered_posts[$pos])) {

                $ordered_posts[$pos] = get_the_ID();

                $posts_exclude[] = get_the_ID();
            }
        }
    }
}

wp_reset_postdata();


/* Step 2: Fill remaining positions with latest posts */

$missing_positions = array_diff(
    range(1, 9),
    array_keys($ordered_posts)
);

if (!empty($missing_positions)) {

    $latest_query = new WP_Query([
        'post_type'      => 'post',
        'posts_per_page' => count($missing_positions),
        'post__not_in'   => $posts_exclude,
        'orderby'        => 'date',
        'order'          => 'DESC',
        'category__not_in' => [$uncategorized_id],
    ]);

    foreach ($missing_positions as $position) {

        if ($latest_query->have_posts()) {

            $latest_query->the_post();

            $ordered_posts[$position] = get_the_ID();

            $posts_exclude[] = get_the_ID();
        }
    }

    wp_reset_postdata();
}


/* Step 3: Order posts array by position */

ksort($ordered_posts);


/*
 * Step 4: Assign posts to sections
 *
 * Position 1      → Featured
 * Positions 2–3   → Sidebar
 * Positions 4–9   → Column posts
 */


/* Featured post */
$featured_post_id = $ordered_posts[1] ?? null;


/* Sidebar posts: Positions 2 and 3 */
$sidebar_post_ids = array_filter([
    $ordered_posts[2] ?? null,
    $ordered_posts[3] ?? null,
]);


/* Column posts: Positions 4–9 */
$column_post_ids = array_filter([
    $ordered_posts[4] ?? null,
    $ordered_posts[5] ?? null,
    $ordered_posts[6] ?? null,
]);

?>

<section class="apum-section article-section px-3 position-relative pt-0 pb-0 no-lazy-section svg-Hampton-bottom">

    <div class="container d-flex flex-column gap-24-48 bg-white artilce-section">

        <div class="row gx-4">

            <!-- Main Article -->
            <div
                class="col-md-7 d-flex flex-column gap-24 mt-4"
                role="region"
                aria-label="Main Articles"
            >

                <?php if ($featured_post_id): ?>

                    <?php

                    $post = get_post($featured_post_id);

                    setup_postdata($post);

                    $author_title = get_article_authors_html(get_the_ID());

                    $additional_classes = 'featured-card h-100 apum-animation-fadeInUp';

                    $show_author = true;

                    $image_size = [1280, 720];

                    $heading_tag = 'h1';

                    get_template_part(
                        'template-parts/articlecard',
                        null,
                        compact(
                            'additional_classes',
                            'show_author',
                            'image_size',
                            'heading_tag'
                        )
                    );

                    wp_reset_postdata();

                    ?>

                <?php endif; ?>

            </div>


            <!-- Sidebar -->
            <div
                class="col-md-5 d-flex flex-column justify-content-between mt-4"
                role="complementary"
                aria-label="Sidebar Articles"
            >

                <?php

                /* Sidebar sections */
                get_template_part(
                    'template-parts/section',
                    'editorial-model'
                );

                get_template_part(
                    'template-parts/section',
                    'announcements'
                );

                ?>

                <div class="d-flex flex-column gap-4">

                    <?php foreach ($sidebar_post_ids as $sb_id): ?>

                        <?php

                        $post = get_post($sb_id);

                        setup_postdata($post);

                        $author_title = get_article_authors_html(get_the_ID());

                        $categories = get_the_category();

                        $category = !empty($categories)
                            ? $categories[0]
                            : null;

                        $additional_classes =
                            'flex-row card-row flex-row-reverse align-items-center apum-animation-fadeInUp';

                        $show_author = true;

                        $image_size = [1280, 720];

                        $img_a_classes = 'ratio ratio-1x1';

                        get_template_part(
                            'template-parts/articlecard',
                            null,
                            compact(
                                'author_title',
                                'additional_classes',
                                'show_author',
                                'image_size',
                                'img_a_classes'
                            )
                        );

                        wp_reset_postdata();

                        ?>

                    <?php endforeach; ?>

                </div>

            </div>

        </div>


        <!-- Column Articles: Positions 4–9 -->

        <div class="row g-4 apum-animation-fadeInUp article-second-row">

            <?php foreach ($column_post_ids as $col_id): ?>

                <?php

                $post = get_post($col_id);

                setup_postdata($post);

                $author_title = get_article_authors_html(get_the_ID());

                $additional_classes = 'card-column h-100';

                $show_author = true;

                $image_size = [1280, 720];

                $heading_tag = 'h2';

                ?>

                <div class="col-12 col-lg-4">

                    <?php

                    get_template_part(
                        'template-parts/articlecard',
                        null,
                        compact(
                            'additional_classes',
                            'show_author',
                            'image_size',
                            'heading_tag'
                        )
                    );

                    ?>

                </div>

                <?php wp_reset_postdata(); ?>

            <?php endforeach; ?>

        </div>


        <?php if (empty($ordered_posts)): ?>

            <p class="no-items-content-area text-center maxw-720 lead">

                <?php
                echo esc_html(
                    t(
                        'iw_we_re_curating_something_great_articles_will_be_available_shortly'
                    )
                );
                ?>

            </p>

        <?php endif; ?>


        <?php if (!empty($ordered_posts)): ?>

            <!-- Button -->

            <div class="text-center apum-animation-fadeInUp">

                <button
                    class="btn btn-primary btn-animate"
                    rel="prefetch"
                    onclick="window.location.href='<?php echo esc_url(get_permalink(get_option('page_for_posts'))); ?>'"
                    role="button"
                >

                    <span>
                        <?php
                        echo esc_html(
                            t('iw_all_articles')
                        );
                        ?>
                    </span>

                    <i class="fas fa-arrow-right"></i>

                </button>

            </div>

        <?php endif; ?>


        <!-- Bottom Illustration -->

        <link
            rel="preload"
            as="image"
            href="/wp-content/uploads/2025/11/hm-illustration-one-1.svg"
            type="image/svg+xml"
        >

        <img
            src="/wp-content/uploads/2025/11/hm-illustration-one-1.svg"
            class="hm-illustration d-block ms-auto"
            alt="hm-illustration-one"
        >

    </div>

</section>


