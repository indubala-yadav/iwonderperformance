<?php 
/**
 * Template Name: Editorial Member Profile
 */
get_header(); ?>

<div id="primary" class="container-fluid p-0 position-relative apum-innerpage-section">
    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
      
        
        <main id="main" class="site-main apum-section position-relative section-pad pt-4">
            <article id="post-<?php the_ID(); ?>" class="container bg-white pt-80 pb-4 rounded d-flex flex-column gap-24-40">
                <div class="maxw-720 content-area">
                    <div class="title-area add-motif mb-5">
                        <h1 class="entry-title mb-0"><?php the_title(); ?></h1>
                        
                        <?php
                        // Get the field object to access choices
                        $field = get_field_object('editorial_member_designation');
                        $designation_value = get_field('editorial_member_designation');
                        
                        if ($designation_value && $field && isset($field['choices'][$designation_value])) :
                            $designation_label = $field['choices'][$designation_value];
                            ?>
                            <p class="my-2 designation-<?php echo sanitize_title($designation_value); ?>">
                                <?php echo esc_html($designation_label); ?>
                            </p>
                        <?php endif; ?>
                        
                        <?php
                        $email = get_field('editorial_member_email');
                        if ($email) : ?>
                            <a href="mailto:<?php echo esc_attr($email); ?>" class="text-decoration-none">
                                <?php echo esc_html($email); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                    
                    <div class="d-flex flex-column flex-md-row gap-24-40">
                        <div class="post-thumbnail" style="width: 220px; height: 220px; flex-shrink: 0;">
                            <?php if (has_post_thumbnail()) : ?>
                                <?php the_post_thumbnail('full', [
                                    'class' => 'object-fit-cover',
                                    'style' => 'width: 100%; height: 100%; object-fit: cover;',
                                    'alt' => esc_attr(get_the_title())
                                ]); ?>
                            <?php else : ?>
                                <img src="<?php echo esc_url('https://placehold.co/220x220?text=Editor'); ?>" 
                                     class="object-fit-cover"
                                     style="width: 100%; height: 100%; object-fit: cover;"
                                     alt="<?php esc_attr_e('Placeholder image', 'textdomain'); ?>">
                            <?php endif; ?>
                        </div>
                        
                        <div class="entry-content">
                           <h2>
							   this is editorial team testing
							</h2> <?php the_content(); ?>
                        </div>
                    </div>

                 
					<?php
// Get all 'editorial-member' posts ordered by title (alphabetically)
$members = get_posts(array(
    'post_type' => 'editorial_team',
    'posts_per_page' => -1,
    'orderby' => 'title',
    'order' => 'ASC',
    'post_status' => 'publish'
));

$current_id = get_the_ID();
$prev_post = null;
$next_post = null;

// Loop to find current post and assign prev/next
foreach ($members as $index => $member) {
    if ($member->ID == $current_id) {
        if ($index > 0) {
            $prev_post = $members[$index - 1];
        }
        if ($index < count($members) - 1) {
            $next_post = $members[$index + 1];
        }
        break;
    }
}
?>

<!-- Alphabetical Navigation Buttons -->
<div class="single-post-nav maxw-720 mx-auto mt-5">
    <?php
    global $post;

    // Get current language
    $current_lang = apply_filters('wpml_current_language', null);

    // Get adjacent posts (custom post type support)
    $prev_post = get_adjacent_post(false, '', true);
    $next_post = get_adjacent_post(false, '', false);

    // Translate them to current language using WPML
    if ($prev_post) {
        $translated_prev_id = apply_filters('wpml_object_id', $prev_post->ID, get_post_type($prev_post), false, $current_lang);
        $prev_post = get_post($translated_prev_id);
    }

    if ($next_post) {
        $translated_next_id = apply_filters('wpml_object_id', $next_post->ID, get_post_type($next_post), false, $current_lang);
        $next_post = get_post($translated_next_id);
    }
    ?>

    <?php if (!empty($prev_post)): ?>
    <a href="<?php echo get_permalink($prev_post); ?>" class="nav-article nav-prev mb-3" aria-label="Previous Member: <?php echo esc_attr($prev_post->post_title); ?>">
        <span class="nav-arrow" aria-hidden="true"><i class="fas fa-arrow-left"></i></span>
        <div class="flex-grow-1">
            <div class="nav-label">Previous Member</div>
            <div class="nav-title fs-16-24"><?php echo wp_trim_words($prev_post->post_title, 8, '...'); ?></div>
        </div>
    </a>
    <?php endif; ?>

    <?php if (!empty($next_post)): ?>
    <a href="<?php echo get_permalink($next_post); ?>" class="nav-article nav-next">
        <div class="flex-grow-1">
            <div class="nav-label">Next Member</div>
            <div class="nav-title fs-16-24"><?php echo wp_trim_words($next_post->post_title, 8, '...'); ?></div>
        </div>
        <span class="nav-arrow"><i class="fas fa-arrow-right"></i></span>
    </a>
    <?php endif; ?>
</div>

</div>
</article>
</main>
<?php endwhile; endif; ?>
</div>

<?php get_template_part('template-parts/section', 'info-cards'); ?>
<?php get_footer(); ?>