<?php
// Safe defaults
$additional_classes = $args['additional_classes'] ?? '';
$show_author        = $args['show_author'] ?? true;
$image_size         = $args['image_size'] ?? 'article-card';
$author_title       = $args['author_title'] ?? '';
$heading_tag        = $args['heading_tag'] ?? 'h3';
$img_a_classes      = $args['img_a_classes'] ?? '';
$resource_term      = $args['resource_term'] ?? null;
$short_note         = $args['short_note'] ?? '';
$heading_class      =$args['heading_class'] ?? '';
// Determine image priority
$is_featured = ($heading_tag === 'h1');
$loading_attr = $is_featured ? 'eager' : 'lazy';
$priority_attr = $is_featured ? 'fetchpriority="high"' : '';

// Link target
if (get_post_type() === 'resource') {

    $external_url = get_field('external_url');

    if (!empty($external_url)) {

        $is_internal = str_starts_with($external_url, home_url()) || str_starts_with($external_url, '/');

        if ($is_internal) {

            $check_url = str_starts_with($external_url, '/')
                ? home_url($external_url)
                : $external_url;

            $response = wp_remote_head($check_url, ['timeout' => 3]);

            if (is_wp_error($response) || wp_remote_retrieve_response_code($response) === 404) {
                // ❌ Broken internal link → disable
                $post_link   = false;
                $is_external = false;
            } else {
                // ✅ Valid internal link
                $post_link   = esc_url($external_url);
                $is_external = false;
            }

        } else {
            // ✅ Valid external link
            $post_link   = esc_url($external_url);
            $is_external = true;
        }

    } else {
        // No URL provided
        $post_link   = false;
        $is_external = false;
    }

} else {
    // Non-resource post → normal permalink
    $post_link   = get_the_permalink();
    $is_external = false;
}


// Term if not passed 
// this is the code for resource type

// if (!$resource_term && get_post_type() === 'resource') {
//     $terms = get_the_terms(get_the_ID(), 'resource_type');
//     if (!empty($terms) && !is_wp_error($terms)) {
//         $resource_term = $terms[0];
//     }
// }
// this is the code for resource categories
if (!$resource_term && get_post_type() === 'resource') {
    $terms = get_the_terms(get_the_ID(), 'resource_categories');
    if (!empty($terms) && !is_wp_error($terms)) {
        $resource_term = $terms[0];
    }
}


$category_class = '';

$post_id   = get_the_ID();
$post_type = get_post_type($post_id);

// this is the code for resource type
// if ($post_type === 'resource') {

//     $terms = get_the_terms($post_id, 'resource_type');

//     if (!empty($terms) && !is_wp_error($terms)) {

//         // ACF term field
//         $field_value = get_field('category__class', 'resource_type_' . $terms[0]->term_id);

//         if (!empty($field_value)) {
//             $category_class = 'cat-' . sanitize_html_class($field_value);
//         }
//     }

// } 

if ($post_type === 'resource') {

    $terms = get_the_terms($post_id, 'resource_categories');

    if (!empty($terms) && !is_wp_error($terms)) {

        // ACF term field
        $field_value = get_field('category__class', 'resource_categories_' . $terms[0]->term_id);

        if (!empty($field_value)) {
            $category_class = 'resource-categories cat-' . sanitize_html_class($field_value);
        }
    }

}

else {

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

<article class="card <?php echo esc_attr(trim("$additional_classes $category_class")); ?>">
<?php
$img_alt = get_the_title();
?>

<?php if ($post_link) : ?>
  <a href="<?php echo esc_url($post_link); ?>"
     <?php if ($is_external) echo 'target="_blank" rel="noopener noreferrer"'; ?>
     aria-label="<?php echo esc_attr(
        get_the_title() . ($is_external ? ' PDF, opens in new tab' : '')
     ); ?>"
     class="<?php echo esc_attr($img_a_classes); ?> placeholder-wrap">

    <?php if (has_post_thumbnail()) : ?>

      <?php
      echo wp_get_attachment_image(
          get_post_thumbnail_id(),
          $image_size,
          false,
          [
              'class'         => 'img-fluid card-img-top',
              'alt'           => get_the_title(),
              'loading'       => $loading_attr,
              'decoding'      => 'async',
              'fetchpriority' => $is_featured ? 'high' : false,
              'sizes'         => '(max-width: 767px) 100vw, (max-width: 1199px) 50vw, 33vw',
          ]
      );
      ?>

    <?php else : ?>

      <span class="placeholder-image" aria-hidden="true"></span>
      <span class="placeholder-title">
        <?php echo esc_html(get_the_title()); ?>
      </span>

    <?php endif; ?>

  </a>

<?php else : ?>

  <div class="<?php echo esc_attr($img_a_classes); ?> placeholder-wrap h-100 w-100 ">

    <?php if (has_post_thumbnail()) : ?>

      <?php echo get_the_post_thumbnail(
          get_the_ID(),
          $image_size,
          ['class' => 'img-fluid card-img-top', 'alt' => get_the_title()]
      ); ?>

    <?php else : ?>

      <span class="placeholder-image img-fluid card-img-top" aria-hidden="true"></span>
      <span class="placeholder-title">
        <?php echo esc_html(get_the_title()); ?>
      </span>

    <?php endif; ?>

  </div>

<?php endif; ?>


<?php if (get_post_type() === 'resource') : ?>
	<div class="card-body-main">
		
	 <?php endif; ?>
    <div class="card-body">
       
        <?php if ($resource_term): ?>
            <?php
$term_link = get_term_link($resource_term, 'resource-categories');

if (!is_wp_error($term_link)) : ?>
    <a href="<?php echo esc_url($term_link); ?>"
       class="badge"
       aria-label="<?php echo esc_attr($resource_term->name); ?>">
        <span><?php echo esc_html($resource_term->name); ?></span>
    </a>
<?php endif; ?>
		
               <div class="badge d-none">
				    <span><?php echo esc_html($resource_term->name); ?></span>
		</div>
            
        <?php elseif (!empty($categories)) : ?>
            <?php $category = $categories[0]; ?>
            <a href="<?php echo esc_url(get_category_link($category->term_id)); ?>"
               class="badge"
               aria-label="<?php echo esc_attr($category->name); ?>">
                <span><?php echo esc_html($category->name); ?></span>
            </a>
        <?php endif; ?>

       <<?php echo $heading_tag; ?> class="card-title text-truncation <?php echo esc_attr($heading_class); ?>">
    <?php if ($post_link) : ?>
        <a href="<?php echo esc_url($post_link); ?>"
           <?php if ($is_external) echo 'target="_blank" rel="noopener noreferrer"'; ?>
           aria-label="<?php echo esc_attr(get_the_title() . ($is_external ? ' PDF, opens in new tab' : '')); ?>">
            <?php the_title(); ?>
        </a>
    <?php else : ?>
        <?php the_title(); ?>
    <?php endif; ?>
</<?php echo $heading_tag; ?>>


        <?php if ($short_note) : ?>
            <?php echo apply_filters('the_content', $short_note); ?>
        <?php endif; ?>

        <div class="text-muted fs-14 d-flex align-items-center gap-8">
            <div class="w-100 gap-1">― <span class="line-animation"><?php echo display_dynamic_linked_authors($post->ID); ?></span></div>
            <span class="apum-dot d-none">•</span>
            <span class="d-none"><?php the_time('d M Y'); ?></span>
        </div>

   

	</div>
	<?php if (get_post_type() === 'resource') : ?>
	<?php
$items         = get_field('attachment_sheet_items');
$external_url  = get_field('external_url');

// --- Count how many sections should appear ---
$sections = 0;

// check related links has at least ONE valid link
$has_related_links = false;
if ($items) {
    foreach ($items as $item) {
        if (!empty($item['related_links'])) {
            $has_related_links = true;
            break;
        }
    }
}

if ($has_related_links) $sections++;
if ($external_url)      $sections++;

// width logic
$col_class = ($sections === 2) ? 'w-50' : 'w-100';
?>

<div class="d-flex align-items-center justify-content-between resource-related-links-main position-relative">

    <!-- RELATED LINKS SECTION -->
    <?php if ($has_related_links): ?>
        <div class="d-flex align-items-center justify-content-between resource-related-links toogle-related-btn <?php echo $col_class; ?>">
            <span class="text-capitalize"><?php echo esc_html( t('iw_Related_Links') ); ?></span>
<!--             <img src="/wp-content/uploads/2025/11/dropdown-icon.svg" width="14" height="8"> -->
			<i class="fa-solid fa-caret-down opacity-50"></i>
		
        </div>
    <?php endif; ?>


    <!-- EXTERNAL LINK SECTION -->
    <?php if ($external_url): ?>
        <a href="<?php echo esc_url($external_url); ?>"
           target="_blank"
           rel="noopener"
           class="d-flex align-items-center  justify-content-between resource-related-links download-pdf-btn <?php echo $col_class; ?>" aria-label="<?php echo esc_attr(get_the_title() . ' PDF, opens in new tab'); ?>">

            <span class="text-capitalize"><?php echo esc_html( t('iw_Download_PDF') ); ?></span>
            <i class="fa-solid fa-download opacity-50" aria-hidden="true"></i>

        </a>

       

    <?php endif; ?>

</div>

    <?php endif; ?>
	
 <?php if (is_post_type_archive('resource')) :  ?>
        </div> <!-- Close .d-flex.resources-card -->
    <?php endif; ?>
<!-- RELATED LINKS SECTION -->
<?php if (get_post_type() === 'resource') : ?>
<?php if ($has_related_links): ?>
   

            <div class="resource-related-links-content">
                <h3 class="fs-18 mb-2 text-capitalize fw-semibold mt-4 mx-3 pb-3"><?php echo esc_html__( 'Related links', 'filter-text-domain' ); ?></h3>
			<div class="position-relative resource-related-links-content-wrapper w-100 position-absolute">
				<div class="resource-related-links-content-innerwrapper w-100 d-flex flex-column">

			<?php
			$valid_items = array_filter($items, fn($i) => !empty($i['related_links']));
			$total = count($valid_items);
			$index = 0;

			foreach ($items as $item):
				$title = $item['related_link_title'];
				$url   = $item['related_links'];

				if (!$url) continue;

				$index++;
				$border_class = ($index < $total) ? 'line-gradint' : '';

				// --- ICON LOGIC ---
				$is_pdf = preg_match('/\.pdf(\?.*)?$/i', $url);
				$is_youtube = preg_match('/(youtube\.com|youtu\.be)/i', $url);
				$is_google_doc = preg_match('/docs\.google\.com/i', $url);
				$is_internal = str_starts_with($url, home_url()) || str_starts_with($url, '/');

				// Choose icon
				if ($is_pdf) {
				 $icon_html =  '<i class="fa-regular fa-file-pdf fa-lg"></i>';
				} elseif ($is_youtube) {
				 $icon_html = '	<i class="fa-brands fa-youtube fa-lg"></i>';
				} elseif ($is_google_doc) {
					 $icon_html =  '<i class="fa-regular fa-file fa-lg"></i>';
				} elseif ($is_internal) {
				 $icon_html = '<i class="fa-regular fa-newspaper fa-lg"></i>';
				} else {
					 $icon_html = '<i class="fa-regular fa-play fa-lg"></i>';
				}
			?>

				<a href="<?php echo esc_url($url); ?>"
   target="_blank"
   rel="noopener"
   class="d-flex align-items-center justify-content-between px-3 py-2  gap-2 <?php echo $border_class; ?>">

    <span><?php echo esc_html($title); ?></span>

    <?php echo $icon_html; ?>

</a>


			<?php endforeach; ?>

			</div>
				</div>

            </div>
<?php endif; ?>
<?php endif; ?>
</article>
