<footer class="footer text-white footerbar-img left pb-0 testfooter<?= is_page('home') ? 'mt-5' : ''; ?>">

	<div class="winmils-container d-flex justify-content-between position-absolute px-4 w-100">
		<img src="/wp-content/uploads/2025/12/windmill2.svg" class="windmils-left" alt="windmils">
		<img src="/wp-content/uploads/2025/12/windmill3.svg" class="windmils-img" alt="">
	</div>
	
	<svg 
    xmlns="http://www.w3.org/2000/svg" 
    viewBox="0 0 1440 320"
    preserveAspectRatio="none"
		 class="wave-image"
   
       
    
>
    <path 
        d="M0,160L48,160C96,160,192,160,288,149.3C384,139,480,117,576,101.3C672,85,768,75,864,80C960,85,1056,107,1152,112C1248,117,1344,107,1392,101.3L1440,96L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"
        
    ></path>
</svg>
	
	
	
	
	<!-- Get in touch section -->
<?php get_template_part( 'template-parts/section', 'get-in-touch' ); ?>
	
	
    <div class="container bg-image-left apum-animation-fadeInUp pt-0 pt-md-5">    
        <!-- Footer Navigation -->
        <div class="row nav-top-border g-0 px-3 px-xxl-0 position-relative z-2 <?= !is_page('home') ? 'pt-5 pt-md-0' : ''; ?>">
		
			<!-- Post Categories -->
			<div class="col-md-3 d-flex flex-column gap-16">
				<p class="text-uppercase footer-menu-title">
					<a href="<?php echo esc_url(home_url('/' . t('iw_articles_slug') . '/')); ?>">
						<?php echo esc_html(t('iw_articles')); ?>
					</a>
				</p>
				<ul class="list-unstyled d-flex flex-column gap-12">
	<?php
	$default_lang = apply_filters('wpml_default_language', null);
	$current_lang = apply_filters('wpml_current_language', null);

	do_action('wpml_switch_language', $default_lang);

	$default_categories = get_terms([
		'taxonomy'   => 'category',
		'hide_empty' => false,
		'orderby'    => 'meta_value_num',
		'order'      => 'ASC',
		'meta_key'   => 'cat_order', // ⭐ your custom order
	]);

	do_action('wpml_switch_language', $current_lang);

	foreach ($default_categories as $default_cat) {

		if (intval($default_cat->term_id) === 1) continue;

		$translated_id  = apply_filters('wpml_object_id', $default_cat->term_id, 'category', false, $current_lang);
		$translated_cat = $translated_id ? get_term($translated_id, 'category') : null;

		$term = (!is_wp_error($translated_cat) && $translated_cat) ? $translated_cat : $default_cat;

		echo '<li><a href="' . esc_url(get_category_link($term->term_id)) . '" class="text-white fm-' . esc_html(strtolower(str_replace(' ', '', $term->name))) . '">' . esc_html($term->name) . '</a></li>';
        
	}
	?>
</ul>


			</div>

            <!-- Resource Categories -->
            <div class="col-md-3 d-flex flex-column gap-16">
                <p class="text-uppercase footer-menu-title">
                    <a href="<?php echo esc_url(home_url('/resources/')); ?>">
						<?php echo esc_html( t('iw_resources') ); ?>
                    </a>
                </p>
                <ul class="list-unstyled d-flex flex-column gap-12">
            <?php
$resource_categories = get_terms([
    'taxonomy'   => 'resource_categories',
    'hide_empty' => false,
    'orderby'    => 'term_id',
    'order'      => 'ASC',
]);

if (!is_wp_error($resource_categories) && !empty($resource_categories)) {
    foreach ($resource_categories as $term) {

        $term_link = get_term_link($term);

        // ✅ Check for WP_Error before using esc_url
        if (!is_wp_error($term_link)) {
            echo '<li><a href="' . esc_url($term_link) . '" class="text-white fm-' .
                esc_attr(strtolower(str_replace(' ', '', $term->name))) . '">' .
                esc_html($term->name) . '</a></li>';
        }
    }
}
?>
<li><a href="<?php echo esc_url(home_url( '/' . t('iw_magazineissues_slug') . '/' )); ?>" class="text-white fw-bold text-uppercase footer-menu-title magazine-issues"><?php echo esc_html( t('iw_magazine_issues') ); ?></a></li>
                </ul>
            </div>

            <div class="col-md-3 d-flex flex-column gap-16">
                <p class="footer-menu-title connect-title"><?php echo esc_html( t('iw_more') ); ?></p>
                <ul class="list-unstyled d-flex flex-column gap-12">
                    <li class="d-none"><a href="<?php echo esc_url(home_url('/resources/')); ?>">
						<?php echo esc_html( t('iw_resources') ); ?>
                    </a></li>
                    <li class="d-none"><a href="<?php echo esc_url(home_url('/')); ?>" class="text-white"><?php echo esc_html( t('iw_home') ); ?></a></li>
                    <li class="d-none"><a href="<?php echo esc_url( home_url( '/' . t('iw_resources_slug') . '/' ) ); ?>" class="text-white"><?php echo esc_html( t('iw_resources') ); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url( '/' . t('iw_aboutus_slug') . '/' )); ?>" class="text-white"><?php echo esc_html( t('iw_about_us') ); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url( '/' . t('iw_authors_slug') . '/' )); ?>" class="text-white"><?php echo esc_html( t('iw_authors') ); ?></a></li>
                     <li class="d-none"><a href="<?php echo esc_url(home_url( '/' . t('iw_callforarticles_slug') . '/' )); ?>" class="text-white"><?php echo esc_html( t('iw_call_for_articles') ); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url( '/' . t('iw_magazineguidelines_slug') . '/' )); ?>" class="text-white"><?php echo esc_html( t('iw_magazine_guidelines') ); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url( '/' . t('iw_contactus_slug') . '/' )); ?>" class="text-white"><?php echo esc_html( t('iw_contact_us') ); ?></a></li>
        
                </ul>
            </div>

            <div class="col-md-3 d-flex flex-column gap-16 d-none">
                <p class="footer-menu-title d-none d-md-block"> </p>
                <ul class="list-unstyled d-flex flex-column gap-12">
                                <li><a href="<?php echo esc_url(home_url( '/' . t('iw_magazineissues_slug') . '/' )); ?>" class="text-white"><?php echo esc_html( t('iw_magazine_issues') ); ?></a></li>
                     <li><a href="<?php echo esc_url(home_url( '/' . t('iw_callforarticles_slug') . '/' )); ?>" class="text-white"><?php echo esc_html( t('iw_call_for_articles') ); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url( '/' . t('iw_magazineguidelines_slug') . '/' )); ?>" class="text-white"><?php echo esc_html( t('iw_magazine_guidelines') ); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url( '/' . t('iw_contactus_slug') . '/' )); ?>" class="text-white"><?php echo esc_html( t('iw_contact_us') ); ?></a></li>
                </ul>
            </div>

            <div class="col-md-3 d-flex flex-column gap-16">
                <p class="footer-menu-title"><?php echo esc_html( t('iw_other_magazines') ); ?></p>
                <ul class="list-unstyled d-flex flex-column gap-12">
                    <li><a href="<?php echo esc_html(t('iw_pathshala_bheetar_aur_baahar')); ?>" target="_blank" rel="noopener noreferrer" class="text-white"> <?php echo esc_html( t('iw_pathshala') ); ?></a></li>
                    <li><a href="<?php echo esc_html(t('iw_at-right-angles')); ?>" target="_blank" rel="noopener noreferrer" class="text-white"><?php echo esc_html( t('iw_atright') ); ?></a></li>
                </ul>
            </div>

        </div>
    </div>
<!-- 	footer image start here  -->
	
            <?php get_template_part('assets/images/footer-img'); ?>
	
<!-- 	footer image end here  -->
	
           <div class="footer-bottom-parent pb-48 px-3 px-xxl-0">
			    <div class="container footer-bottom text-center d-flex flex-md-row flex-column justify-content-between align-items-center pt-3 line-gradint">
                <div class="small mt-2 d-flex gap-16">
                    <a href="<?php echo esc_url(home_url('/' . t('iw_disclaimer_slug') . '/')); ?>" class="text-white"><?php echo esc_html( t('iw_disclaimer') ); ?></a>
                    <a href="<?php echo esc_url(home_url('/' . t('iw_privacypolicy_slug') . '/')); ?>" class="text-white"><?php echo esc_html( t('iw_privacy_policy') ); ?></a>
                </div>
                <div class="small mt-2">© <?php echo date('Y'); ?> <?php echo esc_html( t('iw_azim_premji_university') ); ?></div>
				

                <!-- Social Media Icons -->
                <div class="social-icons mt-2 d-flex gap-16">
               <a href="http://youtube.com/user/AzimPremjiUniversity"
   target="_blank"
   rel="noopener noreferrer"
   aria-label="Visit our YouTube channel"
   title="YouTube"><i class="fa-brands fa-youtube"></i></a>
                   
                    
                    <a href="https://www.facebook.com/azimpremjiuniversity/" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_html( t('iw_facebook') ); ?>"><i class="fa-brands fa-facebook-f" ></i></a>
                    <a href="https://x.com/azimpremjiuniv" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_html( t('iw_twitter') ); ?>"><i class="fa-brands fa-x-twitter" ></i></a>
                    <a href="https://www.instagram.com/azimpremjiuniv/#" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_html( t('iw_instagram') ); ?>"><i class="fa-brands fa-instagram" ></i></a>
                    <a href="https://in.linkedin.com/school/azimpremjiuniversity/" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_html( t('iw_linkedin') ); ?>"><i class="fa-brands fa-linkedin-in"></i></a>
<!-- 					<a href="https://wa.me/918951782383" class="fs-20" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_html( t('iw_whatsapp') ); ?>"><i class="fa-brands fa-whatsapp" ></i></a> -->
                </div>
				<p class="small mt-2 hide">
					<?php echo esc_html( t('iw_follow_us_on') ); ?> <a class="text-decoration-underline" href="https://azimpremjiuniversity.edu.in/" target="_blank" rel="noopener noreferrer"><?php echo esc_html( t('iw_azim_premji_university') ); ?></a>
				</p>
            </div>
	</div>
</footer>
<?php get_template_part('template-parts/section', 'scrolltopbtn'); ?>
<?php wp_footer(); ?>
</body>

</html>