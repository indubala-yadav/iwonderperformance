<!-- 	<div class="single-post-nav maxw-720 mx-auto print-no">
		<?php $prev_post = get_previous_post(); ?>
<?php if (!empty($prev_post)): ?>
  <a href="<?php echo get_permalink($prev_post); ?>" class="nav-article nav-prev mb-3" aria-label="Previous Article: <?php echo esc_attr($prev_post->post_title); ?>">
    <span class="nav-arrow" aria-hidden="true"><i class="fas fa-arrow-left"></i></span>
    <div class="flex-grow-1">
      <div class="nav-label"> <?php echo esc_html( t('iw_previous_article') ); ?></div>
      <div class="nav-title fs-16-24"><?php echo wp_trim_words($prev_post->post_title, 8, '...'); ?></div>
    </div>
  </a>
<?php endif; ?>

	  <?php if (get_next_post()): ?>
		<a href="<?php echo get_permalink(get_next_post()); ?>" class="nav-article nav-next">
		  <div class="flex-grow-1">
			<div class="nav-label">Next Article</div>
			<div class="nav-title fs-16-24"><?php echo wp_trim_words(get_next_post()->post_title, 8, '...'); ?></div>
		  </div>
		  <span class="nav-arrow"><i class="fas fa-arrow-right"></i></span>
		</a>
	  <?php endif; ?>
	</div> -->

<?php
$current_post_id = get_the_ID();
$current_title = get_the_title();
$current_categories = wp_get_post_categories($current_post_id);

$args = [
    'post_type'      => 'post',
    'posts_per_page' => -1,
    'orderby'        => 'date',
    'order'          => 'ASC',
    'category__in'   => $current_categories,
    'fields'         => 'ids',
];

$posts_in_category = get_posts($args);

// Find current post position
$current_index = array_search($current_post_id, $posts_in_category);

$prev_post_id = $current_index > 0 ? $posts_in_category[$current_index - 1] : null;
$next_post_id = $current_index < count($posts_in_category) - 1 ? $posts_in_category[$current_index + 1] : null;
?>

<div class="single-post-nav maxw-720 mx-auto print-no">
    <?php
    // Get current language
    $current_lang = apply_filters('wpml_current_language', null);

    // Translate post IDs to current language
    if (!empty($prev_post_id)) {
        $translated_prev_post_id = apply_filters('wpml_object_id', $prev_post_id, get_post_type($prev_post_id), false, $current_lang);
    }

    if (!empty($next_post_id)) {
        $translated_next_post_id = apply_filters('wpml_object_id', $next_post_id, get_post_type($next_post_id), false, $current_lang);
    }
    ?>

    <?php if (!empty($translated_prev_post_id)): ?>
        <a href="<?php echo get_permalink($translated_prev_post_id); ?>" class="nav-article nav-prev mb-3" aria-label="Previous Article: <?php echo esc_attr(get_the_title($translated_prev_post_id)); ?>">
            <span class="nav-arrow" aria-hidden="true"><i class="fas fa-arrow-left"></i></span>
            <div class="flex-grow-1">
               <div class="nav-label"><?php echo __( t('iw_previous') ); ?></div>

                <div class="nav-title fs-16-24"><?php echo wp_trim_words(get_the_title($translated_prev_post_id), 12, '...'); ?></div>
            </div>
        </a>
    <?php endif; ?>

    <?php if (!empty($translated_next_post_id)): ?>
        <a href="<?php echo get_permalink($translated_next_post_id); ?>" class="nav-article nav-next">
            <div class="flex-grow-1 text-end">
              <div class="nav-label"><?php echo __( t('iw_next') ); ?></div>

                <div class="nav-title fs-16-24"><?php echo wp_trim_words(get_the_title($translated_next_post_id), 12, '...'); ?></div>
            </div>
            <span class="nav-arrow"><i class="fas fa-arrow-right"></i></span>
        </a>
    <?php endif; ?>
</div>
