<?php
/*
 Resource Component Section – Slick Carousel Version
*/
?>
<section class="apum-section px-3 resources-section position-relative pt-5 pb-0">
    <div class="container mgz-container">
        <div class="title-area add-motif mb-5 apum-animation-fadeInUp">
            <h2 class="section-title fs-24-40"><?php echo esc_html(t('iw_resources')); ?></h2>
        </div>

        <?php
        /*
         * STEP 1 → Fetch posts WITH valid post_order
         */
        $ordered_query = new WP_Query([
            'post_type'      => 'resource',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'meta_query'     => [
                [
                    'key'     => 'post_order',
                    'value'   => '---Select----',
                    'compare' => '!=',
                ],
            ],
        ]);

        $ordered_posts = $ordered_query->posts;

        /*
         * STEP 2 → Apply custom ordering (one → nine)
         */
        if (!empty($ordered_posts)) {
            $order_map = [
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

            usort($ordered_posts, function($a, $b) use ($order_map) {
                $a_value = $order_map[get_post_meta($a->ID, 'post_order', true)] ?? 999;
                $b_value = $order_map[get_post_meta($b->ID, 'post_order', true)] ?? 999;
                return $a_value - $b_value;
            });
        }

        /*
         * STEP 3 → Always show 6 posts total
         */
        $max_posts = 6;
        $needed = $max_posts - count($ordered_posts);
        $extra_posts = [];

        if ($needed > 0) {
            $extra_query = new WP_Query([
                'post_type'      => 'resource',
                'post_status'    => 'publish',
                'posts_per_page' => $needed,
                'post__not_in'   => wp_list_pluck($ordered_posts, 'ID'),
                'orderby'        => 'date',
                'order'          => 'DESC',
            ]);

            $extra_posts = $extra_query->posts;
        }

        /*
         * STEP 4 → Final list for output
         */
        $final_posts = array_merge($ordered_posts, $extra_posts);
        ?>

        <?php if (!empty($final_posts)) : ?>
            <div class="related-posts-slider common-slick">
                <?php foreach ($final_posts as $post) : setup_postdata($post); ?>

                    <?php
                    $image_size = [1280, 720];
                    $terms = get_the_terms($post->ID, 'resource_categories');
                    $resource_term = !empty($terms) && !is_wp_error($terms) ? $terms[0] : null;
                    $external_url = get_field('external_url', $post->ID);

                    $additional_classes = 'card-column apum-animation-fadeInUp';
                    $heading_tag = 'h3';
                    $heading_class  = 'line-2';
                    $show_author = true;

                    get_template_part('template-parts/articlecard', null, compact(
                        'additional_classes',
                        'show_author',
                        'image_size',
                        'heading_tag',
                        'resource_term',
                        'external_url',
                        'heading_class'
                    ));
                    ?>

                <?php endforeach; wp_reset_postdata(); ?>
            </div>

            <div class="text-center mt-2 mt-lg-5">
                <?php
                $archive_url = get_post_type_archive_link('resource');
                $translated_url = apply_filters('wpml_permalink', $archive_url, apply_filters('wpml_current_language', null));
                ?>
                <button class="btn btn-primary btn-animate" onclick="window.location.href='<?php echo esc_url($translated_url); ?>'">
                    <span><?php echo esc_html(t('iw_all_resources')); ?></span>
                    <i class="fas fa-arrow-right"></i>
                </button>
            </div>

        <?php else : ?>
            <p class="no-items-content-area text-center maxw-720 lead">
                <?php echo esc_html(t('iw_we_re_curating_something_great_resources_will_be_available_shortly')); ?>
            </p>
        <?php endif; ?>

        <link rel="preload" as="image" href="/wp-content/uploads/2025/11/hm-illustration-two-1.svg" type="image/svg+xml">
        <img src="/wp-content/uploads/2025/11/hm-illustration-two-1.svg" class="hm-illustration d-block ms-auto mt-5" alt="hm-illustration-two"/>
    </div>
</section>


