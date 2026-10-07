<?php
/*
 othermagazine component section
*/
?>

<!-- Other Magazines -->
<section class="section-radius other-magazine-section px-3 position-relative apum-section pt-0 right svg-Maverick-top svg-Maverick-bottom">   <!-- removed bg-color	 -->
    <div class="container apum-animation-fadeInUp">
        <div class="title-area add-motif mb-5">
            <h2 class="fs-20-24 text-uppercase fw-bold"><?php echo esc_html( t('iw_other_magazines_from') ); ?></h2>
            <h3 class="fs-24-32"><strong><?php echo esc_html( t('iw_azim_premji_university') ); ?></strong></h3>
        </div>
        
        <div class="apum-carousel d-flex justify-content-center flex-column flex-md-row gap-32">
            <a href="<?php echo esc_html(t('iw_pathshala_bheetar_aur_baahar')); ?>" 
               title="<?php echo esc_attr( t('iw_go_to_pathshala') ); ?>"  
               target="_blank" 
               rel="noopener noreferrer"
               class="p-4 rounded btm-slide carousel-item-wrapper d-flex gap-32" 
               style="background-color: #DBDDFF;flex: 1;">
                <img 
                    src="<?php echo esc_url(t('iw_othermagazines_pathsala'))?>"  
                    alt="<?php echo esc_attr( t('iw_pathshala_magazine_cover') ); ?>" 
                    class="img-fluid h-100 magazine-image rounded-3 w-50" 
                    loading="lazy" />
                
                <div class="d-flex flex-column gap-8 custom-grid">
                    <h3 class="fw-semibold fs-16-20 lh-base mb-0 title">
                        <?php echo esc_html( t('iw_pathshala') ); ?>
                    </h3>
                    <p class="fs-16 lh-sm mb-0"><?php echo esc_html( t('iw_bheetar_aur_bahar') ); ?></p>
                    <div class="btn btn-white btn-animate">
                        <span><?php echo esc_html( t('iw_visit') ); ?></span>
                         
                        <i class="fa-sharp fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i>
</div>
                </div>
            </a>
            
            <a href="https://azimpremjiuniversity.edu.in/iwonder..." 
               target="_blank" 
               rel="noopener noreferrer"
               title="<?php echo esc_attr( t('iw_go_to_i_wonder') ); ?>" 
               class="p-4 rounded btm-slide carousel-item-wrapper d-flex gap-32 d-none" 
               style="background-color:#FFF8CC;flex: 1;">
                <img 
                    src="https://placehold.co/600x300/cccccc/1f4d7c?text=Other Magazines issue" 
                    alt="<?php echo esc_attr( t('iw_i_wonder_magazine_cover') ); ?>" 
                    class="img-fluid h-100 magazine-image rounded-3 w-50" 
                    loading="lazy" />
                
                <div class="d-flex flex-column gap-8 custom-grid">
                    <h3 class="fw-semibold fs-16-20 lh-base mb-0">
                        <?php echo esc_html( t('iw_i_wonder') ); ?>
                    </h3>
                    <p class="fs-16 lh-sm mb-0"><?php echo esc_html( t('iw_rediscovering_school_science') ); ?></p>
					
                    <div class="btn btn-white btn-animate">
                        <span>
                            <?php echo esc_html( t('iw_visit') ); ?>

        </span>
                        <i class="fa-sharp fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i>
</div>
                </div>
            </a>
  <a href="<?php echo esc_html(t('iw_at-right-angles')); ?>" 
               title="<?php echo esc_attr( t('iw_go_to_pathshala') ); ?>"  
               target="_blank" 
               rel="noopener noreferrer"
               class="p-4 rounded btm-slide carousel-item-wrapper d-flex gap-32" 
               style="background-color: #BBD0FF;flex: 1;">
                <img
  src="<?php echo esc_url(t('iw_othermagazines_atrightangle'))?>" 
                    alt="<?php echo esc_attr( t('iw_atightangle') ); ?>" 
                    class="img-fluid h-100 magazine-image rounded-3 w-50" 
                    loading="lazy" />
                
                <div class="d-flex flex-column gap-8 custom-grid">
                    <h3 class="fw-semibold text-capitalize fs-16-20 lh-base mb-0 title">
              <?php echo esc_html( t('iw_atightangle') ); ?>
                    </h3>
                     <p class="fs-16 lh-sm mb-0"><?php echo esc_html( t('iw_rediscovering_school_science') ); ?></p>
					
                    <div class="btn btn-white btn-animate">
                       <span>
                            <?php echo esc_html( t('iw_visit') ); ?></span>
                        <i class="fa-sharp fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i>
</div>
                </div>
            </a>
             <a href="https://azimpremjiuniversity.edu.in/learning-curve" 
               title="<?php echo esc_attr( t('iw_go_to_pathshala') ); ?>"  
               target="_blank" 
               rel="noopener noreferrer"
               class="p-4 rounded btm-slide carousel-item-wrapper d-flex gap-32 d-none" 
               style="background-color: #DFFFE0;flex: 1;">
                <img
  src="https://placehold.co/268x358/cccccc/1f4d7c?text=Other Magazines issue" 
                    alt="<?php echo esc_attr( t('iw_pathshala_magazine_cover') ); ?>" 
                    class="img-fluid h-100 magazine-image rounded-3 w-50" 
                    loading="lazy" />
                
                <div class="d-flex flex-column gap-8 custom-grid">
                    
                    <p  class="fs-16 lh-sm mb-0">Lorem ipsum dolor sit.</p>
					<h3 class="fw-semibold fs-16-20 lh-base mb-0">
              <?php echo esc_html( t('iw_learning_curve') ); ?>
                    </h3>
                    <div class="btn btn-white btn-animate">
                       <span>
                            <?php echo esc_html( t('iw_visit') ); ?></span>
                        <i class="fa-sharp fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i>
</div>
                </div>
            </a>
        </div>
    </div>
</section>