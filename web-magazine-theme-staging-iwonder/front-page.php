<?php
/**
 * Template Name: Front Page
 */
 
// header here
get_header(); ?>
 <main id="main-content">
<!-- 	resources section start here -->
	<?php get_template_part( 'template-parts/section', 'article-components' ); ?>

<!-- 	resources section start here -->
	<?php get_template_part( 'template-parts/section', 'resource-components' ); ?>

<!-- 	magzineissue section start here -->
	<?php get_template_part( 'template-parts/section', 'magzineissue-components' ); ?>

<!-- 	othermagzine section start here -->
	<?php get_template_part( 'template-parts/section', 'othermagzine-components' ); ?>


<!-- Info card section -->
<?php get_template_part( 'template-parts/section', 'info-cards' ); ?>



<!--   	footer here -->
</main >
<?php get_footer(); 