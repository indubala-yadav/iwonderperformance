<?php
/*
 magazine issue component section
*/
?>
<section class="apum-section px-3 position-relative pt-5 pb-5">
    <div class="container apum-animation-fadeInUp gap-24-48">

        <div class="title-area add-motif mb-5 apum-animation">
            <h2 class="section-title fs-24-40">
                <?php echo __( t('iw_magazine_issues') ); ?>
            </h2>
        </div>

        <?php
        $args = array(
            'post_type'      => 'magazine_issue',
            'posts_per_page' => 4,
            'meta_query'     => array(
                'relation' => 'OR',
                array(
                     'key'     => 'old_magazine_issue',
                     'compare' => 'NOT EXISTS',
                ),
                array(
                    'key'     => 'old_magazine_issue',
                    'value'   => 'yes',
                    'compare' => 'NOT LIKE',
                ),
            ),
        );

        $home_posts = new WP_Query($args);

        if ($home_posts->have_posts()) : ?>

            <!-- Carousel Wrapper -->
            <div class="apum-carousel d-grid grid-col-4 gap-4 responsive-carousel">

                <?php while ($home_posts->have_posts()) : $home_posts->the_post(); ?>
                    <div class="carousel-item-wrapper">
                        <a class="card issuecard p-0 pb-0 pb-lg-4 justify-content-between h-100" href="<?php the_permalink(); ?>">

                            <?php if (has_post_thumbnail()) : 
    the_post_thumbnail(
        'full', // Use full-size image
        [
            'class' => 'img-fluid', // Bootstrap responsive class
            'alt'   => esc_attr(get_the_title())
        ]
    );
else : ?>
    <img src="https://placehold.co/282x374/cccccc/1f4d7c?text=magazine+issue"
         class="img-fluid"
         alt="<?php echo esc_attr(get_the_title()); ?>">
<?php endif; ?>

                            <h3 class="text-truncation mb-1 mt-3 "><?php the_title(); ?></h3>
                        </a>
                    </div>
                <?php endwhile; ?>

            </div><!-- /.apum-carousel -->

            <div class="text-center mt-4 mt-lg-5">
                <button class="btn btn-primary btn-animate" onclick="window.location.href='<?php echo get_post_type_archive_link('magazine_issue'); ?>'" role="button">
                    <span><?php echo __( t('iw_all_magazines') ); ?></span> <i class="fas fa-arrow-right"></i>
                </button>
            </div>

        <?php else : ?>

            <!-- ❗ CORRECT: No-items message OUTSIDE the carousel -->
            <p class="no-items-content-area text-center maxw-720 lead">
                <?php echo esc_html(t('iw_we_re_curating_something_great_magazines_will_be_available_shortly')); ?>
            </p>

        <?php endif;

        wp_reset_postdata(); ?>

        <!-- SVG LCP preload + image -->
        <link rel="preload" as="image" href="/wp-content/uploads/2025/11/hm-illustration-three-1.svg" type="image/svg+xml">
        <img src="/wp-content/uploads/2025/11/hm-illustration-three-1.svg"
             class="hm-illustration mt-4 mt-lg-5 d-block ms-auto"
             alt="hm-illustration-three" fetchpriority="high">

    </div><!-- container -->
</section>
