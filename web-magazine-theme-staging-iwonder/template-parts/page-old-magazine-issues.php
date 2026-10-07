<?php
/* Template Name: Old Magazine Issue */

get_header();

$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

$args = [
    'post_type' => 'magazine_issue',
    'posts_per_page' => 12,
    'paged' => $paged,
    'meta_query' => [
        [
            'key' => 'old_magazine_issue',
            'value' => '"yes"',
            'compare' => 'LIKE'
        ]
    ]
];

if(isset($_REQUEST['search_query']) && $_REQUEST['search_query'] != '') {
    $args['s'] = $_REQUEST['search_query'];
    $args['relevanssi'] = true;
}

$old_issues = new WP_Query($args);
?>

<?php get_template_part('template-parts/main-wrapper-start'); ?>

  <div class="px-3 px-md-0 pb-4">
            <!-- Breadcrumb -->
            <div class="title-area add-motif mb-0">
                <?php custom_breadcrumb(); ?>
				  <!-- Title -->
   <h1 class="mb-5"><?php echo esc_html( t('iw_past_magazine_issues') ); ?></h1>

            </div>

          
            
            <!-- Search & Filters -->
            <?php get_template_part('template-parts/search-and-filter'); ?>
            
            <div class="apum-animation-fadeInUp">
                <?php if ($old_issues->have_posts()) : ?>
                    <div class="d-grid grid-col-4">
                        <?php while ($old_issues->have_posts()) : $old_issues->the_post(); ?>
                            <?php
                                $external_link = get_field('old_magazine_link');
                                $link_url = $external_link ?: get_permalink();
                                $link_target = $external_link ? '_blank' : '_self';
                            ?>
                            <a href="<?php echo esc_url($link_url); ?>" target="<?php echo esc_attr($link_target); ?>" id="post-<?php the_ID(); ?>" class="card issuecard p-0 pb-4">
                                <?php if (has_post_thumbnail()) : ?>
                                    <?php the_post_thumbnail('medium', ['class' => 'img-fluid w-100', 'alt' => get_the_title()]); ?>
                                <?php else : ?>
                                    <img src="https://placehold.co/600x400?text=<?php echo esc_html( t('iw_placeholder') ); ?>" class="img-fluid w-100" alt="<?php echo esc_html( t('iw_placeholder_image') ); ?>">
                                <?php endif; ?>

                                <p class="text-muted mb-1 mt-3 fs-14"><span class="">
                                    <?php
                                    $terms = get_the_terms(get_the_ID(), 'magazine_category');
                                    if (!empty($terms) && !is_wp_error($terms)) {
                                        echo esc_html($terms[0]->name);
                                    }
                                    ?>
                                </span></p>
                                <h2 class="text-truncation line-2 mb-0 fs-16"><?php the_title(); ?></h2>
                            </a>
                        <?php endwhile; ?>
                    </div>

                    <!-- Pagination -->
                    <nav class="pagination-container mt-5 apum-animation-fadeInUp">
                        <?php
                        $big = 999999999;
                        $current_page = max(1, get_query_var('paged'));
                        $total_pages = $old_issues->max_num_pages;

                        if ($total_pages > 1) {
                            echo '<ul class="pagination">';

                            // Previous Button
                            if ($current_page > 1) {
                                echo '<li class="prev"><a href="' . esc_url(get_pagenum_link($current_page - 1)) . '"><i class="fas fa-arrow-left"></i><span class="btn-text">' . esc_html( t('iw_previous') ) . '</span></a></li>';
                            } else {
                                echo '<li class="prev disabled"><span><i class="fas fa-arrow-left"></i> <span class="btn-text">' . esc_html( t('iw_previous') ) . '</span></span></li>';
                            }

                            // Pagination Links
                            $pagination_links = paginate_links([
                                'base'      => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
                                'format'    => '?paged=%#%',
                                'current'   => $current_page,
                                'total'     => $total_pages,
                                'mid_size'  => 1,
                                'end_size'  => 1,
                                'type'      => 'array',
                                'prev_next' => false,
                            ]);

                            if (!empty($pagination_links)) {
                                foreach ($pagination_links as $link) {
                                    echo '<li>' . $link . '</li>';
                                }
                            }

                            // Next Button
                            if ($current_page < $total_pages) {
                                echo '<li class="next"><a href="' . esc_url(get_pagenum_link($current_page + 1)) . '"><span class="btn-text">' . esc_html( t('iw_next') ) . '</span> <i class="fas fa-arrow-right"></i></a></li>';
                            } else {
                                echo '<li class="next disabled"><span class="btn-text">' . esc_html( t('iw_next') ) . '</span><i class="fas fa-arrow-right"></i></li>';
                            }

                            echo '</ul>';
                        }
                        ?>
                    </nav>
                <?php else : ?>
<!--                     <p><?php echo esc_html( t('iw_no_past_magazine_issues_found') ); ?></p> -->
				 <div class="text-center">
				<h4><?php echo esc_html( t('iw_no_past_magazine_issues_found') ); ?></h4>

				
					<?php get_template_part('template-parts/screenscananimation'); ?>
					
				<div class="text-center  mt-48-124">
    <div class="mt-5 ms-md-3">
<?php
// Get current URL without 'search_query'
$current_url = home_url( add_query_arg( null, null ) );
$clean_url = remove_query_arg( 'search_query', $current_url );
?>
<a class="btn btn-primary" href="<?php echo esc_url($clean_url); ?>" role="button">
           <i class="fa-solid fa-arrow-rotate-right me-1"></i><span><?php echo esc_html( t('iw_reset') ); ?>
 </span>
        </a>
    </div>
</div>
					</div>
                <?php endif; ?>
            </div>

            <?php wp_reset_postdata(); ?>
            
            <?php $has_search_query = isset($_REQUEST['search_query']) && !empty($_REQUEST['search_query']); ?>
            <?php if ($has_search_query) : ?>
<!--                 <div class="text-center mt-5">
                    <div class="mt-3 ms-md-3">
                        <a class="btn btn-primary btn-animate" href="<?php echo esc_url(home_url('/old-magazine-issues/')); ?>" role="button">
                            <i class="fas fa-arrow-left"></i> <?php echo esc_html( t('iw_go_back') ); ?>
                        </a>
                    </div>
                </div> -->
            <?php endif; ?>
</div>
<?php
get_template_part('template-parts/main-wrapper-end');
?>

<!-- Info card section -->
<?php get_template_part('template-parts/section', 'info-cards'); ?>

<?php get_footer(); ?>