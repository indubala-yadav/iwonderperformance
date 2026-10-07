<?php
$sections = [
    'chief_editor' => esc_html( t('iw_chief_editor') ),
    // 'associate_editor' => esc_html( t('iw_associate_editor') ),
    'chief_communications_officer' => esc_html( t('iw_chief_communications_officer_managing_editor') ),
    'editorial_office' => esc_html( t('iw_editorial_office') ),
    'web_editor' => esc_html( t('iw_web_editor') ),
    'editorial_member' => esc_html( t('iw_editorial_team') ),
    'editorial_member' => esc_html( t('iw_editorial_committee') ),
    'managing_editor' => esc_html( t('iw_managing_editor') ),
    'translations_editors' => esc_html( t('iw_translations_editors') ),
    'publications_team' => esc_html( t('iw_publications_team') ),
    'illustrations_and_artwork' => esc_html( t('Illustrations and Artwork ') ),
	'student_intern' => esc_html( t('iw_student_intern') ),
];

function get_editorial_members($designation) {
    // Get current language (e.g., 'en', 'hi', 'kn')
    $current_lang = apply_filters('wpml_current_language', NULL);

    return get_posts([
        'post_type' => 'editorial_team',
        'posts_per_page' => -1,
        'orderby' => 'title',
        'order' => 'ASC',
        'suppress_filters' => false, // ensure WPML filters apply
        'lang' => $current_lang,
        'meta_query' => [[
            'key' => 'editorial_member_designation',
            'value' => $designation,
            'compare' => '='
        ]]
    ]);
}
?>
<h2 class="add-motif"><?php echo esc_attr( t('iw_editorial_team') ); ?>:</h2>

<div id="editorial-team" class="container mt-5 mb-0 apum-editorail-section">
    <div class="row">
        <?php foreach ($sections as $key => $label): ?>

            <?php if (in_array($key, ['chief_editor', 'chief_communications_officer'])): ?>
                <!-- Chief, Associate, Web Editors -->
                <div class="col-md-6 mb-5">
                    <div class="card h-100 border-0">
                        <div class="card-body p-0">
                            <h2 class="card-title fs-16 text-uppercase fw-bold mb-3 let-spac-1"><?php echo esc_html($label); ?></h2>
                            <?php 
                            $members = get_editorial_members($key);
                            if ($members): 
                                $member = $members[0];
                                $email = get_field('editorial_member_email', $member->ID);
                                $bio = apply_filters('the_content', $member->post_content);
                                $modal_id = 'modal_' . $member->ID;
                            ?>
                                <h3 class="mb-0 fs-18 fw-medium let-spac-0">
                                    <a href="#" data-bs-toggle="modal" data-bs-target="#<?php echo esc_attr($modal_id); ?>">
                                        <span><?php echo esc_html($member->post_title); ?></span>
										<i class="fa-solid fa-info-circle"></i>
										</a>
                                </h3>
                                <a href="mailto:<?php echo esc_attr($email); ?>" target="_blank"><?php echo esc_html($email); ?></a>

                                <!-- Modal -->
                                <div class="modal fade" id="<?php echo esc_attr($modal_id); ?>" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-scrollable modal-lg modal-dialog-centered">
                                        <div class="modal-content apu-editorteam-model">
											
                                            <div class="model-body">
												<div class="model-head flex-basis-68">
                                                <h5 class="modal-title mb-0"><?php echo esc_html($member->post_title); ?></h5>
												<p class="model-authoremail"><a href="mailto:<?php echo esc_attr($email); ?>"><span><?php echo esc_html($email); ?></span></a></p>	
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?php echo esc_attr( t('iw_close') ); ?>
"></button>
												 <?php echo $bio; ?>
												
                                            </div>
                                               
                                            </div>
											
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

            <?php elseif ($key === 'editorial_office'): ?>
                <!-- Editorial Office -->
                <div class="col-md-6 mb-5">
                    <div class="card h-100 border-0">
                        <div class="card-body p-0">
                            <h2 class="card-title fs-16 text-uppercase fw-bold mb-3 let-spac-1"><?php echo esc_html($label); ?></h2>
                            <p class="fs-16"><?php echo esc_html( t('iw_publications_azim_premji_university') ); ?>
</p>
                            <a href="mailto:publications@apu.edu.in" target="_blank"><?php echo esc_html( t('iw_publications_apu_edu_in') ); ?>
</a>
                            <a class="link-underline-primary line-animation" href="https://www.azimpremjiuniversity.edu.in" target="_blank"><span><?php echo esc_html( t('iw_www_azimpremjiuniversity_edu_in') ); ?></span></a>
							
                        </div>
                    </div>
                </div>

                <?php elseif ($key === 'web_editor'): ?>
                <!-- Editorial Office -->
                <div class="col-md-6 mb-5">
                   <div class="card h-100 border-0">
                        <div class="card-body p-0">
                            <h2 class="card-title fs-16 text-uppercase fw-bold mb-3 let-spac-1"><?php echo esc_html($label); ?></h2>
                            <?php 
                            $members = get_editorial_members($key);
                            if ($members): 
                                foreach ($members as $member):
                                $email = get_field('editorial_member_email', $member->ID);
                                $bio = apply_filters('the_content', $member->post_content);
                                $modal_id = 'modal_' . $member->ID;
                            ?>
                                <h3 class="mb-0 fs-18 fw-medium let-spac-0">
                                    <a href="#" data-bs-toggle="modal" data-bs-target="#<?php echo esc_attr($modal_id); ?>">
                                        <span><?php echo esc_html($member->post_title); ?></span>
										<i class="fa-solid fa-info-circle"></i>
										</a>
                                </h3>
                                <a href="mailto:<?php echo esc_attr($email); ?>" target="_blank"><?php echo esc_html($email); ?></a>

                                <!-- Modal -->
                                <div class="modal fade" id="<?php echo esc_attr($modal_id); ?>" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-scrollable modal-lg modal-dialog-centered">
                                        <div class="modal-content apu-editorteam-model">
											
                                            <div class="model-body">
												<div class="model-head flex-basis-68">
                                                <h5 class="modal-title mb-0"><?php echo esc_html($member->post_title); ?></h5>
												<p class="model-authoremail"><a href="mailto:<?php echo esc_attr($email); ?>"><span><?php echo esc_html($email); ?></span></a></p>	
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?php echo esc_attr( t('iw_close') ); ?>
"></button>
												 <?php echo $bio; ?>
												
                                            </div>
                                               
                                            </div>
											
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; endif; ?>
                        </div>
                    </div>
                </div>

            <?php elseif ($key === 'editorial_member' || $key === 'publications_team' ): ?>
                <!-- Editorial Team & Publications Team -->
                <div class="col-12 mb-5">
                    <div class="card border-0">
                        <div class="card-body p-0">
                            <h2 class="card-title fs-16 text-uppercase fw-bold mb-3 let-spac-1"><?php echo esc_html($label); ?></h2>
                            <div class="row">
                                <?php 
                                $members = get_editorial_members($key);
                                if ($members):
                                    foreach ($members as $member):
                                        $email = get_field('editorial_member_email', $member->ID);
                                        $bio = apply_filters('the_content', $member->post_content);
                                        $modal_id = 'modal_' . $member->ID;
                                ?>
                                    <div class="col-md-6 mb-3">
                                        <h3 class="mb-0 fs-18  fw-medium let-spac-0">
                                            <a href="#" data-bs-toggle="modal" data-bs-target="#<?php echo esc_attr($modal_id); ?>">
                                               <span><?php echo esc_html($member->post_title); ?></span>
											<i class="fa-solid fa-info-circle"></i>
                                            </a>
                                        </h3>
                                        <?php if ($email): ?>
                                            <a href="mailto:<?php echo esc_attr($email); ?>" target="_blank"><span><?php echo esc_html($email); ?></span></a>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Modal -->
                                    <div class="modal fade" id="<?php echo esc_attr($modal_id); ?>" tabindex="-1">
                                        <div class="modal-dialog modal-dialog-scrollable modal-lg modal-dialog-centered">
                                            <div class="modal-content apu-editorteam-model">
											
                                            <div class="model-body">
                                             
												<div class="model-head flex-basis-68">
                                                <h5 class="modal-title mb-0"><span><?php echo esc_html($member->post_title); ?></span></h5>
												<p class="model-authoremail"><a href="mailto:<?php echo esc_attr($email); ?>" target="_blank"><span><?php echo esc_html($email); ?></span></a></p>	
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?php echo esc_attr( t('iw_close') ); ?>
"></button>
												 <?php echo $bio; ?>
												
                                            </div>
                                               
                                            </div>
											
                                        </div>
                                        </div>
                                    </div>
                                <?php endforeach; endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

<?php elseif ($key === 'managing_editor' ||  $key === 'illustrations_and_artwork'): ?>
                <!-- Translations Editors -->
                <div class="col-6 translations-editors">
                    <div class="card border-0">
                        <div class="card-body p-0">
                            <h2 class="card-title fs-16 text-uppercase fw-bold mb-3 let-spac-1 mh-44"><?php echo esc_html($label); ?></h2>
                            <div class="row">
                                <?php 
                                $members = get_editorial_members($key);
                                if ($members):
                                    foreach ($members as $member):
                                        $email = get_field('editorial_member_email', $member->ID);
                                        $bio = apply_filters('the_content', $member->post_content);
                                        $modal_id = 'modal_' . $member->ID;
                                ?>
                                    <div class="col-md-12 mb-3">
                                        <h3 class="mb-0 fs-18">
                                            <a href="#" data-bs-toggle="modal" data-bs-target="#<?php echo esc_attr($modal_id); ?>">
                                                <span><?php echo esc_html($member->post_title); ?></span>
												<i class="fa-solid fa-info-circle"></i>
                                            </a>
                                        </h3>
                                        <?php if ($email): ?>
                                            <a class="mb-2 d-block" href="mailto:<?php echo esc_attr($email); ?>" target="_blank"><span><?php echo esc_html($email); ?></span></a>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Modal -->
                                    <div class="modal fade" id="<?php echo esc_attr($modal_id); ?>" tabindex="-1" aria-labelledby="<?php echo esc_attr($modal_id); ?>Label" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-scrollable modal-lg modal-dialog-centered">
                                         <div class="modal-content apu-editorteam-model">
											
                                            <div class="model-body">
                                             <!-- <div class="flex-basis-32">
											<img src="https://placehold.co/600x400/cccccc/1f4d7c?text=translations editor" alt="<?php echo esc_attr( t('iw_author_placeholder') ); ?>
" class="rounded-1 objectfit-cover" width="220" height="220">
												</div> -->
												<div class="model-head flex-basis-68">
                                                <h5 class="modal-title mb-0" id="<?php echo esc_attr($modal_id); ?>Label"><span><?php echo esc_html($member->post_title); ?></span></h5>
												<p class="model-authoremail"><a href="mailto:<?php echo esc_attr($email); ?>"><span><?php echo esc_html($email); ?></span></a></p>	
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?php echo esc_attr( t('iw_close') ); ?>
"></button>
												 <?php echo $bio; ?>
												
                                            </div>
                                               
                                            </div>
											
                                        </div>
                                        </div>
                                    </div>
                                <?php endforeach; endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php elseif ($key === 'chief_communications_officer'): ?>
                <!-- Communications Officer -->
                <div class="col-md-6 mb-5">
                    <div class="card h-100 border-0">
                        <div class="card-body p-0">
                            <h2 class="card-title fs-16 text-uppercase fw-bold mb-3 let-spac-1"><?php echo esc_html($label); ?></h2>
                            <?php 
                            $members = get_editorial_members($key);
                            if ($members): 
                                $member = $members[0];
                                $email = get_field('editorial_member_email', $member->ID);
                                $bio = apply_filters('the_content', $member->post_content);
                                $modal_id = 'modal_' . $member->ID;
                            ?>
                                <h3 class="mb-0 fs-18 fw-medium let-spac-0">
                                    <a href="#" data-bs-toggle="modal" data-bs-target="#<?php echo esc_attr($modal_id); ?>">
                                        <span><?php echo esc_html($member->post_title); ?></span>
									<i class="fa-solid fa-info-circle"></i>
                                    </a>
                                </h3>
                                <a class="mb-2 d-block" href="mailto:<?php echo esc_attr($email); ?>"><span><?php echo esc_html($email); ?></span></a>
                               
                                <!-- Modal -->
                                <div class="modal fade" id="<?php echo esc_attr($modal_id); ?>" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-scrollable modal-lg modal-dialog-centered">
                                      <div class="modal-content apu-editorteam-model">
											
                                            <div class="model-body">
                                             
												<div class="model-head flex-basis-68">
                                                <h5 class="modal-title mb-0"><span><?php echo esc_html($member->post_title); ?></span></h5>
												<p class="model-authoremail"><a href="mailto:<?php echo esc_attr($email); ?>"><span><?php echo esc_html($email); ?></span></a></p>	
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?php echo esc_attr( t('iw_close') ); ?>
"></button>
												 <?php echo $bio; ?>
												
                                            </div>
                                               
                                            </div>
											
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
		
		 <?php elseif ($key === 'student_intern'): ?>
                <!-- Communications Officer -->
                <div class="col-md-6 mb-5">
                    <div class="card h-100 border-0">
                        <div class="card-body p-0">
                            <h2 class="card-title fs-16 text-uppercase fw-bold mb-3 let-spac-1"><?php echo esc_html($label); ?></h2>
                            <?php 
                            $members = get_editorial_members($key);
                            if ($members): 
                                $member = $members[0];
                                $email = get_field('editorial_member_email', $member->ID);
                                $bio = apply_filters('the_content', $member->post_content);
                                $modal_id = 'modal_' . $member->ID;
                            ?>
                                <h3 class="mb-0 fs-18 fw-medium let-spac-0">
                                    <a href="#" data-bs-toggle="modal" data-bs-target="#<?php echo esc_attr($modal_id); ?>">
                                        <span><?php echo esc_html($member->post_title); ?></span>
									<i class="fa-solid fa-info-circle"></i>
                                    </a>
                                </h3>
                                <a class="mb-2 d-block" href="mailto:<?php echo esc_attr($email); ?>"><span><?php echo esc_html($email); ?></span></a>
                               
                                <!-- Modal -->
                                <div class="modal fade" id="<?php echo esc_attr($modal_id); ?>" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-scrollable modal-lg modal-dialog-centered">
                                      <div class="modal-content apu-editorteam-model">
											
                                            <div class="model-body">
                                             
												<div class="model-head flex-basis-68">
                                                <h5 class="modal-title mb-0"><span><?php echo esc_html($member->post_title); ?></span></h5>
												<p class="model-authoremail"><a href="mailto:<?php echo esc_attr($email); ?>"><span><?php echo esc_html($email); ?></span></a></p>	
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?php echo esc_attr( t('iw_close') ); ?>
"></button>
												 <?php echo $bio; ?>
												
                                            </div>
                                               
                                            </div>
											
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>


            <?php elseif ($key === 'translations_editors'): ?>
                <!-- Translations Editors -->
                <div class="col-6 translations-editors">
                    <div class="card border-0">
                        <div class="card-body p-0">
                            <h2 class="card-title fs-16 text-uppercase fw-bold mb-3 let-spac-1 mh-44"><?php echo esc_html($label); ?></h2>
                            <div class="row">
                                <?php 
                                $members = get_editorial_members($key);
                                if ($members):
                                    foreach ($members as $member):
                                        $email = get_field('editorial_member_email', $member->ID);
                                        $bio = apply_filters('the_content', $member->post_content);
                                        $modal_id = 'modal_' . $member->ID;
                                ?>
                                    <div class="col-md-12 mb-3">
                                        <h3 class="mb-0 fs-18">
                                            <a href="#" data-bs-toggle="modal" data-bs-target="#<?php echo esc_attr($modal_id); ?>">
                                                <span><?php echo esc_html($member->post_title); ?></span>
												<i class="fa-solid fa-info-circle"></i>
                                            </a>
                                        </h3>
                                        <?php if ($email): ?>
                                            <a class="mb-2 d-block" href="mailto:<?php echo esc_attr($email); ?>" target="_blank"><span><?php echo esc_html($email); ?></span></a>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Modal -->
                                    <div class="modal fade" id="<?php echo esc_attr($modal_id); ?>" tabindex="-1" aria-labelledby="<?php echo esc_attr($modal_id); ?>Label" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-scrollable modal-lg modal-dialog-centered">
                                         <div class="modal-content apu-editorteam-model">
											
                                            <div class="model-body">
                                             <!-- <div class="flex-basis-32">
											<img src="https://placehold.co/600x400/cccccc/1f4d7c?text=translations editor" alt="<?php echo esc_attr( t('iw_author_placeholder') ); ?>
" class="rounded-1 objectfit-cover" width="220" height="220">
												</div> -->
												<div class="model-head flex-basis-68">
                                                <h5 class="modal-title mb-0" id="<?php echo esc_attr($modal_id); ?>Label"><span><?php echo esc_html($member->post_title); ?></span></h5>
												<p class="model-authoremail"><a href="mailto:<?php echo esc_attr($email); ?>"><span><?php echo esc_html($email); ?></span></a></p>	
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?php echo esc_attr( t('iw_close') ); ?>
"></button>
												 <?php echo $bio; ?>
												
                                            </div>
                                               
                                            </div>
											
                                        </div>
                                        </div>
                                    </div>
                                <?php endforeach; endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                
            

            <?php endif; ?>

        <?php endforeach; ?>
    </div>
</div>