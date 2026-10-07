<?php get_header(); ?>

<section class="container-fluid p-0 pb-5 position-relative apum-innerpage-section">
  <div class="theme-top-ct apply-top-pattern apply-bottom-pattern pattern-contrast i-wonder-theme-top-ct"></div>

  <div class="apum-section position-relative section-pad pt-5 pt-lg-4 pb-200">
    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
      <div class="container bg-white pt-80 pb-0 rounded" >
        <div class="maxw-720">

          <!-- Breadcrumb -->
          <div class="title-area add-motif mb-0">
            <?php custom_breadcrumb(); ?>
            <h1 class="single-magzine-title"><?php the_title(); ?></h1>
          </div>

          <div class="d-flex flex-column flex-md-row gap-24-40">

            <!-- Thumbnail -->
            <div class="col-md-3 mb-0">
              <?php the_post_thumbnail('medium', ['class' => 'img-fluid']); ?>
            </div>

            <!-- Issue Note -->
            <div class="d-flex flex-column-reverse flex-md-column">

              <div class="magzine-issue-shortnote hide-sharedaddy">
                <?php echo get_field('magazine_issues_short_note'); ?>

                <?php 
                $feedback_url = get_field('feed_back_form_url');

                if ( $feedback_url ) : ?>
                  <p>
                    <?php echo esc_html( t('iw_magazine_feedback') ); ?>
                    <a href="<?php echo esc_url($feedback_url); ?>" class="feedback-btn" target="_blank" rel="noopener">
                      <?php echo esc_html( t('iw_here'));?>
                    </a>
                    <?php echo esc_html( t('iw_Or_write_to_us_at') ); ?>
                    <a href="mailto:iwonder@apu.edu.in" target="_blank" rel="noopener">
                      iwonder@apu.edu.in.
                    </a>
                  </p>
                <?php endif; ?>
              </div>

              <?php get_template_part('template-parts/share-download'); ?>
            </div>
          </div>

          <!-- Tabs -->
          <ul class="nav nav-pills position-relative tabs" id="magazineTabs" role="tablist">
            <li class="nav-item">
              <button class="nav-link active" id="editorial-tab" data-bs-toggle="tab" data-bs-target="#editorial" type="button" role="tab">
                <?php echo esc_html( t('iw_editorial') ); ?>
              </button>
            </li>
            <li class="nav-item">
              <button class="nav-link" id="toc-tab" data-bs-toggle="tab" data-bs-target="#toc" type="button" role="tab">
                <?php echo esc_html( t('iw_table_of_content') ); ?>
              </button>
            </li>
            <span class="glider position-absolute bg-primary"></span>
          </ul>

          <div class="tab-content" id="magazineTabsContent">

            <!-- EDITORIAL -->
            <div class="tab-pane fade show active" id="editorial" role="tabpanel">
              <div>
                <h2><?php echo esc_html( t('iw_from_the_editors_desk') ); ?></h2>
                <?php the_content(); ?>
              </div>
            </div>

            <!-- TOC -->

<div class="tab-pane fade" id="toc" role="tabpanel" aria-labelledby="toc-tab">

<?php
$old_issue_checked = get_field('old_magazine_issue');

if ($old_issue_checked && in_array('yes', $old_issue_checked)) :

    echo get_field('old_magazine_table_of_content');

else :

    $magazine_terms = get_the_terms(get_the_ID(), 'magazine_category');

    if ($magazine_terms && !is_wp_error($magazine_terms)) :

        $magazine_term_ids = wp_list_pluck($magazine_terms, 'term_id');

        /* =========================
           POSTS QUERY
        ========================= */
        $post_args = [
            'post_type' => 'post',
            'posts_per_page' => -1,
            'tax_query' => [[
                'taxonomy' => 'magazine_category',
                'field' => 'term_id',
                'terms' => $magazine_term_ids,
            ]],
            'orderby' => 'title',
            'order' => 'ASC',
        ];

        $post_query = new WP_Query($post_args);
        $post_category_posts = [];

        if ($post_query->have_posts()) {
            while ($post_query->have_posts()) {
                $post_query->the_post();

                $categories = get_the_category();

                foreach ($categories as $cat) {

                    if (!isset($post_category_posts[$cat->term_id])) {
                        $post_category_posts[$cat->term_id] = [
                            'category' => $cat,
                            'posts' => [],
                        ];
                    }

                    $post_category_posts[$cat->term_id]['posts'][] = get_post();
                }
            }
            wp_reset_postdata();
        }

        foreach ($post_category_posts as &$category_posts) {
            usort($category_posts['posts'], function ($a, $b) {
                return strcmp($a->post_title, $b->post_title);
            });
        }
        unset($category_posts);

        /* =========================
           POSTS CATEGORY SORT
        ========================= */
        uasort($post_category_posts, function ($a, $b) {

            $order_map = [
                'experiences'    => 0,
                'explorations'   => 1,
                'perspectives'   => 2,
                'extensions'     => 3,
                'readers-voices' => 4,
            ];

            $slugA = $a['category']->slug;
            $slugB = $b['category']->slug;

            $acfA = get_field('cat_order', 'magazine_category_' . $a['category']->term_id);
            $acfB = get_field('cat_order', 'magazine_category_' . $b['category']->term_id);

            $valA = ($acfA !== '' && $acfA !== null) ? (int) $acfA : ($order_map[$slugA] ?? 9999);
            $valB = ($acfB !== '' && $acfB !== null) ? (int) $acfB : ($order_map[$slugB] ?? 9999);

            return $valA <=> $valB;
        });


        /* =========================
           RESOURCES QUERY
        ========================= */
        $resource_args = [
            'post_type' => 'resource',
            'posts_per_page' => -1,
            'tax_query' => [[
                'taxonomy' => 'magazine_category',
                'field' => 'term_id',
                'terms' => $magazine_term_ids,
            ]],
            'orderby' => 'title',
            'order' => 'ASC',
        ];

        $resource_query = new WP_Query($resource_args);
        $resource_category_posts = [];

        if ($resource_query->have_posts()) {
            while ($resource_query->have_posts()) {
                $resource_query->the_post();

                $resource_terms = get_the_terms(get_the_ID(), 'resource_categories');

                if ($resource_terms) {
                    foreach ($resource_terms as $res_cat) {

                        if (!isset($resource_category_posts[$res_cat->term_id])) {
                            $resource_category_posts[$res_cat->term_id] = [
                                'category' => $res_cat,
                                'posts' => [],
                            ];
                        }

                        $resource_category_posts[$res_cat->term_id]['posts'][] = get_post();
                    }
                }
            }
            wp_reset_postdata();
        }

        foreach ($resource_category_posts as &$category_posts) {
            usort($category_posts['posts'], function ($a, $b) {
                return strcmp($a->post_title, $b->post_title);
            });
        }
        unset($category_posts);

        /* =========================
           RESOURCE CATEGORY SORT
        ========================= */
        uasort($resource_category_posts, function ($a, $b) {

            $order_map = [
                'classroom' => 0,
                'staffroom' => 1,
            ];

            $slugA = $a['category']->slug;
            $slugB = $b['category']->slug;

            $acfA = get_field('cat_order', 'resource_categories_' . $a['category']->term_id);
            $acfB = get_field('cat_order', 'resource_categories_' . $b['category']->term_id);

            $valA = ($acfA !== '' && $acfA !== null) ? (int) $acfA : ($order_map[$slugA] ?? 9999);
            $valB = ($acfB !== '' && $acfB !== null) ? (int) $acfB : ($order_map[$slugB] ?? 9999);

            return $valA <=> $valB;
        });


        /* =========================
           RENDER FUNCTION
        ========================= */
        function display_posts_grouped($grouped_posts, $placeholder_url = 'https://placehold.co/100x100?text=placeholder')
        {
            foreach ($grouped_posts as $cat_data) :

                $cat = $cat_data['category'];

                echo '<div class="d-flex flex-column gap-4 mb-5">';
                echo '<h2 class="mb-1">' . esc_html($cat->name) . '</h2>';

                foreach ($cat_data['posts'] as $post) :

                    setup_postdata($post);

                    $external_url = '';
                    $is_resource = (get_post_type($post->ID) === 'resource');

                    if ($is_resource) {
                        $external_url = get_field('external_url', $post->ID);
                    }
                    ?>

                    <article class="card flex-row">

                        <a class="h-100 ratio ratio-1x1"
                           href="<?php echo $is_resource && $external_url ? esc_url($external_url) : get_permalink($post->ID); ?>">

                            <?php
                            if (has_post_thumbnail($post->ID)) {
                                echo get_the_post_thumbnail($post->ID, [140,124], ['class'=>'img-fluid']);
                            } else {
                                echo '<img src="'.$placeholder_url.'" class="img-fluid">';
                            }
                            ?>

                        </a>

                        <div class="card-body d-flex flex-column gap-8 py-3 px-3 justify-content-center">

                            <h3 class="card-title mb-0">
                                <a href="<?php echo $is_resource && $external_url ? esc_url($external_url) : get_permalink($post->ID); ?>">
                                    <?php echo get_the_title($post->ID); ?>
                                </a>
                            </h3>

                            <div class="text-muted fs-14">
                                <span><?php echo get_the_date('', $post); ?></span>
                            </div>

                        </div>

                    </article>

                    <?php

                endforeach;

                wp_reset_postdata();

                echo '</div>';

            endforeach;
        }

        display_posts_grouped($post_category_posts);
        display_posts_grouped($resource_category_posts);

    endif;

endif;
?>

</div>
          </div>

        </div>
      </div>
    <?php endwhile; endif; ?>
  </div>
</section>

<?php get_template_part('template-parts/section', 'info-cards'); ?>

<?php get_footer(); ?>