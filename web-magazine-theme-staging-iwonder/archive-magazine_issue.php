<?php get_header(); ?>

<?php get_template_part('template-parts/main-wrapper-start'); ?>

              <div class="pb-4">
            <div class="page-header title-area add-motif mb-5">
                <div class="print-no"><?php custom_breadcrumb(); ?></div>
                <h1><?php echo esc_html( t('iw_magazine_issues') ); ?></h1>
                <div class="category-description maxw-720 ms-0 fs-18"> 
                    
                    <?php echo iw_trim_content(t('iw_magazineissues_description') ); ?>

</div>
                <!-- <?php if (is_category() || is_tag()) : ?>
                    <div class="archive-description">
                    
                    </div>
                <?php endif; ?> -->
            </div>

            <!-- Search & Filters -->
            <?php if (false) : ?>
                <?php get_template_part('template-parts/search-and-filter'); ?>
            <?php endif; ?>
            
            <div class="apum-animation-fadeInUp">
                <?php
                $paged = max(1, get_query_var('paged'));
                $args = [
                    'post_type'      => 'magazine_issue',
                    'paged'          => $paged,
                    'posts_per_page' => get_option('posts_per_page'),
                    'meta_query'     => [
                        'relation' => 'OR',
                        [
                            'key'     => 'old_magazine_issue',
                            'compare' => 'NOT EXISTS',
                        ],
                        [
                            'key'     => 'old_magazine_issue',
                            'value'   => 'yes',
                            'compare' => 'NOT LIKE',
                        ]
                    ]
                ];

                if (isset($_REQUEST['search_query']) && !empty($_REQUEST['search_query'])) {
                    $args['s'] = sanitize_text_field($_REQUEST['search_query']);
                    $args['relevanssi'] = true;
                }

                $custom_query = new WP_Query($args);
                ?>

                <?php if ($custom_query->have_posts()) : ?>
                    <div class="d-grid grid-col-4">
                        <?php while ($custom_query->have_posts()) : $custom_query->the_post(); ?>
                           <a href="<?php the_permalink(); ?>" 
   id="post-<?php the_ID(); ?>" 
   class="card issuecard p-0 pb-4 justify-content-between h-100" 
   aria-label="<?php echo esc_attr(sprintf(t('iw_view_magazine_issue'), get_the_title())); ?>">
                                <?php if (has_post_thumbnail()) : ?>
                                    <?php the_post_thumbnail('medium', [
                                        'class' => 'img-fluid w-100 h-100', 
                                        'alt'   => esc_attr(get_the_title())
                                    ]); ?>
                                <?php else : ?>
                                   <img src="<?php echo esc_url('https://placehold.co/600x400?text=' . t('iw_magazine')); ?>" 
     class="img-fluid w-100 h-100" 
     alt="<?php echo esc_html(t('iw_placeholder_image')); ?>">

                                <?php endif; ?>
                                
                                <!-- <p class="text-muted mb-1 mt-3 fs-14">
                                    <span>
                                    <?php
                                        $terms = get_the_terms(get_the_ID(), 'magazine_category');
                                        if (!empty($terms) && !is_wp_error($terms)) {
                                            echo esc_html($terms[0]->name);
                                        }
                                    ?>
                                    </span>
                                </p> -->
                                <h2 class="text-truncation line-2 mb-1 mt-3"><?php the_title(); ?></h2>
                            </a>
                        <?php endwhile; ?>
                    </div>

                    <!-- Pagination -->
<!--                     <nav class="pagination-container mt-5 apum-animation-fadeInUp" aria-label="<?php echo esc_html( t('iw_magazine_issues_pagination') ); ?>">
                        <?php
                        echo paginate_links([
                            'base'      => str_replace(999999999, '%#%', esc_url(get_pagenum_link(999999999))),
                            'format'    => '?paged=%#%',
                            'total'     => $custom_query->max_num_pages,
                            'current'   => $paged,
                            'prev_text' => '<i class="fas fa-arrow-left" aria-hidden="true"></i> ' . esc_html( t('iw_previous') ),
                            'next_text' => esc_html( t('iw_next') ). ' <i class="fas fa-arrow-right" aria-hidden="true"></i>',
                            'mid_size'  => 1,
                            'end_size'  => 1,
                        ]);
                        ?>
                    </nav> -->
                <?php else : ?>
<!--                     <h4 class="text-center"><?php echo esc_html( t('iw_no_magazine_issues_found') ); ?></h4> -->
				 <div class="text-center">
					    <h4><?php echo esc_html( t('iw_no_magazine_issues_found') ); ?></h4>
					 	<?php get_template_part('template-parts/screenscananimation'); ?>
				<div class="text-center mt-48-124">
    <div class="mt-3 ms-md-3">
<?php
// Get current URL without 'search_query'
$current_url = home_url( add_query_arg( null, null ) );
$clean_url = remove_query_arg( 'search_query', $current_url );
?>
<button class="btn btn-primary" onclick="window.location.href='<?php echo esc_url($clean_url); ?>'" role="button">
           <i class="fa-solid fa-arrow-rotate-right me-1"></i><span><?php echo esc_html( t('iw_reset') ); ?></span>
                </button>
    </div>
</div>
					 
					</div>
                <?php endif; ?>
                
                <?php wp_reset_postdata(); ?>
                
                <?php $has_search_query = isset($_REQUEST['search_query']) && !empty($_REQUEST['search_query']); ?>
                
                <div class="text-center mt-5">
                    <?php if ($has_search_query) : ?>
<!--                         <a class="btn btn-primary btn-animate" href="<?php echo esc_url(get_post_type_archive_link('magazine_issue')); ?>" role="button">
                            <i class="fas fa-arrow-left" aria-hidden="true"></i>  
                            <?php echo esc_html( t('iw_go_back') ); ?>
                        </a> -->
                    <?php else : ?>
                        <button class="btn btn-primary btn-animate old-magzine-btn d-none" onclick="window.location.href='<?php echo esc_url(home_url('/old-magazine-issues/')); ?>'" role="button">
                            <span><?php echo esc_html( t('iw_old_magazine_issues') ); ?></span> 
                            <i class="fas fa-arrow-right" aria-hidden="true"></i>
                    </button>
                    <?php endif; ?>
                </div>
            </div>
</div>
<?php
get_template_part('template-parts/main-wrapper-end');
?>
<!-- Info card section -->
<?php get_template_part('template-parts/section', 'info-cards'); ?>

<?php get_footer(); ?>