<?php
/*
Template Name: Archives
*/

get_header();

// Get selected categories from URL
$selected_categories = [];

if ( isset($_GET['category']) ) {
    $raw = wp_unslash($_GET['category']); // keep commas
    $selected_categories = array_map('sanitize_title', explode(',', $raw));
}

?>

<?php  get_template_part('template-parts/main-wrapper-start'); ?>
		 <div class="px-md-0 pb-4">
        <div class="d-flex flex-column justify-items-center title-area add-motif mb-4">
			<?php custom_breadcrumb(); ?>
            <h1><?php echo esc_html( t('iw_resources') ); ?></h1>
			<h2 class="sr-only d-none">At Right Angles, A Resource for school Mathematics</h2>
			<div class="category-description maxw-720 ms-0 fs-18">
    <?php echo iw_trim_content( t('iw_resources_description') ); ?>
</div>
        </div>

        <!-- Search & Filters -->
		<?php if (false) : ?>
    <?php get_template_part('template-parts/search-and-filter'); ?>
<?php endif; ?>

        <!-- Articles Grid -->
        <?php 
		$paged = get_query_var('paged') ? get_query_var('paged') : 1;

		$args = array(
			'post_type' => 'resource',
			'paged'     => $paged,
			'lang'      => apply_filters('wpml_current_language', null),
		);

		// Search
		if ( isset($_REQUEST['search_query']) && $_REQUEST['search_query'] !== '' ) {
			$args['s'] = sanitize_text_field($_REQUEST['search_query']);
			$args['relevanssi'] = true;
		}

		// Category filter
		if ( ! empty($selected_categories) ) {
			$args['tax_query'] = array(
				array(
					'taxonomy' => 'resource_type',
					'field'    => 'slug',
					'terms'    => $selected_categories,
				),
			);
		}

		

//print_r($args);
        $query = new WP_Query($args);

        if ($query->have_posts()) : ?>
	
            <div class="d-grid grid-col-3 apum-animation-fadeInUp">
                <?php while ($query->have_posts()) : $query->the_post(); ?>
                    
						<?php
						
				$author_title = get_field('author_title');
				$additional_classes = 'card flex-coulmn';
				$show_author = true;
				$image_size = [1280, 720];
				 $external_url = get_field('external_url');	
						
				get_template_part('template-parts/articlecard', null, compact('author_title', 'additional_classes', 'show_author', 'image_size','external_url'));
				?>
						<?php endwhile; ?>
				
            </div>

            <!-- Pagination -->
			<nav class="pagination-container apum-animation-fadeInUp mt-5">
				<?php
				//global $wp_query;
				$big = 999999999; // A large number for pagination replacement
				$current_page = max(1, get_query_var('paged'));
				$total_pages = $query->max_num_pages;

				if ($total_pages > 1) {
					echo '<ul class="pagination">';

					// Previous Button
					if ($current_page > 1) {
						echo '<li class="prev"><a href="' . esc_url(get_pagenum_link($current_page - 1)) . '"><i class="fas fa-arrow-left"></i><span class="btn-text">' . esc_html(t('iw_previous')) . '</span></a></li>';
					} else {
						echo '<li class="prev disabled"><span><i class="fas fa-arrow-left"></i><span class="btn-text">' . esc_html(t('iw_previous')) . '</span></span></li>';
					}

					// Pagination Links
					$pagination_links = paginate_links([
						'base'      => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
						'format'    => '?paged=%#%',
						'current'   => $current_page,
						'total'     => $total_pages,
						'mid_size'  => 1,
						'end_size'  => 1,
						'type'      => 'array', // Important: Get an array of links
						'prev_next' => false, // We already have our custom prev/next
					]);

					if (!empty($pagination_links)) {
						foreach ($pagination_links as $link) {
							echo '<li>' . $link . '</li>';
						}
					}

					// Next Button
					if ($current_page < $total_pages) {
						echo '<li class="next"><a href="' . esc_url(get_pagenum_link($current_page + 1)) . '"><span class="btn-text">' . esc_html(t('iw_next')) . '</span> <i class="fas fa-arrow-right"></i></a></li>';
					} else {
						echo '<li class="next disabled"><span><span class="btn-text">' . esc_html(t('iw_next')) . '</span> <i class="fas fa-arrow-right"></i></span></li>';
					}

					echo '</ul>';
				}
				?>
			</nav>
        <?php else : ?>
<!--             <div class="text-center">
					    <h4><?php echo esc_html( t('iw_no_resource_found') ); ?></h4>
				<div class="text-center mt-5">
    <div class="mt-3 ms-md-3">
        <a class="btn btn-primary" href="<?php echo esc_url(get_post_type_archive_link('resource')); ?>" role="button">
           <i class="fa-solid fa-arrow-rotate-right me-1"></i><span><?php echo esc_html( t('iw_reset') ); ?> </span>
        </a>
    </div>
</div>
					</div> -->
		 <div class="text-center">
					    <h4><?php echo esc_html( t('iw_no_resource_found') ); ?></h4>
			 
			 	<?php get_template_part('template-parts/screenscananimation'); ?>
				<div class="text-center  mt-48-124">
    <div class="mt-3 ms-md-3">
<?php
// Get current URL without 'search_query'
$current_url = home_url( add_query_arg( null, null ) );
$clean_url = remove_query_arg( 'search_query', $current_url );
?>
<a class="btn btn-primary" href="<?php echo esc_url( get_post_type_archive_link('resource') ); ?>" role="button">
    <i class="fa-solid fa-arrow-rotate-right me-1"></i>
    <span><?php echo esc_html( t('iw_reset') ); ?></span>
</a>
    </div>
</div>
					</div>
        <?php endif; ?>
        <?php wp_reset_postdata(); ?>
<!-- <?php $has_search_query = isset($_REQUEST['search_query']) && !empty($_REQUEST['search_query']); ?>
<?php if ($has_search_query) : ?>
<div class="text-center mt-5">
    <div class="mt-3 ms-md-3">
        <a class="btn btn-primary btn-animate" href="<?php echo esc_url(get_post_type_archive_link('resource')); ?>" role="button">
            <i class="fas fa-arrow-left"></i> Go Back
        </a>
    </div>
</div>
<?php endif; ?> -->
</div>
<?php
get_template_part('template-parts/main-wrapper-end');
?>
    

<!-- Info card section -->
<?php get_template_part('template-parts/section', 'info-cards'); ?>

<?php get_footer(); ?>