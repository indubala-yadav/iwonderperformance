<?php
/**
 * Template Part: Get in Touch Section (Static)
 * linebar-img
 */
?>

<?php if(is_page('home')):?>
<section class="concrete-bg position-relative apum-section svg-grey pt-0 pb-5 px-3">
    <div class="container apum-animation-fadeInUp">
        <div class="row flex-lg-row  g-4 flex-column mob-gap">

            <!-- Left Section: Contact Info -->
            <div class="col-12 col-lg-4 d-flex getin-touch-section flex-column gap-2 justify-content-center">
                <h2 class="fw-semi-bold title-area add-motif gap-24 Ls-8 fs-24-40">
                    <?php echo esc_html(t('iw_get_in_touch')); ?>
                </h2>

                <!-- Paragraph Content -->
                <p class="d-flex flex-column gap-24 fs-18 mb-0">
                    <span class="d-block">
                        <?php echo wp_kses_post(t('iw_getin_touch_paraone')); ?><br>
                        <a href="mailto:iwonder.editor@apu.edu.in" target="_blank"><u>iwonder@apu.edu.in.</u></a>
                    </span>
                    <span class="d-block">
                        <?php echo wp_kses_post(t('iw_getin_touch_paratwo')); ?>
                        <a href="mailto:publications@apu.edu.in" target="_blank"><u>publications@apu.edu.in.</u></a>
                    </span>
                </p>
				 <div>
                    <h3 class="fw-bold mt-3 ls-1 text-uppercase fs-20 ">
                        <?php echo esc_html(t('iw_address')); ?>
                    </h3>
                    <address class="mb-0 mt-2">
<!--                         <strong><?php echo esc_html(t('iw_azim_premji_university')); ?></strong><br> -->
                        <?php echo esc_html(t('iw_burugunte_village')); ?><br>
                        <?php echo esc_html(t('iw_sarjapur_attibele_road')); ?><br>
                        <?php echo esc_html(t('iw_bengaluru_562125')); ?>
                    </address>
                </div> 

                <!-- Email (Hidden) -->
                <p class="mb-0 d-none">
                    <?php echo esc_html(t('iw_email')); ?>
                    <a href="mailto:reach@apu.edu.in" target="_blank" class="text-dark">
                        reach@apu.edu.in
                    </a>
                </p>
            </div>

            <!-- Right Section: Contact Form and Address -->
            <div class="col-12 col-lg-8">
                <!-- Address -->
             <div class="d-none">
                    <h3 class="fw-bold mt-3 ls-1 text-uppercase fs-20 ">
                        <?php echo esc_html(t('iw_address')); ?>
                    </h3>
                    <address class="mb-0 mt-3">
                        <strong><?php echo esc_html(t('iw_azim_premji_university')); ?></strong><br>
                        <?php echo esc_html(t('iw_burugunte_village')); ?><br>
                        <?php echo esc_html(t('iw_sarjapur_attibele_road')); ?><br>
                        <?php echo esc_html(t('iw_bengaluru_562125')); ?>
                    </address>
                </div> 

                <!-- Contact Form (Language-based) -->

	<div class="getintouch-cards d-flex flex-md-row flex-column gap-32 align-items-stretch w-100 h-100">

    <!-- Card 1 -->
    <div class="touch-card bg-light-green px-4 py-4 rounded-custom flex-fill d-flex flex-column h-100 col-md-6">

        <img src="/wp-content/uploads/2026/04/get-in-touch-stay-informed.png"
             alt=""
             class="img-fluid d-block mb-4"
             >

        <h3 class="fs-20 fw-bold text-uppercase let-spac-1 mb-3 text-center"> <?php echo esc_html(t('iw_stay_informed')); ?></h3>

        <!-- Make content stretch equally -->
        <p class="mb-3 fs-18 flex-grow-1 text-center">
			 <?php echo esc_html(t('iw_stay_informed_box_para')); ?>

        </p>

        <button class="btn btn-primary btn-animate mt-3 m-auto" onclick="window.open('https://forms.office.com/pages/responsepage.aspx?id=OK6aNP56KkW1fkXOvQx6UF8iVSto8sRBlbbPR2QdPhlUOUtEWUYzTVNNN1lLMjFaRUFRMUlLVzQyOS4u&route=shorturl', '_blank')"
         role="button" >
            <span> <?php echo esc_html(t('iw_subscribe_for_free')); ?></span>
            <i class="fas fa-arrow-right"></i>
        </button>

    </div>
    <!-- Card 2 -->
    <div class="touch-card bg-light-orange px-4 py-4 rounded-custom flex-fill d-flex flex-column h-100 col-md-6">

        <img src="/wp-content/uploads/2026/04/get-in-submit-a-pich.png"
             alt="Submit an Article"
             class="img-fluid d-block mb-4"
             >
        <h3 class="fs-20 fw-bold text-uppercase let-spac-1 mb-3 text-center"><?php echo esc_html(t('iw_Submit_a_Pitch_or_Draft')); ?></h3>

        <!-- Make description stretch equal -->
        <p class="mb-3 fs-18 flex-grow-1 text-center">
  <?php echo esc_html(t('iw_Submit_a_Pitch_or_Draft_para')); ?>
        </p>

        <button class="btn btn-primary btn-animate mt-3 m-auto"
           onclick="window.location.href='<?php echo esc_html(t('iw_callforarticles_slug')); ?>/#submitarticle'"
           role="button">
            <span><?php echo esc_html(t('iw_submit')); ?></span>
            <i class="fas fa-arrow-right" ></i>
        </button>

    </div>
</div>


            </div>

        </div>
    </div>
</section>
<?php endif;?>