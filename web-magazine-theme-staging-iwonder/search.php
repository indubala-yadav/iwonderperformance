<?php get_header(); ?>

<?php
$search_query = get_search_query();
$post_types = array('post', 'resource', 'magazine_issue', 'authors_bio', 'page', 'editorial_team', 'announcement');

$args = array(
    's' => $search_query,
    'post_type' => $post_types,
    'posts_per_page' => -1,
);

$search_query_obj = new WP_Query($args);
if (function_exists('relevanssi_do_query')) {
    relevanssi_do_query($search_query_obj);
}

$total_results = 0;
$organized_posts = array(
    'post' => array(),
    'resource' => array(),
    'magazine_issue' => array(),
    'authors_bio' => array(),
    'page' => array(),
    'editorial_team' => array(),
    'announcement' => array()
);

$uncategorized_id = get_cat_ID('Uncategorized');

if ($search_query_obj->have_posts()) {
    while ($search_query_obj->have_posts()) {
        $search_query_obj->the_post();
        $post_type = get_post_type();

        if ($post_type === 'post') {
            $categories = wp_get_post_categories(get_the_ID());
            if (count($categories) === 1 && in_array($uncategorized_id, $categories)) {
                continue;
            }
        }

        $organized_posts[$post_type][] = $post;
        $total_results++;
    }
}


?>

<div class="container-fluid p-0 position-relative apum-innerpage-section">
    <div class="theme-top-ct apply-top-pattern apply-bottom-pattern pattern-contrast i-wonder-theme-top-ct"></div>

    <main id="primary" class="apum-section position-relative section-pad pt-4">
        <?php if ($total_results > 0) : ?>
            <article class="container bg-white pt-80 pb-0 rounded">
                <div class="maxw-720 content-area">
                    <div class="title-area add-motif my-4">
                        <h1><?php echo esc_html(t('iw_search_results')); ?></h1>
                    </div>
                    <form role="search" method="get" class="search-form" action="<?php echo esc_url(home_url('/')); ?>">
                        <div class="input-group search-result-form">
                            <input type="search" class="form-control" placeholder="<?php echo esc_attr(t('iw_search_for')); ?>" value="<?php echo esc_attr(get_search_query()); ?>" name="s" required>
                            <button type="submit" class="btn"><i class="fas fa-search"></i></button>
                        </div>
                    </form>

                    <p class="search-results-count mt-2">
                        <?php
                        echo sprintf(
                            esc_html__('Found %1$s results for "%2$s"', 'web-magazine-theme'),
                            '<strong>' . esc_html($total_results) . '</strong>',
                            '<strong>' . esc_html(get_search_query()) . '</strong>'
                        );
                        ?>
                    </p>

                    <div class="d-flex flex-column">
                        <?php foreach ($organized_posts as $post_type => $posts) :
                            if (!empty($posts)) :
                                $section_title = '';
                                switch ($post_type) {
                                    case 'post': $section_title = esc_html(t('iw_articles')); break;
                                    case 'resource': $section_title = esc_html(t('iw_resources')); break;
                                    case 'magazine_issue': $section_title = esc_html(t('iw_magazine_issues')); break;
                                    case 'authors_bio': $section_title = esc_html(t('iw_authors')); break;
                                    case 'page': $section_title = esc_html(t('iw_pages')); break;
                                    case 'editorial_team': $section_title = esc_html(t('iw_editorial_team')); break;
                                    case 'announcement': $section_title = esc_html(t('iw_announcements')); break;
                                }

                                $hidden_count = count($posts) - 2;
                                ?>
                                <h2 class="section-title fs-24-40 title-area add-motif my-5"><?php echo esc_html($section_title); ?></h2>
                                <div class="post-type-container" data-post-type="<?php echo esc_attr($post_type); ?>">
                                    <?php
                                    $post_count = 0;
                                    foreach ($posts as $post) :
                                        setup_postdata($post);
                                        $post_count++;
                                        $hidden_class = ($post_count > 2) ? 'hidden-post' : '';
                                        $old_issue = ($post_type == 'magazine_issue') ? get_field('old_magazine_issue', $post->ID) : false;
                                        $external_url = ($post_type == 'resource') ? get_field('external_url', $post->ID) : '';
                                        $announcement_url = ($post_type == 'announcement') ? get_field('announcement_url', $post->ID) : false;
                                        $magazine_categories = ($post_type == 'magazine_issue') ? get_the_terms($post->ID, 'magazine_category') : false;
									
							

$category_class = '';

$post_id   = get_the_ID();
$post_type = get_post_type($post_id);

if ($post_type === 'resource') {

    $terms = get_the_terms($post_id, 'resource_category');

    if (!empty($terms) && !is_wp_error($terms)) {

        // ACF term field
        $field_value = get_field('category__class', 'resource_category_' . $terms[0]->term_id);

        if (!empty($field_value)) {
            $category_class = 'cat-' . sanitize_html_class($field_value);
        }
    }

} else {

    $categories = get_the_category($post_id);

    if (!empty($categories)) {

        // ACF term field
        $field_value = get_field('category__class', 'category_' . $categories[0]->term_id);

        if (!empty($field_value)) {
            $category_class = 'cat-' . sanitize_html_class($field_value);
        }
    }
}

                                        ?>
                                        <article class="card flex-row align-items-center mb-4 <?php echo esc_attr($category_class); ?> <?php echo esc_attr($hidden_class); ?>" data-post-type="<?php echo esc_attr($post_type); ?>">
                                            <a href="<?php 
                                                if ($post_type == 'editorial_team') {
                                                    echo esc_url(get_permalink(get_page_by_path('about-us'))) . '#editorial-team';
                                                } elseif ($post_type == 'resource' && $external_url) {
                                                    echo esc_url($external_url);
                                                } elseif ($post_type == 'magazine_issue' && $old_issue) {
                                                    echo esc_url(get_field('old_magazine_link', $post->ID));
                                                } elseif ($post_type == 'announcement' && $announcement_url) {
                                                    echo esc_url($announcement_url['url']);
                                                } else {
                                                    echo esc_url(get_permalink());
                                                }
                                            ?>" <?php 
                                                if (($post_type == 'resource' && $external_url) || 
                                                    ($post_type == 'magazine_issue' && $old_issue) || 
                                                    ($post_type == 'announcement' && !empty($announcement_url['target']))) {
                                                    echo 'target="_blank" rel="noopener noreferrer"';
                                                }
                                            ?> class="ratio ratio-1x1">
                                                <?php if (has_post_thumbnail()) :
                                                    the_post_thumbnail('full', ['class' => 'img-fluid wp-post-image', 'alt' => esc_attr(get_the_title())]);
                                                else : ?>
                                                    <img src="https://placehold.co/150x150?text=Apum+Placeholder" alt="Default Image" class="img-fluid wp-post-image">
                                                <?php endif; ?>
                                            </a>

                                            <div class="card-body d-flex flex-column gap-8 p-3">
                                                <?php if ($post_type === 'post' || $post_type === 'resource') : 
                                                    echo get_styled_category_link();
                                                elseif ($post_type === 'magazine_issue' && !empty($magazine_categories)) : ?>
                                                    <div class="magazine-category"><?php echo esc_html($magazine_categories[0]->name); ?></div>
                                                <?php endif; ?>

                                                <h3 class="card-title text-truncation line-2">
                                                    <a href="<?php 
                                                        if ($post_type == 'editorial_team') {
                                                            echo esc_url(get_permalink(get_page_by_path('about-us'))) . '#editorial-team';
                                                        } elseif ($post_type == 'resource' && $external_url) {
                                                            echo esc_url($external_url);
                                                        } elseif ($post_type == 'magazine_issue' && $old_issue) {
                                                            echo esc_url(get_field('old_magazine_link', $post->ID));
                                                        } elseif ($post_type == 'announcement' && $announcement_url) {
                                                            echo esc_url($announcement_url['url']);
                                                        } else {
                                                            echo esc_url(get_permalink());
                                                        }
                                                    ?>" <?php 
                                                        if (($post_type == 'resource' && $external_url) || 
                                                            ($post_type == 'magazine_issue' && $old_issue) || 
                                                            ($post_type == 'announcement' && !empty($announcement_url['target']))) {
                                                            echo 'target="_blank" rel="noopener noreferrer"';
                                                        }
                                                    ?>>
                                                        <?php the_title(); ?>
                                                    </a>
                                                </h3>

                                       <?php the_excerpt();?>


                                                <?php if ($post_type === 'post') : ?>
                                                    <div class="text-muted fs-14 d-flex align-items-center gap-8 line-1 apum-article-authordetails text-nowrap text-truncation line-1">
                                                        <div class="apum-article-author d-flex mw-50 gap-1">― <?php echo display_dynamic_linked_authors($post->ID); ?></div>
<!--                                                         <span class="apum-dot">•</span> -->
<!--                                                         <span><?php the_time('d M Y'); ?></span> -->
                                                    </div>
                                                <?php elseif ($post_type === 'resource') : ?>
                                                    <div class="text-muted fs-14 d-flex align-items-center gap-8 line-1 apum-article-authordetails text-nowrap text-truncation line-1">
                                                        ― <?php echo display_dynamic_linked_authors($post->ID); ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </article>
                                    <?php endforeach; ?>
                                    <?php if ($hidden_count > 0) : ?>
                                        <div class="load-more-container mt-5">
                                            <button id="load-more-button" class="btn btn-light btn-animate load-more-btn" data-post-type="<?php echo esc_attr($post_type); ?>">
                                               <span>
                                                    <?php 
                                                    echo sprintf(
                                                        esc_html__( 'Load More %s', 'your-text-domain' ),
                                                        esc_html( $section_title )
                                                    );
                                                    ?>
                                                    </span>
                                                <i class="fas fa-arrow-right"></i>
                                            </button>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif;
                        endforeach;
                        wp_reset_postdata(); ?>
                    </div>
                </div>
            </article>

        <?php else : ?>
            <!-- No Results -->
            <article class="container apum-innerpage-body bg-white pt-80 pb-0 rounded no-result-found" style="height:calc(100vh - 30vh);">
                <div class="maxw-720 content-area d-flex flex-column gap-4">
                    <div class="title-area add-motif">
                        <h1 class="mb-0"><?php echo __( t('iw_search_results') ); ?></h1>
                    </div>
					
                    <form role="search" method="get" class="search-form" action="<?php echo esc_url(home_url('/')); ?>">
                        <div class="input-group search-result-form">
                            <input type="search" class="form-control" placeholder="<?php echo esc_attr__( t('iw_search_for') ); ?>" value="<?php echo esc_attr(get_search_query()); ?>" name="s" required>
							
                            <button type="submit" class="btn"><i class="fas fa-search"></i></button>
                        </div>
                    </form>

                    <div class="no-results-message d-flex align-items-center justify-content-center flex-column gap-3">
                        <p class="mb-0"><?php echo __( t('iw_no_results_found') ); ?></p>
                        <div class="search-animation-wrapper" style="width: 300px;">
                            <svg width="100%" height="240" viewBox="0 0 300 240" xmlns="http://www.w3.org/2000/svg">
                                <rect x="40" y="20" rx="10" ry="10" width="220" height="140" fill="var(--theme-color)" />
                                <rect x="40" y="20" rx="10" ry="10" width="220" height="25" fill="var(--theme-color)" />
                                <circle cx="55" cy="32" r="3" fill="#fff" />
                                <circle cx="65" cy="32" r="3" fill="#fff" />
                                <circle cx="75" cy="32" r="3" fill="#fff" />
                                <rect x="90" y="30" width="100" height="6" fill="#fff" rx="2" />
                                <g id="doc" transform="translate(80, 60)">
                                    <rect width="140" height="100" rx="10" ry="10" fill="#fff8e6" stroke="#ffe38c" stroke-width="2"/>
                                    <rect x="15" y="15" width="20" height="6" fill="var(--theme-color)" />
                                    <rect x="40" y="15" width="60" height="6" fill="var(--theme-color)" />
                                    <rect x="15" y="30" width="110" height="6" fill="var(--theme-color)" />
                                    <rect x="15" y="42" width="110" height="6" fill="var(--theme-color)" />
                                    <rect x="15" y="54" width="110" height="6" fill="var(--theme-color)" />
                                    <rect x="15" y="66" width="110" height="6" fill="var(--theme-color)" />
                                    <rect x="15" y="78" width="110" height="6" fill="var(--theme-color)" />
                                </g>
                            </svg>
							<img src="/wp-content/uploads/2025/08/svgviewer-png-output-e1749115337780.webp" alt="Search Icon" style="top:20%;left:0;right:0;margin:0 auto;" class="scanner-animation">
                        </div>
                    </div>
                </div>
            </article>
        <?php endif; ?>
    </main>
</div>

<?php if (have_posts()) : get_template_part('template-parts/section', 'info-cards'); endif; ?>

<script>
jQuery(document).ready(function($) {
    $('.load-more-btn').click(function() {
        var postType = $(this).data('post-type');
        var container = $(this).closest('.post-type-container');
        var buttonWrapper = $(this).parent();
        var hiddenPosts = container.find('article.hidden-post[data-post-type="' + postType + '"]');

        hiddenPosts.each(function(index) {
            var $post = $(this);
            setTimeout(function() {
                $post.removeClass('hidden-post').addClass('fadeInUp');
            }, 100 * index);
        });

        setTimeout(function() {
            buttonWrapper.fadeOut(10);
        }, 100 * hiddenPosts.length + 200);
    });
});
</script>

<?php get_footer(); ?>
