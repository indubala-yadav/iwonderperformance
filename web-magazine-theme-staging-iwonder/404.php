<?php get_header(); ?>


<div class="container-fluid p-0 position-relative apum-innerpage-section error-page" > 
	<div class="theme-top-ct apply-top-pattern apply-bottom-pattern pattern-contrast i-wonder-theme-top-ct"></div>
	<main id="primary" class="apum-section position-relative section-pad pt-4">

<div class="container apum-innerpage-body bg-white pt-80 pb-0 rounded h-100 text-center" 
     style="background-image: url('/wp-content/uploads/2025/06/404error-1.png'); background-size: contain; background-position: center;">


			<div class="maxw-720 content-area">

			<h1>
				404
				</h1>
				
				<p>
					<span><?php echo esc_html( t('iw_Page_not_found') ); ?></span>
				</p>

			<?php
			$translated_home_url = apply_filters('wpml_home_url', home_url());
			?>

			<a href="<?php echo esc_url($translated_home_url); ?>" class="btn btn-primary btn-animate">
				<i class="fas fa-arrow-left mt-1"></i>
				<span><?php echo esc_html( t('iw_go_back_home') ); ?></span>
			</a>

</div>
		</div>
	
	</main><!-- #primary -->
 </div>
<?php get_footer(); ?>
