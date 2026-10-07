<?php get_header(); ?>

<section class="container-fluid p-0 pb-48-96 position-relative apum-innerpage-section pdf-export">
	<div class="theme-top-ct apply-top-pattern apply-bottom-pattern pattern-contrast i-wonder-theme-top-ct"></div>

	<div class="apum-section position-relative section-pad pt-5 pt-lg-3 pdf-export-wrapper" id="pdf-content">
		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

				<article class="container apum-innerpage-body bg-white pt-80 pb-0 rounded article-wrapper">
					<div class="maxw-720">
						<div class="title-area add-motif mb-4">
							<div class="print-no"><?php custom_breadcrumb(); ?></div>
							<h1><?php the_title(); ?></h1>
						</div>

						<div class="d-flex flex-column flex-md-row gap-3 mb-4 align-items-start align-items-md-center category-badge-issue-parent">
							<?php
							$category = get_the_category();
							if (!empty($category)) {
								echo '<a href="' . esc_url(get_category_link($category[0]->term_id)) . '" class="badge ' . esc_attr(get_category_class()) . '"><span>' . esc_html($category[0]->name) . '</span></a>';
							}
							?>
							<div class="d-flex align-items-baseline magzine-issue-box">

								<?php
								$terms = get_the_terms(get_the_ID(), 'magazine_category');

								if (!empty($terms) && !is_wp_error($terms)) {




									ob_start();
									display_magazine_categories();
									$categories = trim(ob_get_clean());
									if (!empty($categories)) {
										echo $categories . '<span class="mx-2 apum-dot">•</span>';
									}
								}
								?>


								<a class="publishdate"><?php echo esc_html(t('iw_publishon')); ?> <?php echo esc_html(get_the_date(__('j, M Y', 'iwtrings'))); ?></a>
							</div>
						</div>

						<?php get_template_part('template-parts/section', 'post-author'); ?>

						<div class="other-translators mt-3">
							<?php the_field('other_translators'); ?>
						</div>

						<!--	Share and Download button 	-->

						<!-- 		<div class="d-flex align-items-center gap-4 flex-wrap share-pdf-row print-no"> -->
						<?php get_template_part('template-parts/share-download'); ?>
						<!-- 		</div> -->
						<!-- 			latex-shortnot -->
						<!-- 
			<?php if ($short_note = get_field('short_note')) : ?>
				<div class="short-note hide-sharedaddy"><?php echo do_shortcode('[acf_with_latex field="short_note"]'); ?></div>
			<?php endif; ?> -->

						<?php if ($short_note = get_field('short_note')) : ?>
							<div class="short-note hide-sharedaddy">
								<?php echo do_shortcode($short_note); ?>
							</div>
						<?php endif; ?>

					</div>
					<?php get_template_part('template-parts/section', 'post-feature-image'); ?>
					<div class="maxw-720 content-area mt-5">
						<?php the_content(); ?>
					</div>
					<div class="dynamic-content-area">
						<?php get_template_part('template-parts/section', 'post-key-takeawys'); ?>
						<?php get_template_part('template-parts/section', 'post-activitysheet'); ?>
						<?php get_template_part('template-parts/section', 'post-editornote'); ?>
						<?php get_template_part('template-parts/section', 'post-reference'); ?>
						<?php get_template_part('template-parts/section', 'related-links'); ?>
						<!-- this is feed back form we commented through client requirment -->
						<!-- <?php get_template_part('template-parts/section', 'post-feedbackform'); ?> -->

					</div>


					<div class="print-no">
						<div class="maxw-720"><?php get_template_part('template-parts/share-download'); ?></div>
					</div>

					<div class="maxw-720 apum-commentsection print-no">
						<?php if (comments_open() || get_comments_number()) comments_template(); ?>
					</div>

					<?php get_template_part('template-parts/section', 'post-navigation'); ?>
				</article>
		<?php endwhile;
		endif; ?>
	</div>
</section>

<?php get_template_part('template-parts/section', 'post-related-articles'); ?>
<?php get_template_part('template-parts/section', 'info-cards'); ?>

<script type="text/x-mathjax-config">
	MathJax.Hub.Config({
    showMathMenu: false
  });
</script>

<?php get_footer(); ?>