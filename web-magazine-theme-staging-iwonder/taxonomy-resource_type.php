<?php
/**
 * Template for displaying resources by category
 */

get_header();

// Get the current term
$term = get_queried_object();
?>

<?php

get_template_part('template-parts/main-wrapper-start');

?>

             <div class="pb-4">
            <div class="d-flex flex-column justify-items-center title-area add-motif mb-4">
                <div class="print-no"><?php custom_breadcrumb(); ?></div>
                <h1><?php echo esc_html($term->name); ?></h1>
                <?php if ($term->description) : ?>
                    <div class="term-description maxw-720 ms-0 fs-18">
                        
                        <?php echo iw_trim_content( $term->description ); ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Search & Filters - Modified to maintain current category -->
            <?php 
            // Pass the current category to the filter template
            set_query_var('current_category', $term->slug);
            get_template_part('template-parts/search-and-filter'); 
            ?>

            <!-- Articles Grid -->
            <?php 
            $paged = get_query_var('paged') ? get_query_var('paged') : 1;
            $args = array(
                'post_type' => 'resource',
                'tax_query' => array(
                    array(
                        'taxonomy' => 'resource_type',
                        'field'    => 'slug',
                        'terms'    => $term->slug,
                    ),
                ),
                'paged' => $paged,
            );
            
            if(isset($_REQUEST['search_query']) && $_REQUEST['search_query'] != '') {
                $args['s'] = $_REQUEST['search_query'];
                $args['relevanssi'] = true;
            }

            $query = new WP_Query($args);

            if ($query->have_posts()) : ?>
                <div class="row g-4 apum-animation-fadeInUp">
                    <?php while ($query->have_posts()) : $query->the_post(); ?>
                        <div class="col-12 col-md-4">
                            <?php
                            $author_title = get_field('author_title');
                            $additional_classes = ' h-100 card flex-column';
                            $show_author = true;
                            $image_size = [1280, 720];
							$external_url = get_field('external_url');
							;
                            get_template_part('template-parts/articlecard', null, compact('author_title', 'additional_classes', 'show_author', 'image_size','external_url'));
                            ?>
                            <?php
                            $post_url = get_permalink();
                            $title    = trim(get_the_title());
                            $alt_text = $title ? $title : 'Resource image';
                            ?>

                            <article class="card h-100 hide">
                                    <?php if ($post_url) : ?>
                                <a href="<?php echo esc_url($post_url); ?>">
                                <?php endif; ?>

                                    <?php if (has_post_thumbnail()) : ?>
                                        <?php
                                        echo get_the_post_thumbnail(
                                            get_the_ID(),
                                            'medium',
                                            [
                                                'class' => 'img-fluid card-img-top',
                                                'alt'   => esc_attr($alt_text),
                                            ]
                                        );
                                        ?>
                                    <?php else : ?>
                                        <img
                                            src="https://placehold.co/600x400?text=placeholder"
                                            class="img-fluid card-img-top"
                                            alt="<?php echo esc_attr($alt_text); ?>"
                                            loading="lazy"
                                            decoding="async"
                                        >
                                    <?php endif; ?>

                                <?php if ($post_url) : ?>
                                </a>
                                <?php endif; ?>
                                <div class="card-body d-flex flex-column gap-8">
                                    <?php 
                                    $terms = get_the_terms(get_the_ID(), 'resource_type'); 
                                    if (!empty($terms) && !is_wp_error($terms)) {
                                        $term = $terms[0];
                                        echo '<a href="' . esc_url(get_term_link($term)) . '" class="badge ' . esc_attr(get_resource_category_class()) . '">' . esc_html($term->name) . '</a>';
                                    }
                                    ?>
                                    <h5 class="card-title text-truncation line-2">
                                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                    </h5>
                                    <div class="text-muted apum-article-author fs-14 d-flex align-items-center gap-8">
                                        —
                                        <?php
                                        $author_post_ids = get_field('resources_author');
                                        $author_links = [];

                                        if ($author_post_ids) {
                                            foreach ((array)$author_post_ids as $author_post_id) {
                                                if (is_object($author_post_id)) {
                                                    $author_links[] = '<a href="'.get_permalink($author_post_id->ID).'">'.$author_post_id->post_title.'</a>';
                                                } elseif (is_numeric($author_post_id)) {
                                                    $author_links[] = '<a href="'.get_permalink($author_post_id).'">'.get_the_title($author_post_id).'</a>';
                                                }
                                            }
                                            echo implode(', ', $author_links);
                                        }
                                        ?>
                                        <span class="fw-bold">•</span>
                                        <span><?php echo get_the_date('j M Y'); ?></span>
                                    </div>
                                </div>
                            </article>
                        </div>
                    <?php endwhile; ?>
                </div>

                <!-- Pagination -->
                <nav class="pagination-container apum-animation-fadeInUp mt-5">
                    <?php
                    $big = 999999999; // A large number for pagination replacement
                    $current_page = max(1, get_query_var('paged'));
                    $total_pages = $query->max_num_pages;

                    if ($total_pages > 1) {
                        echo '<ul class="pagination">';

                        // Previous Button
                        if ($current_page > 1) {
                            echo '<li class="prev"><a href="' . esc_url(get_pagenum_link($current_page - 1)) . '"><i class="fas fa-arrow-left"></i> <span class="btn-text">Previous</span></a></li>';
                        } else {
                            echo '<li class="prev disabled"><span><i class="fas fa-arrow-left"></i> <span class="btn-text">Previous</span></span></li>';
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
                            echo '<li class="next"><a href="' . esc_url(get_pagenum_link($current_page + 1)) . '"><span class="btn-text">Next</span> <i class="fas fa-arrow-right"></i></a></li>';
                        } else {
                            echo '<li class="next disabled"><span><span class="btn-text">Next</span> <i class="fas fa-arrow-right"></i></span></li>';
                        }

                        echo '</ul>';
                    }
                    ?>
                </nav>
            <?php else : ?>
			 <div class="text-center">
					   <h4><?php echo esc_html( t('iw_no_resources_found_in_this_category') ); ?></h4>

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
<?php
get_template_part('template-parts/main-wrapper-end');
?>

<!-- Info card section -->
<?php get_template_part('template-parts/section', 'info-cards'); ?>

<?php get_footer(); ?>