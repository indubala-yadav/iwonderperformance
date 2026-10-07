<?php get_header(); ?>

<?php get_template_part('template-parts/main-wrapper-start'); ?>

<div class="apum-innerpage-body pb-0 px-48 d-flex flex-column gap-24-48">
    <div class="maxw-720">
        <div class="page-header title-area add-motif">
            <?php custom_breadcrumb(); ?>
            <h1 class="page-title mb-0"><?php echo esc_html( t('iw_authors') ); ?></h1>
        </div>

        <!-- Search & Filters -->
        <?php get_template_part('template-parts/search-and-filter'); ?>

        <div class="d-grid grid-col-4-author maxw-720">
            <?php
            try {
                $args = array(
                    'post_type'      => 'authors_bio',
                    'posts_per_page' => -1,
                    'nopaging'       => true,
                );

                $author_query = new WP_Query($args);

                if ($author_query->have_posts()) :
                    
                    // Guard Rail Setup
                    $loop_counter      = 0;
                    $max_safe_limit    = 500; // 🛑 1. Safety limit for maximum loop iterations
                    $memory_limit_bytes = 128 * 1024 * 1024; // 🛑 2. 128MB safety limit

                    while ($author_query->have_posts()) : 
                        
                        // ✅ FIX 1: Advances loop pointer to prevent infinite loops
                        $author_query->the_post(); 
                        $loop_counter++;

                        // 🛑 GUARD RAIL 1: Catch infinite loops early
                        if ($loop_counter > $max_safe_limit) {
                            throw new Exception("Safety threshold exceeded ({$max_safe_limit} iterations). Possible infinite loop prevented.");
                        }

                        // 🛑 GUARD RAIL 2: Catch high memory usage before PHP crashes
                        if (memory_get_usage() > $memory_limit_bytes) {
                            throw new Exception("Memory consumption threshold exceeded (" . round(memory_get_usage() / 1024 / 1024, 2) . " MB). Halting execution safely.");
                        }

                        $post_id = get_the_ID();
                ?>
                        <a href="<?php the_permalink(); ?>" class="d-block author-box px-12 py-3 text-decoration-none">
                            <span><?php the_title(); ?></span>
                        </a>
                <?php 
                    endwhile;
                    wp_reset_postdata();
                endif;

            } catch (Exception $e) {
                // 🛡️ SAFE EXCEPTION HANDLING:
                // Log the issue without crashing the WordPress theme layout
                error_log('WP_Query Exception Catch: ' . $e->getMessage());

                if ( defined('WP_DEBUG') && WP_DEBUG ) {
                    echo '<div class="alert alert-warning w-100 my-3">';
                    echo '<strong>Query Execution Guard Rail Triggered:</strong> ' . esc_html($e->getMessage());
                    echo '</div>';
                }
            }
            ?>
        </div>
    </div>
</div>

<?php get_template_part('template-parts/main-wrapper-end'); ?>
<?php get_footer(); ?>