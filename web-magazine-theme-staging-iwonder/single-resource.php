<?php get_header(); 

get_template_part('template-parts/main-wrapper-start');

?>

 <article class="apum-innerpage-body pb-0">

             
				 <?php while (have_posts()) : the_post(); ?>
                <div class="maxw-720 content-area">

                    <!-- Title Area -->
                    <div class="title-area add-motif mb-4">
                        <?php custom_breadcrumb(); ?>
                        <h1 class="entry-title"><?php the_title(); ?></h1>
                    </div>

                    <!-- Post Meta -->
                    <div class="d-flex flex-column flex-md-row gap-3 mb-4 align-items-star align-items-md-center category-badge-issue-parent">
                        <?php 
                        $terms = get_the_terms(get_the_ID(), 'resource_type'); 
                        if (!empty($terms) && !is_wp_error($terms)) {
                            echo '<a href="' . esc_url(get_term_link($terms[0])) . '" class="badge ' . esc_attr(get_resource_category_class()) . '">' . esc_html($terms[0]->name) . '</a>';
                        }
                        ?>
                        
                        <!-- Post Meta -->    
                        <div class="d-flex align-items-baseline">
                            <?php display_magazine_categories(); ?>
                            <?php 
                            ob_start();
                            display_magazine_categories();
                            $categories = ob_get_clean();

                            // Remove unwanted extra content if necessary
                            $categories = trim($categories); // You can further manipulate this if needed
                            if (!empty($categories)) :
                            ?>
                                <span class="mx-2">•</span>
                            <?php endif; ?>
                            <a class="publishdate"><?php echo get_the_date('j, M Y'); ?></a>
                        </div>
                    </div>
                    
                    <!-- Resource Info -->
                    <div class="entry-meta mb-4">
                        <?php
                        $resource_type = get_post_meta(get_the_ID(), '_resource_type', true);
                        $resource_link = get_post_meta(get_the_ID(), '_resource_link', true);

                        if ($resource_type) {
                            echo '<p><strong>Resource Type:</strong> ' . esc_html($resource_type) . '</p>';
                        }
                        if ($resource_link) {
                            echo '<p><strong>Resource Link:</strong> <a href="' . esc_url($resource_link) . '" target="_blank">View Resource</a></p>';
                        }
                        ?>
                    </div>
                    
                    <!-- Get the 'Short Note' field value -->
                    <div class="entry-content my-5">
                        <?php 
                        // Get the 'Short Note' field value
                        $short_note = get_field('resource_short_note');
                        // Check if the field has a value before displaying
                        if ($short_note) : ?>
                            <div class="short-note hide-sharedaddy">
                                <?php echo do_shortcode('[acf_with_latex field="resource_short_note"]');?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    	<?php get_template_part('template-parts/section', 'post-author'); ?>

                    <!-- Share and Download -->
         <?php get_template_part('template-parts/share-download'); ?>
                    
                    <?php
                    $files = [];

                    if (have_rows('upload_files')) {
                        // Collect all PDFs
                        while (have_rows('upload_files')) {
                            the_row();
                            $pdf = get_sub_field('upload_file');
                            if ($pdf && isset($pdf['url'])) {
                                $files[] = $pdf;
                            }
                        }

                        // Reset rows
                        reset_rows();

                        // Determine flex class
                        $flex_class = count($files) > 3 ? 'flex-column' : 'flex-wrap';

                        echo '<div id="pdf-downloads" class="d-flex gap-2 ' . esc_attr($flex_class) . ' mb-3">';

                        // Show buttons
                        $count = 1;
                        foreach ($files as $pdf) {
                            $pdf_url = esc_url($pdf['url']);
                            $label = count($files) === 1 ? 'Download PDF' : 'Download PDF' . $count;

                            echo '<a href="' . $pdf_url . '" class="btn btn-light pdf-btn d-flex align-items-center gap-2" download target="_blank">';
                            echo '<i class="fas fa-file-download"></i> ' . esc_html($label);
                            echo '</a>';

                            $count++;
                        }

                        echo '</div>';

                        // "Download All" if more than 1 file
                        if (count($files) > 1) {
                            echo '<button id="download-all-btn" class="btn btn-primary"><i class="fas fa-download"></i> Download All</button>';
                        }
                    }
                    ?>
                    <script>
                    document.addEventListener("DOMContentLoaded", function () {
                        const downloadAllBtn = document.getElementById("download-all-btn");
                        const pdfButtons = document.querySelectorAll(".pdf-btn");

                        if (downloadAllBtn) {
                            downloadAllBtn.addEventListener("click", function () {
                                pdfButtons.forEach((btn) => {
                                    const link = document.createElement("a");
                                    link.href = btn.getAttribute("href");
                                    link.download = '';
                                    link.target = "_blank";
                                    document.body.appendChild(link);
                                    link.click();
                                    document.body.removeChild(link);
                                });
                            });
                        }
                    });
                    </script>
 </div> <!-- /.maxw-720 -->
         
			<?php get_template_part('template-parts/section', 'post-feature-image'); ?>

              <?php endwhile; ?> 

                </article>


	<?php
get_template_part('template-parts/main-wrapper-end');
?>

<?php get_template_part('template-parts/section', 'post-related-articles'); ?>

<!-- Info card section -->
<?php get_template_part('template-parts/section', 'info-cards'); ?>
<!-- </main> -->

<?php get_footer(); ?>