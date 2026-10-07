<?php
$announcement_query = new WP_Query([
    'post_type'      => 'announcement',
    'posts_per_page' => 2,
]);

if ($announcement_query->have_posts()) : ?>
    <!-- <span class="divider d-none d-md-block"></span> -->
    <div class="announcement mb-4 apum-animation-fadeInUp">
        <div class="announcement-label">
            <p class="text-uppercase"><?php echo esc_html( t('iw_announcements') ); ?></p>
        </div>
        <div class="announcement-slider">
            <?php while ($announcement_query->have_posts()) : $announcement_query->the_post();
                $announcement_link = get_field('announcement_url');
                if ($announcement_link && isset($announcement_link['url'])) :
                    $link_url = $announcement_link['url'];
                    $link_target = !empty($announcement_link['target']) ? $announcement_link['target'] : '_self';
            ?>
                <div class="slick-slide">
                        <h3 class="fs-20 fw-bold">
							<a href="<?php echo esc_url($link_url); ?>" target="<?php echo esc_attr($link_target); ?>">
								<?php the_title(); ?></a></h3>
                     
	<?php $tooltip_text = esc_attr( wp_strip_all_tags( get_the_content() ) ); ?>

<div data-bs-toggle="tooltip"
     data-bs-placement="bottom"
     data-bs-title="<?php echo $tooltip_text; ?>">
    <?php the_content(); ?>
</div>
</div>
            <?php endif; endwhile; wp_reset_postdata(); ?>
        </div>
    </div>
<?php endif; ?>
