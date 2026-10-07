<?php get_header(); ?>

<main id="primary" class="site-main">
    <?php while (have_posts()) : the_post(); ?>
        <section id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <header class="entry-header">
                <h1 class="entry-title"><?php the_title(); ?></h1>
            </header>

            <div class="entry-meta">
                <?php
                $issue_number = get_post_meta(get_the_ID(), '_issue_number', true);
                $publication_date = get_post_meta(get_the_ID(), '_publication_date', true);
                
                if ($issue_number) {
                    echo '<p><strong>Issue Number:</strong> ' . esc_html($issue_number) . '</p>';
                }
                if ($publication_date) {
                    echo '<p><strong>Publication Date:</strong> ' . esc_html($publication_date) . '</p>';
                }
                ?>
            </div>

            <div class="entry-content">
                <?php the_content(); ?>
            </div>

            <?php if (has_post_thumbnail()) : ?>
                <div class="post-thumbnail">
                    <?php the_post_thumbnail('large'); ?>
                </div>
            <?php endif; ?>

        </section>
    <?php endwhile; ?>
</main>

<?php get_footer(); ?>
