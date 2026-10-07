<?php
/**
 * Template Name: Post Category Archive
 * Description: Template for displaying regular post categories
 */

get_header();

// Get selected categories from URL
$selected_categories = isset($_GET['category']) ? explode(',', sanitize_text_field($_GET['category'])) : array();
$search_query = isset($_GET['search_query']) ? sanitize_text_field($_GET['search_query']) : '';
$paged = get_query_var('paged') ? get_query_var('paged') : 1;
?>

<?php get_template_part('template-parts/main-wrapper-start'); ?>

            <div class="pb-4">
            <div class="d-flex flex-column justify-items-center title-area add-motif mb-4">
                <div class="print-no"><?php custom_breadcrumb(); ?></div>
                <h1><?php single_cat_title(); ?></h1>
                   <h2 class="sr-only d-none" >At Right Angles, A Resource for school Mathematics</h2>
<?php if (category_description()) : ?>
<div class="category-description maxw-720 ms-0">
    <?php echo iw_trim_content( category_description() ); ?>
</div>
<?php endif; ?>
            </div>

            <!-- Search & Filters -->
            <?php if (false) : ?>
    <?php get_template_part('template-parts/search-and-filter'); ?>
<?php endif; ?>
            <div class="apum-animation-fadeInUp">
                <?php
                // Build the query args
                $args = array(
                    'post_type'      => 'post',
                    'posts_per_page' => get_option('posts_per_page'),
                    'paged'          => $paged,
                    'category_name' => get_queried_object()->slug, // Current category
                );

                // Add search
                if (!empty($search_query)) {
                    $args['s'] = $search_query;
                    $args['relevanssi'] = true;
                }

                // Add additional category filters if selected
                if (!empty($selected_categories)) {
                    $args['category_name'] .= ',' . implode(',', $selected_categories);
                }

                // Custom Query
                $custom_query = new WP_Query($args);
                ?>

                <?php if ($custom_query->have_posts()) : ?>
                    <div class="d-grid grid-col-3">
                        <?php while ($custom_query->have_posts()) : $custom_query->the_post(); ?>
                            <?php
                            $author_title = get_field('author_title');
                            $additional_classes = 'card-column h-100';
                            $show_author = true;
                            $image_size = [1280, 720];
                            get_template_part('template-parts/articlecard', null, compact('author_title', 'additional_classes', 'show_author', 'image_size'));
                            ?>
                        <?php endwhile; ?>
                    </div>

                    <!-- Pagination -->
                    <nav class="pagination-container mt-5">
                        <?php
                        $big = 999999999;
                        $current_page = max(1, get_query_var('paged'));
                        $total_pages = $custom_query->max_num_pages;

                        if ($total_pages > 1) {
                            echo '<ul class="pagination">';

                            // Previous
                            if ($current_page > 1) {
                                echo '<li class="prev"><a href="' . esc_url(get_pagenum_link($current_page - 1)) . '"><i class="fas fa-arrow-left"></i><span class="btn-text">' . esc_html( t('iw_previous') ) . '</span></a></li>';
                            } else {
                                echo '<li class="prev disabled"><span><i class="fas fa-arrow-left"></i> <span class="btn-text">' . esc_html( t('iw_previous') ) . '</span></span></li>';
                            }

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
                                echo '<li class="next"><a href="' . esc_url(get_pagenum_link($current_page + 1)) . '"><span class="btn-text">' . esc_html( t('iw_next') ) . '</span> <i class="fas fa-arrow-right"></i></a></li>';
                            } else {
                                echo '<li class="next disabled"><span class="btn-text">' . esc_html( t('iw_next') ) . '</span><i class="fas fa-arrow-right"></i></li>';
                            }

                            echo '</ul>';
                        }
                        ?>
                    </nav>

                <?php else : ?>
				 <div class="text-center">
					    <h4><?php echo esc_html( t('iw_no_articles_found_in_this_category') ); ?></h4>
					 <?php get_template_part('template-parts/screenscananimation'); ?>
				<div class="text-center  mt-48-124">
    <div class="mt-3 ms-md-3">
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

                <?php wp_reset_postdata(); ?>
            </div>
            </div>
<?php get_template_part('template-parts/main-wrapper-end'); ?>
        

<!-- Info card section -->
<?php get_template_part('template-parts/section', 'info-cards'); ?>
<?php get_footer(); ?>
